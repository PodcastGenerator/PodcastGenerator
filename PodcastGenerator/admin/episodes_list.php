<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

require 'checkLogin.php';
require '../core/include_admin.php';

$episodes = array_map(fn ($e) => $e['episode'], findEpisodes($config, null, '', true));

// sort into descending order, as future and recent episodes are most likely to
// be edited
usort($episodes, fn ($a, $b) => $a['filemtime'] <=> $b['filemtime']);
$episodes = array_reverse($episodes);

$now = time();

?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Episodes'); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?php echo _('Episodes'); ?></h1>
        <p><?php echo _("Click on the title of the podcast you want to edit/delete."); ?></p>
        <p><?php echo _("Dates in <span style='color:blue;'>blue</span> are in the future. Dates in <span style='color:green;'>green</span> have been posted.")?></p>
        <?php if (isset($error)) {
            ?><p style="color: red;"><strong><?= $error ?></strong></p><?php
        } ?>
        <ul>
            <?php foreach ($episodes as $episode) { ?>
            <li>
                <span style='color:<?= ($episode['filemtime'] > $now) ? "blue" : "green" ?>'><?= $episode['moddate'] ?></span>
                - <a href='./episodes_edit.php?name=<?= $episode['filename'] ?>'><?= $episode['titlePG'] ?></a>
            </li>
            <?php } ?>
        </ul>
    </div>
</body>

</html>