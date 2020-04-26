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

    // Get episodes names and pubDates (which are the file
    // modification times).  We'll ignore files with future
    // timestamps.
    $now_time = time();
    $episodes_mtimes = array();
    if ($handle = opendir($_config['upload_dir'])) {
        while (false !== ($entry = readdir($handle))) {
            // If the file is a 'real' file, has a linked XML file,
            // and isn't from the future, add its name and
            // modification time to our array.
            $this_entry = $_config['upload_dir'] . $entry;
            $this_mtime = filemtime($this_entry);
            if (in_array(pathinfo($this_entry, PATHINFO_EXTENSION), $supported_extensions)
                && file_exists($_config['upload_dir'] . pathinfo($this_entry, PATHINFO_FILENAME) . '.xml')
                && $this_mtime <= $now_time) {
                array_push($episodes_mtimes, [$entry, $this_mtime]);
            }
        }
    }

    // Sort entries according to their pubDates.
    usort($episodes_mtimes, 'compare_mtimes');

    // Get XML data for the episodes of interest.
    $episodes_data = array();
    for ($i = 0; $i < sizeof($episodes_mtimes); $i++) {
        $episode = $episodes_mtimes[$i][0];
        // We need to get the CDATA in plaintext.
        $xml_file_name = pathinfo('../' . $_config['upload_dir'] . $episode, PATHINFO_FILENAME) . '.xml';
        $xml = simplexml_load_file($_config['upload_dir'] . $xml_file_name, null, LIBXML_NOCDATA);
        foreach ($xml as $item) {
            // If we are filtering by category, we can omit episodes
            // that lack the desired category.
            if ($category != null && $category != 'all') {
                if ($item->categoriesPG->category1PG[0] != $category 
                    && $item->categoriesPG->category2PG[0] != $category
                    && $item->categoriesPG->category3PG[0] != $category) {
                    continue;
                }
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
                    'filename' => $episode,
                    'fileid' => pathinfo('../' . $_config['upload_dir'] . $episode, PATHINFO_FILENAME),
                    'moddate' => date('Y-m-d', filemtime($_config['upload_dir'] . $episode))
                ]
            ];
            array_push($episodes_data, $append_array);
        }
    }
    unset($_config);
    return $episodes_data;
}

// usort() compare function that reverse-sorts numeric values (which,
// in our case, are file modification times.
function compare_mtimes($a, $b)
{
    return $b[1] - $a[1];
}
