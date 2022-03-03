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

if (isset($_GET['edit'])) {
    checkToken();
    foreach ($_POST as $key => $value) {
        updateConfig('../config.php', $key, $value);
    }
    header('Location: pg_config.php');
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Podcast Generator Configuration') ?></title>
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
        <h1><?= _('Change Podcast Generator Configuration') ?></h1>
        <form action="pg_config.php?edit=1" method="POST">
            <?= _('Enable Audio and Video Player') ?>:<br>
            <small><?= _('Enable streaming in web browser') ?></small><br>
            <input type="radio" name="enablestreaming" value="yes" <?= $config['enablestreaming'] == 'yes' ? 'checked' : '' ?>> <?= _('Yes') ?>
            <input type="radio" name="enablestreaming" value="no" <?= $config['enablestreaming'] != 'yes' ? 'checked' : '' ?>> <?= _('No') ?><br>
            <hr>
            <?= _('Enable Freebox') ?>:<br>
            <small><?= _('Freebox allows you to write freely what you wish, add links or text through a visual editor in the admin section.') ?></small><br>
            <input type="radio" name="freebox" value="yes" <?= $config['freebox'] == 'yes' ? 'checked' : '' ?>> <?= _('Yes') ?>
            <input type="radio" name="freebox" value="no" <?= $config['freebox'] != 'yes' ? 'checked' : '' ?>> <?= _('No') ?><br>
            <hr>
            <?= _('Enable categories') ?>:<br>
            <small><?= _('Enable categories feature to make thematic lists of your podcasts.') ?></small><br>
            <input type="radio" name="categoriesenabled" value="yes" <?= $config['categoriesenabled'] == 'yes' ? 'checked' : '' ?>> <?= _('Yes') ?>
            <input type="radio" name="categoriesenabled" value="no" <?= $config['categoriesenabled'] != 'yes' ? 'checked' : '' ?>> <?= _('No') ?><br>
            <hr>
            <?= _('Enable custom tag input') ?>:<br>
            <small><?= _('Enable the ability to add custom RSS tags to your podcast feed and individual episodes.') ?></small><br>
            <input type="radio" name="customtagsenabled" value="yes" <?= $config['customtagsenabled'] == 'yes' ? 'checked' : '' ?>> <?= _('Yes') ?>
            <input type="radio" name="customtagsenabled" value="no" <?= $config['customtagsenabled'] != 'yes' ? 'checked' : '' ?>> <?= _('No') ?><br>
            <hr>
            <?= _('Use cron to regenerate the RSS feed') ?>:<br>
            <input type="text" value="<?= htmlspecialchars($config['url']) . "pg-cron.php?key=" . htmlspecialchars($config['installationKey']) ?>" style="width: 100%;" readonly><br>
            <hr>
            <?= _('Password Protection for the web pages') ?>:<br>
            <small><?= _('Leave empty for no password, keep in mind that the feed and the audio files will still be accessible no matter if a password is set or not') ?></small><br>
            <input type="text" name="podcastPassword" value="<?= $config['podcastPassword'] ?>"><br>
            <hr>
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>
