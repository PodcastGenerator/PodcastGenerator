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

if (isset($_GET['disable'])) {
    checkToken();
    updateConfig('../config.php', 'freebox', 'no');
    header('Location: theme_freebox.php');
    die();
}
if (isset($_GET['enable'])) {
    checkToken();
    updateConfig('../config.php', 'freebox', 'yes');
    header('Location: theme_freebox.php');
    die();
}

if (isset($_GET['change'])) {
    checkToken();
    updateFreebox('../', $_POST['content']);
    header('Location: theme_freebox.php');
    die();
}

$freebox = getFreebox('../');

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Customize Freebox') ?></title>
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
        <h1><?= _('Customize Freebox') ?></h1>
        <?php if ($freebox != null) { ?>
            <h3><?= _('Current Freebox') ?></h3>
            <div class="card">
                <div class="card-body">
                    <?= $freebox ?>
                </div>
            </div>
        <?php } ?>
        <h3><?= _('Enable / Disable Freebox') ?></h3>
        <?php if ($freebox == null) { ?>
            <form action="theme_freebox.php?enable=1" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input class="btn btn-success" type="submit" value="<?= _('Enable Freebox') ?>">
            </form>
        <?php } else { ?>
            <form action="theme_freebox.php?disable=1" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input class="btn btn-danger" type="submit" value="<?= _('Disable Freebox') ?>">
            </form>
        <?php } ?>
        <form action="theme_freebox.php?change=1" method="POST">
            <?php if ($freebox != null) { ?>
                <h3><?= _('Change Freebox content') ?></h3>
                <label for="content">?= _('Content') ?>:</label><br>
                <textarea rows="10" cols="100"
                        id="content" name="content"><?= htmlspecialchars(getFreebox('../')) ?></textarea>
                <br>

                <br>
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                <input type="submit" value="<?= _('Save') ?>" class="btn btn-success">
            <?php } ?>
        </form>
    </div>
</body>

</html>
