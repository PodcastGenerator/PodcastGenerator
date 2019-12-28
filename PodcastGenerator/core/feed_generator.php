<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
function generateRSS()
{
    // Make variables available in this scope
    global $config, $version;
    // Create path if it doesn't exist
    if(!is_dir($config['absoluteurl'] . $config['feed_dir'])) {
        mkdir($config['absoluteurl'] . $config['feed_dir']);
    }
    // Set the feed header with relevant podcast informations
    $feedhead = '<?xml version="1.0" encoding="' . $config['feed_encoding'] . '"?>
    <!-- generator="Podcast Generator ' . $version . '" -->
    <rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xml:lang="' . $config['scriptlang'] . '" version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>' . $config['podcast_title'] . '</title>
		<link>' . $config['url'] . '</link>
		<atom:link href="' . $config['url'] . 'feed.xml" rel="self" type="application/rss+xml" />
		<description>' . $config['podcast_description'] . '</description>
		<generator>Podcast Generator ' . $version . ' - http://www.podcastgenerator.net</generator>
		<lastBuildDate>' . date('r') . '</lastBuildDate>
		<language>' . substr($config['feed_language'], 0, 2) . '</language>
		<copyright>' . $config['copyright'] . '</copyright>
		<itunes:image href="' . $config['url'] . $config['img_dir'] . 'itunes_image.jpg" />
		<image>
			<url>' . $config['url'] . $config['img_dir'] . 'itunes_image.jpg</url>
			<title>' . $config['podcast_title'] . '</title>
			<link>' . $config['url'] . '</link>
		</image>
		<itunes:summary>' . $config['podcast_description'] . '</itunes:summary>
		<itunes:subtitle>' . $config['podcast_subtitle'] . '</itunes:subtitle>
		<itunes:author>' . $config['author_name'] . '</itunes:author>
		<itunes:owner>
			<itunes:name>' . $config['author_name'] . '</itunes:name>
			<itunes:email>' . $config['author_email'] . '</itunes:email>
        	</itunes:owner>
        	<itunes:explicit>' . $config['explicit_podcast'] . '</itunes:explicit>
		<itunes:category text="' . $config['itunes_category[0]'] . '"></itunes:category>'."\n";
    if ($config['itunes_category[1]'] != '') {
        $feedhead .= '		<itunes:category text="' . $config['itunes_category[1]'] . '"></itunes:category>'."\n";
    }
    if ($config['itunes_category[2]'] != '') {
        $feedhead .= '		<itunes:category text="' . $config['itunes_category[2]'] . '"></itunes:category>'."\n";
    }
    // Get supported file extensions
    $supported_extensions = array();
    $supported_extensions_xml = simplexml_load_file($config['absoluteurl'] . 'components/supported_media/supported_media.xml');
    foreach ($supported_extensions_xml->mediaFile->extension as $item) {
        array_push($supported_extensions, $item);
    }
    // Get episodes ordered by pub date
    $files = array();
    if ($handle = opendir($config['absoluteurl'] . $config['upload_dir'])) {
        while (false !== ($entry = readdir($handle))) {
            // Sort out all files which have no XML file
            if(!file_exists($config['absoluteurl'] . $config['upload_dir'] . pathinfo($config['upload_dir'] . $entry, PATHINFO_FILENAME) . '.xml')) {
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
    if(strtolower($config['recent_episode_in_feed']) != 'all') {
        $maxEpisodes = intval($config['recent_episode_in_feed']);
    }
    // Items (Episodes) in XML
    $items = array();
    for ($i = 0; $i < $maxEpisodes; $i++) {
        $link = str_replace('?', '', $config['link']);
        $link = str_replace('=', '', $link);
        $link = str_replace('$url', '', $link);
        $original_full_filepath = $config['url'] . $config['upload_dir'] . $files[$i]['filename'];
        $file = simplexml_load_file($config['absoluteurl'] . $config['upload_dir'] . pathinfo($config['upload_dir'] . $files[$i]['filename'], PATHINFO_FILENAME) . '.xml');
        $item = '
		<item>
			<title><![CDATA[' . $file->episode->titlePG . ']]></title>
			<itunes:subtitle><![CDATA[' . $file->episode->shortdescPG . ']]></itunes:subtitle>
			<itunes:summary><![CDATA[' . $file->episode->longdescPG . ']]></itunes:summary>
			<description><![CDATA[' . $file->episode->shortdescPG . ']]></description>
			<link>' . $original_full_filepath . '</link>
			<enclosure url="' . $original_full_filepath . '" length="' . filesize($config['absoluteurl'] . $config['upload_dir'] . $files[$i]['filename']) . '" type="' . mime_content_type($config['absoluteurl'] . $config['upload_dir'] . $files[$i]['filename']) . '"></enclosure>
			<guid>' . $config['url'] . "?" . $link . "=" . $files[$i]['filename'] . '</guid>
			<itunes:duration>' . $file->episode->fileInfoPG->duration . '</itunes:duration>
			<author>' . $file->episode->authorPG->emailPG . ' (' . $file->episode->authorPG->namePG . ')' . '</author>
			<itunes:author>' . $file->episode->authorPG->namePG . '</itunes:author>
			<itunes:keywords><![CDATA[' . $file->episode->keywordsPG . ']]></itunes:keywords>
			<itunes:explicit>' . $file->episode->explicitPG . '</itunes:explicit>
			<pubDate>' . date("r", $files[$i]["lastModified"]) . '</pubDate>
        </item>'."\n";
        // Push XML to the real XML
        array_push($items, $item);
    }
    // Close the tags
    $feedfooter = '
    </channel>
    </rss>'."\n";
    // Generate the actual XML
    $xml = $feedhead;
    for ($i = 0; $i < sizeof($items); $i++) {
        $xml .= $items[$i];
    }
    // Append footer
    $xml .= $feedfooter;
    return file_put_contents($config['absoluteurl'] . $config['feed_dir'] .  'feed.xml', $xml);
}
