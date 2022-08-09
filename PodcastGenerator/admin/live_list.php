<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

require 'checkLogin.php';
require '../core/include_admin.php';

$dateFormat = 'Y-m-d H:i';
$statusColors = [
    LIVEITEM_STATUS_PENDING => 'blue',
    LIVEITEM_STATUS_LIVE => 'red',
    LIVEITEM_STATUS_ENDED => 'green'
];

function getLiveItemArray()
{
    global $config;

    $liveItems = getLiveItems($config);

    // sorts into descending order, as future and recent episodes are most
    // likely to be edited
    usort($liveItems, function ($a, $b) {
        return $a['filemtime'] <=> $b['filemtime'];
    });
    return array_reverse($liveItems);
}

$liveItems = getLiveItemArray();

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Live Items') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $config['url'] ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
    <h1><?= _('Live items') ?></h1>
        <p><?= _("Click on the title of the live item you want to edit/delete.") ?></p>
        <p>
            <?php foreach ($statusColors as $status => $color) { ?>
                <?= sprintf(_('Dates in <span style="color: %s;">%s</span> are %s.'), $color, $color, $status) ?>
            <?php } ?>
        </p>
        <?php if (isset($error)) {
            ?><p style="color: red;"><strong><?= $error ?></strong></p><?php
        } ?>
        <ul>
            <?php foreach ($liveItems as $liveItem) { ?>
            <li>
                <span style='color: <?= $statusColors[$liveItem['status']] ?>'>
                    <?= $liveItem['startTime']->format($dateFormat) ?>
                    &ndash;
                    <?= $liveItem['endTime']->format($dateFormat) ?>
                </span>
                : <a href='./live_edit.php?name=<?= $liveItem['filename'] ?>'><?= $liveItem['title'] ?></a>
            </li>
            <?php } ?>
        </ul>
    </div>
</body>

</html>
