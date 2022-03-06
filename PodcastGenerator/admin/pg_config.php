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

$currentTz = $config['timezone'];
if (empty($currentTz)) {
    $currentTz = date_default_timezone_get();
}

function strcasecmp_empty_last($str1, $str2)
{
    if ($str1 == $str2) {
        return 0;
    } elseif ($str1 == '' || $str1 == null) {
        return 1;
    } elseif ($str2 == '' || $str2 == null) {
        return -1;
    } else {
        return strcasecmp($str1, $str2);
    }
}

$timezones = array();
foreach (DateTimeZone::listIdentifiers() as $tz) {
    $group = explode('/', $tz, 2)[0];
    if ($group == $tz) {
        $group = '';
    }

    if (!isset($timezones[$group])) {
        $timezones[$group] = array();
    }
    array_push($timezones[$group], $tz);
}
uksort($timezones, 'strcasecmp_empty_last');
foreach ($timezones as $group => $list) {
    asort($list);
}

$cronLink = htmlspecialchars($config['url'] . "pg-cron.php?key=" . $config['installationKey']);

$episodeSortOrderOptions = array(
    [ 'value' => 'timestamp', 'label' => _('Timestamp') ],
    [ 'value' => 'season_and_episode', 'label' => _('Season and episode number') ]
);

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
            <?php htmlOptionRadios('enablestreaming', $config['enablestreaming'], $yesNoOptions); ?>
            <br>
            <hr>

            <?= _('Enable Freebox') ?>:<br>
            <small>
                <?= _('Freebox allows you to write freely what you wish, add links or text through a visual editor in the admin section.') ?>
            </small><br>
            <?php htmlOptionRadios('freebox', $config['freebox'], $yesNoOptions); ?>
            <br>
            <hr>

            <?= _('Enable categories') ?>:<br>
            <small><?= _('Enable categories feature to make thematic lists of your podcasts.') ?></small><br>
            <?php htmlOptionRadios('categoriesenabled', $config['categoriesenabled'], $yesNoOptions); ?>
            <br>
            <hr>

            <?= _('Enable custom tag input') ?>:<br>
            <small>
                <?= _('Enable the ability to add custom RSS tags to your podcast feed and individual episodes.') ?>
            </small><br>
            <?php htmlOptionRadios('customtagsenabled', $config['customtagsenabled'], $yesNoOptions); ?>
            <br>
            <hr>

            <?= _('Episode sort order') ?>:<br>
            <small><?= _('Choose how episodes are ordered on the website and in the RSS feed.') ?></small><br>
            <?php htmlOptionRadios('feed_sort', $config['feed_sort'], $episodeSortOrderOptions); ?>
            <br>
            <hr>

            <?= _('Time zone') ?>:<br>
            <small><?= _('Select time zone for displaying the time which episodes have been released.') ?></small><br>
            <select name="timezone">
                <?php foreach ($timezones as $header => $zones) { ?>
                    <?php if ($header != '') { ?>
                        <optgroup label="<?= $header ?>">
                    <?php } ?>
                    <?php foreach ($zones as $zone) { ?>
                        <option <?= ($zone == $currentTz) ? "selected" : "" ?>><?= $zone ?></option>
                    <?php } ?>
                    <?php if ($header != '') { ?>
                        </optgroup>
                    <?php } ?>
                <?php } ?>
            </select>
            <hr>

            <?= _('Use cron to regenerate the RSS feed') ?>:<br>
            <input type="text" readonly style="width: 100%;" value="<?= $cronLink ?>">
            <br>
            <hr>

            <label for="podcastPassword"><?= _('Password Protection for the web pages') ?>:</label><br>
            <small>
                <?= _('Leave empty for no password, keep in mind that the feed and the audio files will still be accessible no matter if a password is set or not') ?>
            </small><br>
            <input type="text" id="podcastPassword" name="podcastPassword" value="<?= $config['podcastPassword'] ?>">
            <br>

            <hr>
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>
