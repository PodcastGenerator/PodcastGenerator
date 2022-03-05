<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

function itunes_category($categoryName)
{
    $cat_seg = explode(':', $categoryName, 2);
    if (count($cat_seg) > 1) {
        $output  = '		<itunes:category text="' . htmlspecialchars($cat_seg[0]) . '">' . "\n";
        $output .= '			<itunes:category text="' . htmlspecialchars($cat_seg[1]) . '"/>' . "\n";
        $output .= '		</itunes:category>' . "\n";
    } else {
        $output = '		<itunes:category text="' . htmlspecialchars($categoryName) . '"/>' . "\n";
    }
    return $output;
}

function generateRssItem($file, $uploadDir, $uploadUrl, $imagesDir, $imagesUrl)
{
    global $config;

    $link = str_replace('?', '', $config['link']);
    $link = str_replace('=', '', $link);
    $link = str_replace('$url', '', $link);

    // encode special characters in file name
    $encodedFilename = str_replace('+', '%20', urlencode($file['filename']));
    $enclosureUrl = $uploadUrl . $encodedFilename;

    // Skip files with no read permission
    $mimetype = getmime($uploadDir . $file['filename']);
    if (!$mimetype) {
        $mimetype = null;
    }

    $author = null;
    if (!empty($file['data']->episode->authorPG->emailPG)) {
        $author = $file['data']->episode->authorPG->emailPG;
        if (!empty($file['data']->episode->authorPG->namePG)) {
            $author .= ' (' . $file['data']->episode->authorPG->namePG . ')';
        }
    } else {
        $author = $config['author_email'] . ' (' . $config['author_name'] . ')';
    }

    // Get lines of custom tags
    $customTags = isset($file['data']->episode->customTagsPG)
        ? preg_split("/\r\n|\n|\r/", $file['data']->episode->customTagsPG)
        : array();

    // Generate GUID if a pregenerated GUID is missing for the episode
    $guid = isset($file['data']->episode->guid)
        ? $file['data']->episode->guid
        : $config['url'] . "?" . $link . "=" . $file['filename'];

    // Check if this episode has a cover art
    $basename = pathinfo($file['filename'], PATHINFO_FILENAME);
    $has_cover = false;
    if (!empty($file['data']->episode->imgPG)) {
        $has_cover = $file['data']->episode->imgPG;
    } elseif (file_exists($imagesDir . $basename . '.jpg') || file_exists($imagesDir . $basename . '.png')) {
        $ext = file_exists($imagesDir . $basename . '.png') ? '.png' : '.jpg';
        $has_cover = $imagesUrl . $basename . $ext;
    }

    $TAB = "\t\t\t";
    $LR = "\n";

    $item = '
    <item>' . "\n";
    $item .= $TAB . '<title>' . $file['data']->episode->titlePG . '</title>' . $LR;

    if (!empty($file['data']->episode->episodeNumPG)) {
        $item .= $TAB . '<itunes:episode>' . $file['data']->episode->episodeNumPG . '</itunes:episode>' . $LR;
        $item .= $TAB . '<podcast:episode>' . $file['data']->episode->episodeNumPG . '</podcast:episode>' . $LR;
    }
    if (!empty($file['data']->episode->seasonNumPG)) {
        $item .= $TAB . '<itunes:season>' . $file['data']->episode->seasonNumPG . '</itunes:season>' . $LR;
        $item .= $TAB . '<podcast:season>' . $file['data']->episode->seasonNumPG . '</podcast:season>' . $LR;
    }

    $item .= $TAB . '<itunes:subtitle><![CDATA[' . $file['data']->episode->shortdescPG . ']]></itunes:subtitle>' . $LR;
    $item .= $TAB . '<description><![CDATA[' . $file['data']->episode->shortdescPG . ']]></description>' . $LR;
    if ($file['data']->episode->longdescPG != "") {
        $item .= $TAB . '<itunes:summary><![CDATA[' . $file['data']->episode->longdescPG . ']]></itunes:summary>' . $LR;
    }

    $item .= $TAB . '<link>' . $config['url'] . '?' . $link . '=' . $encodedFilename . '</link>' . $LR;
    $item .= $TAB . '<enclosure url="' . htmlspecialchars($enclosureUrl) . '" length="'
        . filesize($uploadDir . $file['filename']) . '" type="' . $mimetype . '"></enclosure>' . $LR;
    $item .= $TAB . '<guid>' . htmlspecialchars($guid) . '</guid>' . $LR;
    $item .= $TAB . '<itunes:duration>' . $file['data']->episode->fileInfoPG->duration . '</itunes:duration>' . $LR;

    $item .= $TAB . '<author>' . htmlspecialchars($author) . '</author>' . $LR;
    if (!empty($file['data']->episode->authorPG->namePG)) {
        $item .= $TAB . '<itunes:author>' . htmlspecialchars($file['data']->episode->authorPG->namePG)
            . '</itunes:author>' . $LR;
    } else {
        $item .= $TAB . '<itunes:author>' . $config['author_name'] . '</itunes:author>' . $LR;
    }

    if ($file['data']->episode->keywordsPG != "") {
        $item .= $TAB . '<itunes:keywords>' . $file['data']->episode->keywordsPG . '</itunes:keywords>' . $LR;
    }
    $item .= $TAB . '<itunes:explicit>' . $file['data']->episode->explicitPG . '</itunes:explicit>' . $LR;

    // If image is set
    if ($has_cover) {
        $item .= $TAB . '<itunes:image href="' . $has_cover . '" />' . $LR;
    }

    $item .= $TAB . '<pubDate>' . date("r", $file['lastModified']) . '</pubDate>' . $LR;

    foreach ($customTags as $line) {
        $item .= $TAB . $line . $LR;
    }

    $item .= "\t\t</item>\n";
    return $item;
}

