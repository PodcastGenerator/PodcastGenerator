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

if (isset($_GET['start'])) {
    checkToken();
    $num_added = indexEpisodes($config);
    if ($num_added) {
        generateRSS();
        pingServices();
        $success = sprintf(ngettext('Added one new episode', 'Added %d new episodes', $num_added), $num_added);
    } else {
        $success = _('No new episodes were found');
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('FTP Feature') ?></title>
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
        <h1><?= _('FTP Auto Indexing') ?></h1>
        <?php if (!isset($_GET['start'])) { ?>
            <form action="episodes_ftp_feature.php?start=1" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token']  ?>">
            <input class="btn btn-success" type="submit" value="<?= _('Begin') ?>">
            </form>
        <?php } ?>
        <?php if (isset($success)) { ?>
            <p><?= htmlspecialchars($success) ?></p>
        <?php } ?>
    </div>
</body>

</html>
