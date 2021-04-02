<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
function setupEpisodes($_config)
{
    $supported_extensions = simplexml_load_file($_config['absoluteurl'] . 'components/supported_media/supported_media.xml');
    $realsupported_extensions = array();
    foreach ($supported_extensions as $item) {
        array_push($realsupported_extensions, $item->extension);
    }
    $supported_extensions = $realsupported_extensions;
    unset($realsupported_extensions);

    // Get episodes names and pubDates (which are the file
    // modification times).  We'll ignore files with future
    // timestamps.
    $now_time = time();
    $episodes_mtimes = array();
    if ($handle = opendir($_config['absoluteurl'] . $_config['upload_dir'])) {
        while (false !== ($entry = readdir($handle))) {
            // If the file is a 'real' file, has a linked XML file,
            // and isn't from the future, add its name and
            // modification time to our array.
            $this_entry = $_config['absoluteurl'] . $_config['upload_dir'] . $entry;
            $this_mtime = filemtime($this_entry);
            if (in_array(pathinfo($this_entry, PATHINFO_EXTENSION), $supported_extensions)
                && file_exists($_config['absoluteurl'] . $_config['upload_dir'] . pathinfo($this_entry, PATHINFO_FILENAME) . '.xml')
                && ($this_mtime <= $now_time || isset($_SESSION['username']))) {
                array_push($episodes_mtimes, [$entry, $this_mtime]);
            }
        }
    }

    // Sort entries according to their pubDates.
    usort($episodes_mtimes, 'compare_mtimes');
    return $episodes_mtimes;
}

function arrayEpisode($item, $episode, $_config)
{
    $append_array = [
        'episode' => [
            'guid' => $item->guid,
            'titlePG' => $item->titlePG,
            'shortdescPG' => $item->shortdescPG,
            'longdescPG' => $item->longdescPG,
            'imgPG' => $item->imgPG,
            'categoriesPG' => [
                'category1PG' => $item->categoriesPG->category1PG,
                'category2PG' => $item->categoriesPG->category2PG,
                'category3PG' => $item->categoriesPG->category3PG
            ],
            'keywordsPG' => $item->keywordsPG,
            'explicitPG' => $item->explicitPG,
            'authorPG' => [
                'namePG' => $item->authorPG->namePG,
                'emailPG' => $item->authorPG->emailPG
            ],
            'fileInfoPG' => [
                'size' => $item->fileInfoPG->size,
                'duration' => $item->fileInfoPG->duration,
                'bitrate' => $item->fileInfoPG->bitrate,
                'frequency' => $item->fileInfoPG->frequency
            ],
            'filename' => $episode,
            'fileid' => pathinfo($_config['absoluteurl'] . $_config['upload_dir'] . $episode, PATHINFO_FILENAME),
            'moddate' => date('Y-m-d', filemtime($_config['absoluteurl'] . $_config['upload_dir'] . $episode))
        ]
    ];
    return $append_array;
}

function getEpisodes($category = null, $_config)
{
    $episodes_mtimes = setupEpisodes($_config);
    // Get XML data for the episodes of interest.
    $episodes_data = array();
    for ($i = 0; $i < sizeof($episodes_mtimes); $i++) {
        $episode = $episodes_mtimes[$i][0];
        // We need to get the CDATA in plaintext.
        $xml_file_name = pathinfo($_config['absoluteurl'] . $_config['upload_dir'] . $episode, PATHINFO_FILENAME) . '.xml';
        $xml = simplexml_load_file($_config['absoluteurl'] . $_config['upload_dir'] . $xml_file_name, null, LIBXML_NOCDATA);
        foreach ($xml as $item) {
            // If we are filtering by category, we can omit episodes
            // that lack the desired category.
            if ($category != null && $category != 'all') {
                if ($item->categoriesPG->category1PG != $category
                    && $item->categoriesPG->category2PG != $category
                    && $item->categoriesPG->category3PG != $category) {
                    continue;
                }
            }
            array_push($episodes_data, arrayEpisode($item, $episode, $_config));
        }
    }
    unset($_config);
    return $episodes_data;
}

