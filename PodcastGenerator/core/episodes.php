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
    $supported_extensions_xml = simplexml_load_file(
        $config['absoluteurl'] . 'components/supported_media/supported_media.xml'
    );
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
  if($episodeA['lastModified'] == $episodeB['lastModified']) {
    return 0;
  }
    return $episodeA['lastModified'] - $episodeB['lastModified'];
}

/**
 * usort() callback for sorting episodes by season and episode.
 *
 * @param mixed $episodeA
 * @param mixed $episodeB
 * @return int
 */

function getValueOrDefault($val)
{
    if (empty($val) || !is_numeric($val)) {
        return -1;
    }
    return $val + 0;
}


function sort_episodes_by_season_and_episode($episodeA, $episodeB)
{


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

function getEpisodeFiles($_config, $includeFuture = false)
{
    global $config;

    $now = time();
    $supported_extensions = getSupportedExtensions($_config);
    $uploadDir = $_config['absoluteurl'] . $_config['upload_dir'];

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
                'data' => simplexml_load_file($dataFile, null, LIBXML_NOCDATA)
            ]);
        }
    }

    // sort episodes by the selected method
    $sortMethod = 'sort_episodes_by_' . (!empty($config['feed_sort']) ? $config['feed_sort'] : 'timestamp');
    usort($files, $sortMethod);

    // need to reverse the array since it's sorted ascending
    return array_reverse($files);
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

/**
 * Gets data for all episodes matching the provided conditions.
 *
 * @param mixed   $_config       The PG site configuration.
 * @param string  $category      The category to filter on, or null for all episodes.
 * @param string  $searchTerm    The search term used to filter episodes.
 * @param boolean $includeFuture Whether to include unpublished episodes or not.
 * @return array
 *
 * @since 3.2
 */
function findEpisodes($_config, $category = null, $searchTerm = '', $includeFuture = false)
{
    $episodes = getEpisodeFiles($_config, $includeFuture);

    if ($category != null) {
        $episodes = array_filter(
            $episodes,
            function ($ep) use ($category) {
                $categories = $ep['data']->episode->categoriesPG;
                return $categories->category1PG == $category
                    || $categories->category2PG == $category
                    || $categories->category3PG == $category;
            }
        );
    }

    if (!empty($searchTerm)) {
        $searchTerm = strtolower($searchTerm);
        $episodes = array_filter(
            $episodes,
            function ($ep) use ($searchTerm) {
                $data = $ep['data']->episode;
                return strpos(strtolower($data->titlePG), $searchTerm) !== false
                    || strpos(strtolower($data->shortdescPG), $searchTerm) !== false
                    || strpos(strtolower($data->longdescPG), $searchTerm) !== false
                    || strpos(strtolower($data->categoriesPG->category1PG), $searchTerm) !== false
                    || strpos(strtolower($data->categoriesPG->category2PG), $searchTerm) !== false
                    || strpos(strtolower($data->categoriesPG->category3PG), $searchTerm) !== false
                    || strpos(strtolower($data->keywordsPG), $searchTerm) !== false
                    || strpos(strtolower($data->authorPG->namePG), $searchTerm) !== false;
            }
        );
    }

    return array_map(
        function ($ep) use ($_config) {
            return arrayEpisode($ep['data']->episode, $ep['filename'], $_config);
        },
        $episodes
    );
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
        $filename = basename($new_files[$i]);

        // Skip files if they are not strictly named
        if ($_config['strictfilenamepolicy'] == 'yes') {
            if (!preg_match('/^[\w._-]+$/', $filename)) {
                continue;
            }
        }

        // fix filename encoding if mbstring is present
        if (extension_loaded('mbstring')) {
            $filename = mb_convert_encoding($filename, 'UTF-8', mb_detect_encoding($filename));
        }

        // Select new filenames (with date) if not already exists
        preg_match('/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/', $filename, $output_array);
        $fname = $filename;
        if (count($output_array) == 0) {
            $new_filename = $uploadDir . date('Y-m-d') . '_' . $filename;
            $new_filename = str_replace(' ', '_', $new_filename);
            $appendix = 1;
            while (file_exists($new_filename)) {
                $new_filename = $uploadDir . strtolower(
                    date('Y-m-d') . '_' . $appendix . '_' . basename($filename)
                );
                $new_filename = str_replace(' ', '_', $new_filename);
                $appendix++;
            }
            rename($uploadDir . $filename, $new_filename);
            $fname = $new_filename;
        } else {
            // We don't need to rename the file, but we do need to get the full path
            $fname = $uploadDir . $filename;
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
            $img_filename =
                $_config['absoluteurl'] . $_config['img_dir'] . pathinfo($fname, PATHINFO_FILENAME) . '.' . $imgext;
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

/**
 * Creates a standardized filename for the provided original filename.
 *
 * Because this function takes a directory name, it is useful for both episode
 * files and cover art files.
 *
 * @param string $directory The path of the directory where the file will be
 *                          saved.
 * @param string $date      The publication date of the file in YYYY-MM-DD
 *                          format.
 * @param string $filename  The original name of the file to be saved.
 * @return string           The file name and path to use for saving the file.
 */
function makeEpisodeFilename($directory, $date, $filename)
{
    $filename = strtolower(trim(preg_replace('/[^a-zA-Z0-9._-]+/', '_', $filename), '_'));
    $targetfile = $directory . $date . '_' . $filename;

    if (file_exists($targetfile)) {
        $appendix = 1;
        while (file_exists($targetfile)) {
            $targetfile = $directory . $date . '_' . $appendix . '_' . $filename;
            $appendix++;
        }
    }
    return $targetfile;
}

/**
 * Deletes a podcast episode and its related sidecar file and cover images.
 *
 * This function does not regenerate the XML feed or ping third party services.
 * Whoever calls this function should ensure that those tasks are taken care of.
 *
 * On failure, some files related to a podcast episode may still be left around
 * the media and images directories.
 *
 * @param string $episodeFile The path of the episode file to delete.
 * @param mixed  $config      The PG configuration object.
 * @return bool               true on success or false on failure.
 */
function deleteEpisode($episodeFile, $config)
{
    $imagesDir = $config['absoluteurl'] . $config['img_dir'];

    $pathinfo = pathinfo($episodeFile);
    $xmlFile = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.xml';

    $filesToDelete = [
        $episodeFile,
        $xmlFile
    ];

    $xmlData = simplexml_load_file($xmlFile);
    $coverImg = (string) $xmlData->episode->imgPG->attributes()['path'];
    if (!empty($coverImg)) {
        $filesToDelete[] = $coverImg;
    }
    if (isset($xmlData->episode->previousImgsPG)) {
        foreach ($xmlData->episode->previousImgsPG->children() as $prevImg) {
            $filesToDelete[] = (string) $prevImg;
        }
    }

    if (file_exists($imagesDir . pathinfo($episodeFile, PATHINFO_FILENAME) . '.jpg')) {
        $filesToDelete[] = $imagesDir . pathinfo($episodeFile, PATHINFO_FILENAME) . '.jpg';
    } elseif (file_exists($imagesDir . pathinfo($episodeFile, PATHINFO_FILENAME) . '.png')) {
        $filesToDelete[] = $imagesDir . pathinfo($episodeFile, PATHINFO_FILENAME) . '.png';
    }

    // Go through the list of files and delete each one
    foreach ($filesToDelete as $file) {
        if (!unlink($file)) {
            return false;
        }
    }
    return true;
}