function generateRSS()
{
    // Make variables available in this scope
    global $config, $version;

    $feedDir = $config['absoluteurl'] . $config['feed_dir'];

    // We use the media directory a lot, and possibly also the images directory
    // Stick them in variables instead of concatenating all the time
    $uploadDir = $config['absoluteurl'] . $config['upload_dir'];
    $uploadUrl = $config['url'] . $config['upload_dir'];

    $imagesDir = $config['absoluteurl'] . $config['img_dir'];
    $imagesUrl = $config['url'] . $config['img_dir'];

    // Create path if it doesn't exist
    if (!is_dir($feedDir)) {
        mkdir($feedDir);
    }

    $podcastCoverUrl = $imagesUrl . $config['podcast_cover'];

    // Set the feed header with relevant podcast informations
    $feedhead = '<?xml version="1.0" encoding="' . $config['feed_encoding'] . '"?>
<!-- generator="Podcast Generator ' . $version . '" -->
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"
     xml:lang="' . $config['feed_language'] . '"
     version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:podcast="https://podcastindex.org/namespace/1.0">
	<channel>
		<title>' . htmlspecialchars($config['podcast_title']) . '</title>
		<link>' . $config['url'] . '</link>
		<atom:link href="' . $config['url'] . 'feed.xml" rel="self" type="application/rss+xml" />
		<description>' . htmlspecialchars($config['podcast_description']) . '</description>
		<generator>Podcast Generator ' . $version . ' - http://www.podcastgenerator.net</generator>
		<lastBuildDate>' . date('r') . '</lastBuildDate>
		<language>' . $config['feed_language'] . '</language>
		<copyright>' . htmlspecialchars($config['copyright']) . '</copyright>
		<managingEditor>' . htmlspecialchars($config['author_email']) . '</managingEditor>
		<webMaster>' . htmlspecialchars($config['webmaster']) . '</webMaster>
		<itunes:image href="' . $podcastCoverUrl . '" />
		<image>
			<url>' . $podcastCoverUrl . '</url>
			<title>' . htmlspecialchars($config['podcast_title']) . '</title>
			<link>' . $config['url'] . '</link>
		</image>
		<itunes:summary>' . htmlspecialchars($config['podcast_description']) . '</itunes:summary>
		<itunes:subtitle>' . htmlspecialchars($config['podcast_subtitle']) . '</itunes:subtitle>
		<itunes:author>' . htmlspecialchars($config['author_name']) . '</itunes:author>
		<itunes:owner>
			<itunes:name>' . htmlspecialchars($config['author_name']) . '</itunes:name>
			<itunes:email>' . htmlspecialchars($config['author_email']) . '</itunes:email>
        </itunes:owner>
        <itunes:explicit>' . $config['explicit_podcast'] . '</itunes:explicit>' . "\n";

    $feedhead .= itunes_category($config['itunes_category[0]']);
    if ($config['itunes_category[1]'] != '' || $config['itunes_category[1]'] == 'null') {
        $feedhead .= itunes_category($config['itunes_category[1]']);
    }
    if ($config['itunes_category[2]'] != '' || $config['itunes_category[1]'] == 'null') {
        $feedhead .= itunes_category($config['itunes_category[2]']);
    }

    if ($config['websub_server'] != '') {
        $feedhead .= '		<atom:link href="' . $config['websub_server'] . '" rel="hub" />' . "\n";
    }

    if ($config['feed_locked'] != '') {
        $feedhead .= '		<podcast:locked owner="' . htmlspecialchars($config['author_email']) . '">'
            . $config['feed_locked'] . '</podcast:locked>' . "\n";
    }

    $custom_tags = getCustomFeedTags();
    if ($custom_tags != '') {
        foreach (preg_split("/\r\n|\n|\r/", $custom_tags) as $line) {
            $feedhead .= '		' . $line . "\n";
        }
    }

    // Get ordered episodes
    $files = getEpisodeFiles($config);

    // Set a maximum amount of episodes generated in the feed
    $maxEpisodes = count($files);
    if (strtolower($config['recent_episode_in_feed']) != 'all') {
        $maxEpisodes = intval($config['recent_episode_in_feed']);
    }

    // Items (Episodes) in XML
    $items = array();
    for ($i = 0; $i < $maxEpisodes; $i++) {
        $item = generateRssItem($files[$i], $uploadDir, $uploadUrl, $imagesDir, $imagesUrl);
        // Push XML to the real XML
        array_push($items, $item);
    }

    // Close the tags
    $feedfooter = '
    </channel>
    </rss>' . "\n";

    // Generate the actual XML
    $xml = $feedhead;
    for ($i = 0; $i < count($items); $i++) {
        $xml .= $items[$i];
    }

    // Append footer
    $xml .= $feedfooter;
    return file_put_contents($feedDir . 'feed.xml', $xml);
}
