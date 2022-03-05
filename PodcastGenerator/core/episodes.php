<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

function getSupportedExtensions($config)
{
    $supported_extensions = array();
    $supported_extensions_xml = simplexml_load_file($config['absoluteurl'] . 'components/supported_media/supported_media.xml');
    foreach ($supported_extensions_xml->mediaFile as $item) {
        array_push($supported_extensions, strval($item->extension));
    }
    return $supported_extensions;
}

/**
 * usort() callback for sorting episodes by timestamp.
 *
 * @param mixed $episodeA
 * @param mixed $episodeB
 * @return int
 */
function sort_episodes_by_timestamp($episodeA, $episodeB)
{
    return $episodeA['lastModified'] - $episodeB['lastModified'];
}

/**
 * usort() callback for sorting episodes by season and episode.
 *
 * @param mixed $episodeA
 * @param mixed $episodeB
 * @return int
 */
function sort_episodes_by_season_and_episode($episodeA, $episodeB)
{
    function getValueOrDefault($val)
    {
        if (empty($val) || !is_numeric($val)) {
            return -1;
        }
        return $val + 0;
    }

    $seasonA = getValueOrDefault($episodeA['data']->episode->seasonNumPG);
    $seasonB = getValueOrDefault($episodeB['data']->episode->seasonNumPG);
    if ($seasonA != $seasonB) {
        return $seasonA - $seasonB;
    }

    $episodeA = getValueOrDefault($episodeA['data']->episode->episodeNumPG);
    $episodeB = getValueOrDefault($episodeB['data']->episode->episodeNumPG);
    if ($episodeA != $episodeB) {
        return $episodeA - $episodeB;
    }

    // fall back on timestamp if season and episode numbers match / aren't set
    return sort_episodes_by_timestamp($episodeA, $episodeB);
}

function getEpisodeFiles($uploadDir, $includeFuture = false)
{
    global $config;

    $now = time();
    $supported_extensions = getSupportedExtensions($config);

    // Load episode data
    $files = array();
    if ($handle = opendir($uploadDir)) {
        while (false !== ($entry = readdir($handle))) {
            $filePath = $uploadDir . $entry;

            $dataFile = $uploadDir . pathinfo($entry, PATHINFO_FILENAME) . '.xml';
            // if sidecar file doesn't exist, skip
            if (!file_exists($dataFile)) {
                continue;
            }

            if (!$includeFuture) {
                // if file exists in the future, skip
                $lastModified = filemtime($filePath);
                if ($now < $lastModified) {
                    continue;
                }
            }

            // if file doesn't have supported extension, skip
            if (!in_array(pathinfo($filePath, PATHINFO_EXTENSION), $supported_extensions)) {
                continue;
            }

            array_push($files, [
                'filename' => $entry,
                'path' => $filePath,
                'lastModified' => filemtime($filePath),
                'data' => simplexml_load_file($dataFile)
            ]);
        }
    }

    // sort episodes by the selected method
    $sortMethod = 'sort_episodes_by_' . (!empty($config['feed_sort']) ? $config['feed_sort'] : 'timestamp');
    usort($files, $sortMethod);

    // need to reverse the array since it's sorted ascending
    return array_reverse($files);
}

