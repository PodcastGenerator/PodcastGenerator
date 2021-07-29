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

function generateRSS()
{
    // Make variables available in this scope
    global $config, $version;
    // Create path if it doesn't exist
    if (!is_dir($config['absoluteurl'] . $config['feed_dir'])) {
        mkdir($config['absoluteurl'] . $config['feed_dir']);
    }
    // Set the feed header with relevant podcast informations
    $feedhead = '<?xml version="1.0" encoding="' . $config['feed_encoding'] . '"?>
    <!-- generator="Podcast Generator ' . $version . '" -->
    <rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0" xml:lang="' . $config['feed_language'] . '" version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>' . htmlspecialchars($config['podcast_title']) . '</title>
		<link>' . $config['url'] . '</link>
		<atom:link href="' . $config['url'] . 'feed.xml" rel="self" type="application/rss+xml" />
		<description>' . htmlspecialchars($config['podcast_description']) . '</description>
		<generator>Podcast Generator ' . $version . ' - http://www.podcastgenerator.net</generator>
		<lastBuildDate>' . date('r') . '</lastBuildDate>
		<language>' . substr($config['feed_language'], 0, 2) . '</language>
		<copyright>' . htmlspecialchars($config['copyright']) . '</copyright>
		<managingEditor>' . htmlspecialchars($config['author_email']) . '</managingEditor>
		<webMaster>' . htmlspecialchars($config['webmaster']) . '</webMaster>
		<itunes:image href="' . $config['url'] . $config['img_dir'] . 'itunes_image.jpg" />
		<image>
			<url>' . $config['url'] . $config['img_dir'] . 'itunes_image.jpg</url>
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
    // Get supported file extensions
    $supported_extensions = array();
    $supported_extensions_xml = simplexml_load_file($config['absoluteurl'] . 'components/supported_media/supported_media.xml');
    foreach ($supported_extensions_xml->mediaFile as $item) {
        array_push($supported_extensions, strval($item->extension));
    }
    // Get episodes ordered by pub date
    $files = array();
    if ($handle = opendir($config['absoluteurl'] . $config['upload_dir'])) {
        while (false !== ($entry = readdir($handle))) {
            // Sort out all files which have no XML file
            if (!file_exists($config['absoluteurl'] . $config['upload_dir'] . pathinfo($config['upload_dir'] . $entry, PATHINFO_FILENAME) . '.xml')) {
                continue;
            }
            // Sort out all files with invalid file extensions
            if (in_array(pathinfo($config['absoluteurl'] . $config['upload_dir'] . $entry, PATHINFO_EXTENSION), $supported_extensions)) {
                array_push($files, [
                    'filename' => $entry,
                    'lastModified' => filemtime($config['absoluteurl'] . $config['upload_dir'] . $entry)
                ]);
            }
        }
    }

    do {
        $swapped = false;
        for ($i = 0, $c = sizeof($files) - 1; $i < $c; $i++) {
            if ($files[$i]['lastModified'] < $files[$i + 1]['lastModified']) {
                list($files[$i + 1], $files[$i]) = array($files[$i], $files[$i + 1]);
                $swapped = true;
            }
        }
    } while ($swapped);
    // Pop files from the future
    $realfiles = array();
    for ($i = 0; $i < sizeof($files); $i++) {
        if (time() > $files[$i]['lastModified']) {
            array_push($realfiles, $files[$i]);
        }
    }
    $files = $realfiles;
    unset($realfiles);
    // Set a maximum amount of episodes generated in the feed
    $maxEpisodes = sizeof($files);
    if (strtolower($config['recent_episode_in_feed']) != 'all') {
        $maxEpisodes = intval($config['recent_episode_in_feed']);
    }
    // Items (Episodes) in XML
    $items = array();
    for ($i = 0; $i < $maxEpisodes; $i++) {
        $link = str_replace('?', '', $config['link']);
        $link = str_replace('=', '', $link);
        $link = str_replace('$url', '', $link);
        $original_full_filepath = $config['url'] . $config['upload_dir'] . str_replace(' ', '%20', $files[$i]['filename']);
        $file = simplexml_load_file($config['absoluteurl'] . $config['upload_dir'] . pathinfo($config['upload_dir'] . $files[$i]['filename'], PATHINFO_FILENAME) . '.xml');
        // Skip files with no read permission
        $mimetype = getmime($config['absoluteurl'] . $config['upload_dir'] . $files[$i]['filename']);
        if (!$mimetype) {
            $mimetype = null;
        }
        $author = null;
        if (!empty($file->episode->authorPG->emailPG)) {
            $author = $file->episode->authorPG->emailPG;
            if (!empty($file->episode->authorPG->namePG))
                $author .= ' (' . $file->episode->authorPG->namePG . ')';
        } else {
            $author = $config['author_email'] . ' (' . $config['author_name'] . ')';
        }
        // Generate GUID if a pregenerated GUID is missing for the episode
        $guid = isset($file->episode->guid) ? $file->episode->guid : $config['url'] . "?" . $link . "=" . $files[$i]['filename'];
        // Check if this episode has a cover art
        $basename = pathinfo($config['absoluteurl'] . $config['upload_dir'] . $files[$i]['filename'], PATHINFO_FILENAME);
        $has_cover = false;
        if (!empty($file->episode->imgPG))
            $has_cover = $file->episode->imgPG;
        elseif (file_exists($config['absoluteurl'] . $config['img_dir'] . $basename . '.jpg') || file_exists($config['absoluteurl'] . $config['img_dir'] . $basename . '.png')) {
            $ext = file_exists($config['absoluteurl'] . $config['img_dir'] . $basename . '.png') ? '.png' : '.jpg';
            $has_cover = $config['url'] . $config['img_dir'] . $basename . $ext;
        }
        $indent = "\t\t\t";
        $linebreak = "\n";
        $item = '
        <item>' . "\n";
        $item .= $indent . '<title>' . $file->episode->titlePG . '</title>' . $linebreak;
        $item .= $indent . '<itunes:subtitle><![CDATA[' . $file->episode->shortdescPG . ']]></itunes:subtitle>' . $linebreak;
        $item .= $indent . '<description><![CDATA[' . $file->episode->shortdescPG . ']]></description>' . $linebreak;
        if ($file->episode->longdescPG != "") {
            $item .= $indent . '<itunes:summary><![CDATA[' . $file->episode->longdescPG . ']]></itunes:summary>' . $linebreak;
        }
        $item .= $indent . '<link>' . $config['url'] . '?' . $link . '=' . $files[$i]['filename'] . '</link>' . $linebreak;
        $item .= $indent . '<enclosure url="' . $original_full_filepath . '" length="' . filesize($config['absoluteurl'] . $config['upload_dir'] . $files[$i]['filename']) . '" type="' . $mimetype . '"></enclosure>' . $linebreak;
        $item .= $indent . '<guid>' . $guid . '</guid>' . $linebreak;
        $item .= $indent . '<itunes:duration>' . $file->episode->fileInfoPG->duration . '</itunes:duration>' . $linebreak;
        $item .= $indent . '<author>' . htmlspecialchars($author) . '</author>' . $linebreak;
        if (!empty($file->episode->authorPG->namePG)) {
            $item .= $indent . '<itunes:author>' . htmlspecialchars($file->episode->authorPG->namePG) . '</itunes:author>' . $linebreak;
        } else {
            $item .= $indent . '<itunes:author>' . $config['author_name'] . '</itunes:author>' . $linebreak;
        }
        if ($file->episode->keywordsPG != "") {
            $item .= $indent . '<itunes:keywords>' . $file->episode->keywordsPG . '</itunes:keywords>' . $linebreak;
        }
        $item .= $indent . '<itunes:explicit>' . $file->episode->explicitPG . '</itunes:explicit>' . $linebreak;
        // If image is set
        if ($has_cover)
            $item .= $indent . '<itunes:image href="' . $has_cover . '" />' . $linebreak;
        $item .= $indent . '<pubDate>' . date("r", $files[$i]['lastModified']) . '</pubDate>' . $linebreak;
        $item .= "\t\t</item>\n";
        // Push XML to the real XML
        array_push($items, $item);
    }
    // Close the tags
    $feedfooter = '
    </channel>
    </rss>' . "\n";
    // Generate the actual XML
    $xml = $feedhead;
    for ($i = 0; $i < sizeof($items); $i++) {
        $xml .= $items[$i];
    }
    // Append footer
    $xml .= $feedfooter;
    return file_put_contents($config['absoluteurl'] . $config['feed_dir'] .  'feed.xml', $xml);
}