function searchEpisodes($name = "", $_config)
{
    $name = strtolower($name);
    $episodes_mtimes = setupEpisodes($_config);
    // Check if name is a category and replace
    $cats_xml = simplexml_load_file('categories.xml');
    foreach ($cats_xml as $item) {
        if ($name === strtolower($item->description)) {
            $name = strval($item->id);
        }
    }
    // Get XML data for the episodes of interest.
    $episodes_data = array();
    for ($i = 0; $i < sizeof($episodes_mtimes); $i++) {
        $episode = $episodes_mtimes[$i][0];
        // We need to get the CDATA in plaintext.
        $xml_file_name = pathinfo($_config['absoluteurl'] . $_config['upload_dir'] . $episode, PATHINFO_FILENAME) . '.xml';
        $xml = simplexml_load_file($_config['absoluteurl'] . $_config['upload_dir'] . $xml_file_name, null, LIBXML_NOCDATA);
        foreach ($xml as $item) {
            if (strpos(strtolower($item->titlePG), $name) === false
                && strpos(strtolower($item->shortdescPG), $name) === false
                && strpos(strtolower($item->longdescPG), $name) === false
                && strpos(strtolower($item->categoriesPG->category1PG), $name) === false
                && strpos(strtolower($item->categoriesPG->category2PG), $name) === false
                && strpos(strtolower($item->categoriesPG->category3PG), $name) === false
                && strpos(strtolower($item->keywordsPG), $name) === false
                && strpos(strtolower($item->authorPG->namePG), $name) === false) {
                continue;
            }
            array_push($episodes_data, arrayEpisode($item, $episode, $_config));
        }
    }
    unset($_config);
    return $episodes_data;
}

// Fetch ID3 tags. Try ID3V2, then ID3V1, before falling back
// to the specific default value.
function getID3Tag($fileinfo, $tagName, $defaultValue = null)
{
    if(isset($fileinfo['tags']['id3v2'][$tagName][0]) &&
            $fileinfo['tags']['id3v2'][$tagName][0])
        return $fileinfo['tags']['id3v2'][$tagName][0];
    else
    {
        if(isset($fileinfo['tags']['id3v1'][$tagName][0]) &&
                $fileinfo['tags']['id3v1'][$tagName][0])
            return $fileinfo['tags']['id3v1'][$tagName][0];
        else
            return $defaultValue;
    }
}

