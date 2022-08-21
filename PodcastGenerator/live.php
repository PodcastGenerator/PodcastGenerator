<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

session_start();
require 'core/include.php';
// Check if a password is set
if ($config['podcastPassword'] != "") {
    if (!isset($_SESSION['password'])) {
        header('Location: auth.php');
        die(_('Authentication required'));
    }
}

if (isset($_GET['name'])) {
    // get the particular live item by 'name'
    $filename = $_GET['name'];
    checkPath($filename);
    $filename = $config['absoluteurl'] . $config['upload_dir'] . '_live_' . $filename . '.xml';
    if (file_exists($filename)) {
        $liveItem = loadLiveItem($filename, $config);
    }
} else {
    // grab all the live items
    $liveItems = getLiveItems($config);

    if (!isset($_GET['all'])) {
        // get the 'current' live item: the "oldest" in live status, or if none
        // are in live status, the "oldest" in pending status.
        $filteredItems = array_filter($liveItems, function ($l) { return $l['status'] == LIVEITEM_STATUS_LIVE; });
        if (empty($filteredItems)) {
            $filteredItems = array_filter(
                $liveItems,
                function ($l) { return $l['status'] == LIVEITEM_STATUS_PENDING; }
            );
        }

        if (!empty($filteredItems)) {
            // assuming live items are already sorted by ascending start date
            $liveItem = array_values($filteredItems)[0];
        } else {
            $liveItem = null; // no 'current' live item!
        }

        // clean up the live items collection
        unset($liveItems);
        unset($filteredItems);
    }
}

$loggedIn = isset($_SESSION["username"]);

$buttons = getButtons('./');
require $config['theme_path'] . 'live.php';
