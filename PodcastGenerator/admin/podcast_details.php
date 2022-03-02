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

$languages = simplexml_load_file('../components/supported_languages/podcast_languages.xml');

if (isset($_GET['edit'])) {
    checkToken();
    foreach ($_POST as $key => $value) {
        if ($key == 'custom_tags') {
            // need to handle custom_tags specially
            $custom_tags = $value;
            if (isWellFormedXml($custom_tags)) {
                // only set the value if it's well-formed XML
                saveCustomFeedTags($custom_tags);
            } elseif ($config['customtagsenabled'] == 'yes') {
                // only error if custom tags feature is enabled
                $error = _('Custom tags are not well-formed');
            }
        } else {
            updateConfig('../config.php', $key, $value);
        }
    }

    if (!isset($error)) {
        header('Location: podcast_details.php');
        die();
    }
    header('Location: podcast_details.php');
    die();
} else {
    generateRSS();
    pingServices();
}

$custom_tags = getCustomFeedTags();

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Podcast Details') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        .txt {
            width: 100%;
        }
    </style>
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
        <h1><?= _('Change Podcast Details') ?></h1>
        <form action="podcast_details.php?edit=1" method="POST">
            <?= _('Podcast Title') ?>:<br>
            <input type="text" name="podcast_title" value="<?= htmlspecialchars($config['podcast_title']) ?>" class="txt"><br>
            <?= _('Podcast Subtitle or Slogan') ?>:<br>
            <input type="text" name="podcast_subtitle" value="<?= htmlspecialchars($config['podcast_subtitle']) ?>" class="txt"><br>
            <?= _('Podcast Description') ?>:<br>
            <input type="text" name="podcast_description" value="<?= htmlspecialchars($config['podcast_description']) ?>" class="txt"><br>
            <?= _('Copyright Notice') ?>:<br>
            <input type="text" name="copyright" value="<?= htmlspecialchars($config['copyright']) ?>" class="txt"><br>
            <?= _('Author Name') ?>:<br>
            <input type="text" name="author_name" value="<?= htmlspecialchars($config['author_name']) ?>" class="txt"><br>
            <?= _('Author E-Mail Address') ?>:<br>
            <input type="text" name="author_email" value="<?= htmlspecialchars($config['author_email']) ?>" class="txt"><br>
            <?= _('Feed Language'); ?>: (<?= _('Main language of your podcast') ?>)<br>
            <select name="feed_language">
                <?php foreach ($languages as $lang) { ?>
                    <option value="<?= htmlspecialchars($lang->code) ?>"<?= $config['feed_language'] == $lang->code ? " selected" : "" ?>><?= htmlspecialchars($lang->name) ?></option>
                <?php } ?>
            </select><br>
            <?= _('Explicit Podcast') ?>:<br>
            <input type="radio" name="explicit_podcast" value="yes" <?= $config['explicit_podcast'] == 'yes' ? 'checked' : '' ?>> <?= _('Yes'); ?> <input type="radio" name="explicit_podcast" value="no" <?= $config['explicit_podcast'] == 'no' ? 'checked' : '' ?>> <?= _('No') ?><br>
            <br>
<?php if ($config['customtagsenabled'] == 'yes') { ?>
            <?= _('Custom Feed Tags') ?>:<br>
            <textarea name="custom_tags" style="width:100%;"><?= htmlspecialchars($custom_tags) ?></textarea>
            <br>
<?php } ?>
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>