function setupEpisodes($_config)
{
    $supported_extensions = getSupportedExtensions($_config);
    $uploadDir = $_config['absoluteurl'] . $_config['upload_dir'];

    // Get episodes names and pubDates (which are the file
    // modification times).  We'll ignore files with future
    // timestamps.
    $now_time = time();
    $episodes_mtimes = array();
    if ($handle = opendir($uploadDir)) {
        while (false !== ($entry = readdir($handle))) {
            // If the file is a 'real' file, has a linked XML file,
            // and isn't from the future, add its name and
            // modification time to our array.
            $this_entry = $uploadDir . $entry;
            $this_mtime = filemtime($this_entry);
            if (
                in_array(pathinfo($this_entry, PATHINFO_EXTENSION), $supported_extensions)
                && file_exists($uploadDir . pathinfo($this_entry, PATHINFO_FILENAME) . '.xml')
                && ($this_mtime <= $now_time || isset($_SESSION['username']))
            ) {
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
    $filemtime = filemtime($_config['absoluteurl'] . $_config['upload_dir'] . $episode);
    $append_array = [
        'episode' => [
            'guid' => $item->guid,
            'titlePG' => $item->titlePG,
            'episodeNumPG' => $item->episodeNumPG,
            'seasonNumPG' => $item->seasonNumPG,
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
            'customTagsPG' => $item->customTagsPG,
            'filename' => $episode,
            'fileid' => pathinfo($episode, PATHINFO_FILENAME),
            'filemtime' => $filemtime,
            'moddate' => date('Y-m-d', $filemtime)
        ]
    ];
    return $append_array;
}

function getEpisodes($category = null, $_config)
{
    $uploadDir = $_config['absoluteurl'] . $_config['upload_dir'];
    $episodes_mtimes = setupEpisodes($_config);

    // Get XML data for the episodes of interest.
    $episodes_data = array();
    for ($i = 0; $i < count($episodes_mtimes); $i++) {
        $episode = $episodes_mtimes[$i][0];
        // We need to get the CDATA in plaintext.
        $xml_file_name = pathinfo($episode, PATHINFO_FILENAME) . '.xml';
        $xml = simplexml_load_file($uploadDir . $xml_file_name, null, LIBXML_NOCDATA);
        foreach ($xml as $item) {
            // If we are filtering by category, we can omit episodes
            // that lack the desired category.
            if ($category != null && $category != 'all') {
                if (
                    $item->categoriesPG->category1PG != $category
                    && $item->categoriesPG->category2PG != $category
                    && $item->categoriesPG->category3PG != $category
                ) {
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
    $uploadDir = $_config['absoluteurl'] . $_config['upload_dir'];

    // Check if name is a category and replace
    $cats_xml = simplexml_load_file('categories.xml');
    foreach ($cats_xml as $item) {
        if ($name === strtolower($item->description)) {
            $name = strval($item->id);
        }
    }

    // Get XML data for the episodes of interest.
    $episodes_data = array();
    for ($i = 0; $i < count($episodes_mtimes); $i++) {
        $episode = $episodes_mtimes[$i][0];

        // We need to get the CDATA in plaintext.
        $xml_file_name = pathinfo($episode, PATHINFO_FILENAME) . '.xml';
        $xml = simplexml_load_file($uploadDir . $xml_file_name, null, LIBXML_NOCDATA);
        foreach ($xml as $item) {
            if (
                strpos(strtolower($item->titlePG), $name) === false
                && strpos(strtolower($item->shortdescPG), $name) === false
                && strpos(strtolower($item->longdescPG), $name) === false
                && strpos(strtolower($item->categoriesPG->category1PG), $name) === false
                && strpos(strtolower($item->categoriesPG->category2PG), $name) === false
                && strpos(strtolower($item->categoriesPG->category3PG), $name) === false
                && strpos(strtolower($item->keywordsPG), $name) === false
                && strpos(strtolower($item->authorPG->namePG), $name) === false
            ) {
                continue;
            }
            array_push($episodes_data, arrayEpisode($item, $episode, $_config));
        }
    }

    return $episodes_data;
}

require_once $config['absoluteurl'] . 'vendor/james-heinrich/getid3/getid3/getid3.php';

/**
 * Get episode audio metadata from getID3.
 *
 * @param string $filename  The path of the episode audio file.
 * @return array            The result from getID3 analyzing the file.
 */
function getID3Info($filename)
{
    $getID3 = new getID3();
    return $getID3->analyze($filename);
}

// Fetch ID3 tags. Try ID3V2, then ID3V1, before falling back
// to the specific default value.
function getID3Tag($fileinfo, $tagName, $defaultValue = null)
{
    if (
        isset($fileinfo['tags']['id3v2'][$tagName][0])
        && $fileinfo['tags']['id3v2'][$tagName][0]
    ) {
        return $fileinfo['tags']['id3v2'][$tagName][0];
    } elseif (
        isset($fileinfo['tags']['id3v1'][$tagName][0])
        && $fileinfo['tags']['id3v1'][$tagName][0]
    ) {
        return $fileinfo['tags']['id3v1'][$tagName][0];
    } else {
        return $defaultValue;
    }
}

function indexEpisodes($_config)
{
    $new_files = array();
    $mimetypes = simplexml_load_file($_config['absoluteurl'] . 'components/supported_media/supported_media.xml');
    $uploadDir = $_config['absoluteurl'] . $_config['upload_dir'];

    // Get all files and check if they have an XML file associated
    if ($handle = opendir($uploadDir)) {
        while (false !== ($entry = readdir($handle))) {
            // Skip dotfiles
            if (substr($entry, 0, 1) == '.') {
                continue;
            }

            // Skip XML files
            if (pathinfo($entry, PATHINFO_EXTENSION) == 'xml') {
                continue;
            }

            // Check if an XML file for that episode exists
            if (file_exists($uploadDir . pathinfo($entry, PATHINFO_FILENAME) . '.xml')) {
                continue;
            }

            // Get mime type
            $mimetype = getmime($uploadDir . $entry);

            // Continue if file isn't readable
            if (!$mimetype) {
                continue;
            }

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

    // Generate XML from audio file (with mostly empty values)
    $num_added = 0;
    for ($i = 0; $i < count($new_files); $i++) {
        // Skip files if they are not strictly named
        if ($_config['strictfilenamepolicy'] == 'yes') {
            if (!preg_match('/^[\w.]+$/', $new_files[$i])) {
                continue;
            }
        }

        // Select new filenames (with date) if not already exists
        preg_match('/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/', $new_files[$i], $output_array);
        $fname = $new_files[$i];
        if (count($output_array) == 0) {
            $new_filename = $uploadDir . date('Y-m-d') . '_' . $new_files[$i];
            $new_filename = str_replace(' ', '_', $new_filename);
            $appendix = 1;
            while (file_exists($new_filename)) {
                $new_filename = $uploadDir . strtolower(date('Y-m-d') . '_' . $appendix . '_' . basename($new_files[$i]));
                $new_filename = str_replace(' ', '_', $new_filename);
                $appendix++;
            }
            rename($uploadDir . $new_files[$i], $new_filename);
            $fname = $new_filename;
        }

        // Get audio metadata (duration, bitrate etc)
        $fileinfo = getID3Info($fname);
        $duration = $fileinfo['playtime_string'];           // Get duration
        $bitrate = $fileinfo['audio']['bitrate'];           // Get bitrate
        $frequency = $fileinfo['audio']['sample_rate'];     // Frequency
        $title = getID3Tag($fileinfo, 'title', pathinfo($fname, PATHINFO_FILENAME));
        $comment = getID3Tag($fileinfo, 'comment', $title);
        $author_name = getID3Tag($fileinfo, 'artist', '');

        $link = str_replace('?', '', $_config['link']);
        $link = str_replace('=', '', $link);
        $link = str_replace('$url', '', $link);

        $episodefeed = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
        <episode>
            <guid>' . htmlspecialchars($_config['url'] . "?" . $link . "=" . basename($fname)) . '</guid>
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
                <namePG>' . $author_name . '</namePG>
                <emailPG></emailPG>
            </authorPG>
            <fileInfoPG>
                <size>' . intval(filesize($fname) / 1000 / 1000) . '</size>
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
        file_put_contents($uploadDir . pathinfo($fname, PATHINFO_FILENAME) . '.xml', $episodefeed);
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