function indexEpisodes($_config)
{
    $new_files = array();
    $mimetypes = simplexml_load_file($_config['absoluteurl'] . 'components/supported_media/supported_media.xml');
    // Get all files and check if they have an XML file associated
    if ($handle = opendir($_config['absoluteurl'] . $_config['upload_dir'])) {
        while (false !== ($entry = readdir($handle))) {
            // Skip dotfiles
            if (substr($entry, 0, 1) == '.') {
                continue;
            }
            // Skip XML files
            if (pathinfo($_config['absoluteurl'] . $_config['upload_dir'] . $entry, PATHINFO_EXTENSION) == 'xml') {
                continue;
            }
            // Check if an XML file for that episode exists
            if (file_exists($_config['absoluteurl'] . $_config['upload_dir'] . pathinfo($_config['absoluteurl'] . $_config['upload_dir'] . $entry, PATHINFO_FILENAME) . '.xml')) {
                continue;
            }

            // Get mime type
            $mimetype = getmime($_config['absoluteurl'] . $_config['upload_dir'] . $entry);

            // Continue if file isn't readable
            if (!$mimetype)
                continue;

            // Skip invalid mime types
            $validExtension = false;
            foreach ($mimetypes->mediaFile as $item) {
                if ($mimetype == $item->mimetype) {
                    $validExtension = true;
                    break;
                }
            }
            if (!$validExtension) {
                continue;
            }
            array_push($new_files, $entry);
        }
    }
    require_once $_config['absoluteurl'] . 'components/getid3/getid3.php';

    // Generate XML from audio file (with mostly empty values)
    $num_added = 0;
    for ($i = 0; $i < sizeof($new_files); $i++) {
        // Skip files if they are not strictly named
        if ($_config['strictfilenamepolicy'] == 'yes') {
            if (!preg_match('/^[\w.]+$/', $new_files[$i])) {
                continue;
            }
        }
        // Select new filenames (with date) if not already exists
        preg_match('/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/', $new_files[$i], $output_array);
        $fname = $new_files[$i];
        if (sizeof($output_array) == 0) {
            $new_filename = $_config['absoluteurl'] . $_config['upload_dir'] . date('Y-m-d') . '_' . $new_files[$i];
            $new_filename = str_replace(' ', '_', $new_filename);
            $appendix = 1;
            while (file_exists($new_filename)) {
                $new_filename = $_config['absoluteurl'] . $_config['upload_dir'] . date('Y-m-d') . '_' . $appendix . '_' . basename($new_files[$i]);
                $new_filename = str_replace(' ', '_', $new_filename);
                $appendix++;
            }
            $new_filename = strtolower($new_filename);
            rename($_config['absoluteurl'] . $_config['upload_dir'] . $new_files[$i], $new_filename);
            $fname = $new_filename;
        }
        // Get audio metadata (duration, bitrate etc)
        $getID3 = new getID3;
        $fileinfo = $getID3->analyze($_config['absoluteurl'] . $_config['upload_dir'] . $new_files[$i]);
        $duration = $fileinfo['playtime_string'];           // Get duration
        $bitrate = $fileinfo['audio']['bitrate'];           // Get bitrate
        $frequency = $fileinfo['audio']['sample_rate'];     // Frequency
        $title = getID3Tag($fileinfo, 'title', pathinfo($_config['absoluteurl'] . $_config['upload_dir'] . $new_files[$i], PATHINFO_FILENAME));
        $comment = getID3Tag($fileinfo, 'comment', $title);
        $author_name = getID3Tag($fileinfo, 'artist', '');

        $link = str_replace('?', '', $config['link']);
        $link = str_replace('=', '', $link);
        $link = str_replace('$url', '', $link);

        $episodefeed = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
        <episode>
            <guid>' . htmlspecialchars($config['url'] . "?" . $link . "=" . basename($fname)) . '</guid>
            <titlePG>' . htmlspecialchars($title, ENT_NOQUOTES) . '</titlePG>
            <shortdescPG><![CDATA[' . $comment . ']]></shortdescPG>
            <longdescPG><![CDATA[' . $comment . ']]></longdescPG>
            <imgPG></imgPG>
            <categoriesPG>
                <category1PG>uncategorized</category1PG>
                <category2PG></category2PG>
                <category3PG></category3PG>
            </categoriesPG>
            <keywordsPG></keywordsPG>
            <explicitPG>' . htmlspecialchars($_config['explicit_podcast']) . '</explicitPG>
            <authorPG>
                <namePG>'. $author_name .'</namePG>
                <emailPG></emailPG>
            </authorPG>
            <fileInfoPG>
                <size>' . intval(filesize($_config['absoluteurl'] . $_config['upload_dir'] . $new_files[$i]) / 1000 / 1000) . '</size>
                <duration>' . $duration . '</duration>
                <bitrate>' . substr(strval($bitrate), 0, 3) . '</bitrate>
                <frequency>' . $frequency . '</frequency>
            </fileInfoPG>
        </episode>
</PodcastGenerator>';
        // Write image if set
        if (isset($fileinfo['comments']['picture'])) {
            $imgext = ($fileinfo['comments']['picture'][0]['image_mime'] == 'image/png') ? 'png' : 'jpg';
            $img_filename = $_config['absoluteurl'] . $_config['img_dir'] . pathinfo($fname, PATHINFO_FILENAME) . '.' . $imgext;
            file_put_contents($img_filename, $fileinfo['comments']['picture'][0]['data']);
        }
        // Write XML file
        file_put_contents($_config['absoluteurl'] . $_config['upload_dir'] . pathinfo($fname, PATHINFO_FILENAME) . '.xml', $episodefeed);
        $num_added++;
    }
    return $num_added;
}

// usort() compare function that reverse-sorts numeric values (which,
// in our case, are file modification times.
function compare_mtimes($a, $b)
{
    return $b[1] - $a[1];
}
