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

function getEpisodeFiles($uploadDir)
{
    global $config;

    // Get supported file extensions
    $supported_extensions = array();
    $supported_extensions_xml = simplexml_load_file($config['absoluteurl'] . 'components/supported_media/supported_media.xml');
    foreach ($supported_extensions_xml->mediaFile as $item) {
        array_push($supported_extensions, strval($item->extension));
    }

    // Get episodes ordered by pub date
    $files = array();
    if ($handle = opendir($uploadDir)) {
        while (false !== ($entry = readdir($handle))) {
            // Sort out all files which have no XML file
            if (!file_exists($uploadDir . pathinfo($config['upload_dir'] . $entry, PATHINFO_FILENAME) . '.xml')) {
                continue;
            }
            // Sort out all files with invalid file extensions
            if (in_array(pathinfo($uploadDir . $entry, PATHINFO_EXTENSION), $supported_extensions)) {
                array_push($files, [
                    'filename' => $entry,
                    'lastModified' => filemtime($uploadDir . $entry)
                ]);
            }
        }
    }

    do {
        $swapped = false;
        for ($i = 0, $c = count($files) - 1; $i < $c; $i++) {
            if ($files[$i]['lastModified'] < $files[$i + 1]['lastModified']) {
                list($files[$i + 1], $files[$i]) = array($files[$i], $files[$i + 1]);
                $swapped = true;
            }
        }
    } while ($swapped);

    // Pop files from the future
    $realfiles = array();
    for ($i = 0; $i < count($files); $i++) {
        if (time() > $files[$i]['lastModified']) {
            array_push($realfiles, $files[$i]);
        }
    }

    return $realfiles;
}

function generateRssItem($file, $uploadDir, $uploadUrl, $imagesDir, $imagesUrl)
{
    global $config;

    $link = str_replace('?', '', $config['link']);
    $link = str_replace('=', '', $link);
    $link = str_replace('$url', '', $link);
    $original_full_filepath = $uploadUrl . str_replace(' ', '%20', $file['filename']);
    $data = simplexml_load_file($uploadDir . pathinfo($file['filename'], PATHINFO_FILENAME) . '.xml');

    // Skip files with no read permission
    $mimetype = getmime($uploadDir . $file['filename']);
    if (!$mimetype) {
        $mimetype = null;
    }

    $author = null;
    if (!empty($data->episode->authorPG->emailPG)) {
        $author = $data->episode->authorPG->emailPG;
        if (!empty($data->episode->authorPG->namePG)) {
            $author .= ' (' . $data->episode->authorPG->namePG . ')';
        }
    } else {
        $author = $config['author_email'] . ' (' . $config['author_name'] . ')';
    }

    // Get lines of custom tags
    $customTags = isset($data->episode->customTagsPG)
        ? preg_split("/\r\n|\n|\r/", $data->episode->customTagsPG)
        : array();

    // Generate GUID if a pregenerated GUID is missing for the episode
    $guid = isset($data->episode->guid) ? $data->episode->guid : $config['url'] . "?" . $link . "=" . $file['filename'];
    // Check if this episode has a cover art
    $basename = pathinfo($uploadDir . $file['filename'], PATHINFO_FILENAME);
    $has_cover = false;
    if (!empty($data->episode->imgPG)) {
        $has_cover = $data->episode->imgPG;
    } elseif (file_exists($imagesDir . $basename . '.jpg') || file_exists($imagesDir . $basename . '.png')) {
        $ext = file_exists($imagesDir . $basename . '.png') ? '.png' : '.jpg';
        $has_cover = $imagesUrl . $basename . $ext;
    }

    $indent = "\t\t\t";
    $linebreak = "\n";

    $item = '
    <item>' . "\n";
    $item .= $indent . '<title>' . $data->episode->titlePG . '</title>' . $linebreak;

    if (!empty($data->episode->episodeNumPG)) {
        $item .= $indent . '<itunes:episode>' . $data->episode->episodeNumPG . '</itunes:episode>' . $linebreak;
        $item .= $indent . '<podcast:episode>' . $data->episode->episodeNumPG . '</podcast:episode>' . $linebreak;
    }
    if (!empty($data->episode->seasonNumPG)) {
        $item .= $indent . '<itunes:season>' . $data->episode->seasonNumPG . '</itunes:season>' . $linebreak;
        $item .= $indent . '<podcast:season>' . $data->episode->seasonNumPG . '</podcast:season>' . $linebreak;
    }

    $item .= $indent . '<itunes:subtitle><![CDATA[' . $data->episode->shortdescPG . ']]></itunes:subtitle>' . $linebreak;
    $item .= $indent . '<description><![CDATA[' . $data->episode->shortdescPG . ']]></description>' . $linebreak;
    if ($data->episode->longdescPG != "") {
        $item .= $indent . '<itunes:summary><![CDATA[' . $data->episode->longdescPG . ']]></itunes:summary>' . $linebreak;
    }

    $item .= $indent . '<link>' . $config['url'] . '?' . $link . '=' . $file['filename'] . '</link>' . $linebreak;
    $item .= $indent . '<enclosure url="' . $original_full_filepath . '" length="' . filesize($uploadDir . $file['filename']) . '" type="' . $mimetype . '"></enclosure>' . $linebreak;
    $item .= $indent . '<guid>' . $guid . '</guid>' . $linebreak;
    $item .= $indent . '<itunes:duration>' . $data->episode->fileInfoPG->duration . '</itunes:duration>' . $linebreak;

    $item .= $indent . '<author>' . htmlspecialchars($author) . '</author>' . $linebreak;
    if (!empty($data->episode->authorPG->namePG)) {
        $item .= $indent . '<itunes:author>' . htmlspecialchars($data->episode->authorPG->namePG) . '</itunes:author>' . $linebreak;
    } else {
        $item .= $indent . '<itunes:author>' . $config['author_name'] . '</itunes:author>' . $linebreak;
    }

    if ($data->episode->keywordsPG != "") {
        $item .= $indent . '<itunes:keywords>' . $data->episode->keywordsPG . '</itunes:keywords>' . $linebreak;
    }
    $item .= $indent . '<itunes:explicit>' . $data->episode->explicitPG . '</itunes:explicit>' . $linebreak;

    // If image is set
    if ($has_cover) {
        $item .= $indent . '<itunes:image href="' . $has_cover . '" />' . $linebreak;
    }

    $item .= $indent . '<pubDate>' . date("r", $file['lastModified']) . '</pubDate>' . $linebreak;

    foreach ($customTags as $line) {
        $item .= $indent . $line . $linebreak;
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
    <rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0" xml:lang="' . $config['feed_language'] . '" version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:podcast="https://podcastindex.org/namespace/1.0">
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

    $custom_tags = getCustomFeedTags();
    if ($custom_tags != '') {
        foreach (preg_split("/\r\n|\n|\r/", $custom_tags) as $line) {
            $feedhead .= '		' . $line . "\n";
        }
    }

    // Get ordered episodes
    $files = getEpisodeFiles($uploadDir);

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
