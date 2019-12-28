<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
function getEpisodes($category = null)
{
    $_config = getConfig('config.php');
    $supported_extensions = simplexml_load_file('components/supported_media/supported_media.xml');
    $realsupported_extensions = array();
    foreach ($supported_extensions as $item) {
        array_push($realsupported_extensions, $item->extension);
    }
    $supported_extensions = $realsupported_extensions;
    unset($realsupported_extensions);
    // Get episodes names
    $episodes = array();
    if ($handle = opendir($_config['upload_dir'])) {
        while (false !== ($entry = readdir($handle))) {
            // Check if the file is a 'real' file and if has a linked XML file
            if (in_array(pathinfo($_config['upload_dir'] . $entry, PATHINFO_EXTENSION), $supported_extensions) && file_exists($_config['upload_dir'] . pathinfo($_config['upload_dir'] . $entry, PATHINFO_FILENAME) . '.xml')) {
                array_push($episodes, $entry);
            }
        }
    }
    // Bubble sort files according to their pubDate
    do {
        $swapped = false;
        for ($i = 0, $c = sizeof($episodes) - 1; $i < $c; $i++) {
            if (filemtime($_config['upload_dir'] . $episodes[$i]) < filemtime($_config['upload_dir'] . $episodes[$i + 1])) {
                list($episodes[$i + 1], $episodes[$i]) = array($episodes[$i], $episodes[$i + 1]);
                $swapped = true;
            }
        }
    } while ($swapped);
    // Get XML data for the certain episodes
    $episodes_data = array();
    for ($i = 0; $i < sizeof($episodes); $i++) {
        // We need to get the CDATA in plaintext
        $xml = simplexml_load_file($_config['upload_dir'] . pathinfo('../' . $_config['upload_dir'] . $episodes[$i], PATHINFO_FILENAME) . '.xml', null, LIBXML_NOCDATA);
        foreach ($xml as $item) {
            // Skip episodes from the future
            if (filemtime($_config['upload_dir'] . $episodes[$i]) > time()) {
                break;
            }
            $append_array = [
                'episode' => [
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
                    'filename' => $episodes[$i],
                    'moddate' => date('Y-m-d', filemtime($_config['upload_dir'] . $episodes[$i]))
                ]
            ];
            array_push($episodes_data, $append_array);
        }
    }
    unset($_config);
    if ($category == null || $category == 'all') {
        return $episodes_data;
    }
    // Pop out non matching categories
    $realepisodes = array();
    foreach ($episodes_data as $item) {
        $categories = array();
        array_push($categories, $item['episode']['categoriesPG']['category1PG'][0], $item['episode']['categoriesPG']['category2PG'][0], $item['episode']['categoriesPG']['category3PG'][0]);
        if (in_array($category, $categories)) {
            array_push($realepisodes, $item);
        }
    }
    return $realepisodes;
}
