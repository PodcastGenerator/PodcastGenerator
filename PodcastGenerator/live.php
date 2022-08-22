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
    } else {
        $now = new DateTimeImmutable();

        // need to filter pending and ended live items
        $endedLiveItems = [];
        $liveLiveItems = [];
        $pendingLiveItems = [];

        foreach ($liveItems as $liveItem) {
            switch ($liveItem['status']) {
                case LIVEITEM_STATUS_ENDED:
                    $endedLiveItems[] = $liveItem;
                    break;
                case LIVEITEM_STATUS_LIVE:
                    $liveLiveItems[] = $liveItem;
                    break;
                case LIVEITEM_STATUS_PENDING:
                    $pendingLiveItems[] = $liveItem;
                    break;
            }
        }

        // for ended live items, we need the items from the far end of the array
        // where the endTime >= now - liveitems_earliest_ended
        $maxEnded = (int) $config['liveitems_max_ended'];
        $endTimeSeconds = (int) ($config['liveitems_earliest_ended'] * 86400);
        $endTimeInterval = DateInterval::createFromDateString("$endTimeSeconds seconds");
        $endTime = $now->sub($endTimeInterval);
        $endedLiveItems = array_filter(
            array_slice($endedLiveItems, -$maxEnded, $maxEnded),
            function ($liveItem) use ($endTime) {
                return $liveItem['endTime'] >= $endTime;
            }
        );

        // for pending live items, we need the items from the start of the array
        // where the startTime <= now + liveitems_latest_pending
        $maxPending = (int) $config['liveitems_max_pending'];
        $pndTimeSeconds = (int) ($config['liveitems_latest_pending'] * 86400);
        $pndTimeInterval = DateInterval::createFromDateString("$pndTimeSeconds seconds");
        $pndTime = $now->add($pndTimeInterval);
        $pendingLiveItems = array_filter(
            array_slice($pendingLiveItems, 0, $maxPending),
            function ($liveItem) use ($pndTime) {
                return $liveItem['startTime'] <= $pndTime;
            }
        );

        $ranks = [ LIVEITEM_STATUS_LIVE => 0, LIVEITEM_STATUS_PENDING => 1, LIVEITEM_STATUS_ENDED => 2 ];

        // Live live items come first, followed by pending live items, all in
        // ascending order by start date and then end date. Ended live items
        // come last, descending by start date and end date.
        $liveItems = array_merge($liveLiveItems, $pendingLiveItems, $endedLiveItems);
        usort(
            $liveItems,
            function ($a, $b) use ($ranks) {
                $diff = $ranks[$a['status']] <=> $ranks[$b['status']];
                if ($diff == 0) {
                    // compare start and end times
                    $diff = $a['startTime'] <=> $b['startTime'];
                    if ($diff == 0) {
                        $diff = $a['endTime'] <=> $b['endTime'];
                    }
                    if ($a['status'] == LIVEITEM_STATUS_ENDED) {
                        $diff = -$diff;
                    }
                }
                return $diff;
            }
        );

        unset($liveLiveItems);
        unset($livePendingItems);
        unset($liveEndedItems);
    }
}

$loggedIn = isset($_SESSION["username"]);

$buttons = getButtons('./');
require $config['theme_path'] . 'live.php';
