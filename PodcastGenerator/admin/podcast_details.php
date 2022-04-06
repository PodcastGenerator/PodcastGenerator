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
        updateConfig('../config.php', $key, $value);
    }
    header('Location: podcast_details.php');
    die();
} else {
    generateRSS();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Podcast Details'); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        .txt {
            width: 100%;
        }
    </style>
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
        <h1><?php echo _('Change Podcast Details'); ?></h1>
        <form action="podcast_details.php?edit=1" method="POST">
            <?php echo _('Podcast Title'); ?>:<br>
            <input type="text" name="podcast_title" value="<?php echo htmlspecialchars($config['podcast_title']); ?>" class="txt"><br>
            <?php echo _('Podcast Subtitle or Slogan'); ?>:<br>
            <input type="text" name="podcast_subtitle" value="<?php echo htmlspecialchars($config['podcast_subtitle']); ?>" class="txt"><br>
            <?php echo _('Podcast Description'); ?>:<br>
            <input type="text" name="podcast_description" value="<?php echo htmlspecialchars($config['podcast_description']); ?>" class="txt"><br>
            <?php echo _('Copyright Notice'); ?>:<br>
            <input type="text" name="copyright" value="<?php echo htmlspecialchars($config['copyright']); ?>" class="txt"><br>
            <?php echo _('Author Name'); ?>:<br>
            <input type="text" name="author_name" value="<?php echo htmlspecialchars($config['author_name']); ?>" class="txt"><br>
            <?php echo _('Author E-Mail Address'); ?>:<br>
            <input type="text" name="author_email" value="<?php echo htmlspecialchars($config['author_email']); ?>" class="txt"><br>
            <?php echo _('Feed Language'); ?>: (<?php echo _('Main language of your podcast'); ?>)<br>
            <select name="feed_language">
                <?php foreach ($languages as $lang) { ?>
                    <option value="<?= htmlspecialchars($lang->code) ?>"<?= $config['feed_language'] == $lang->code ? " selected" : "" ?>><?= htmlspecialchars($lang->name) ?></option>
                <?php } ?>
            </select><br>
            <?php echo _('Explicit Podcast'); ?>:<br>
            <input type="radio" name="explicit_podcast" value="yes" <?php echo $config['explicit_podcast'] == 'yes' ? 'checked' : '' ?>> <?php echo _('Yes'); ?> <input type="radio" name="explicit_podcast" value="no" <?php echo $config['explicit_podcast'] == 'no' ? 'checked' : '' ?>> <?php echo _('No'); ?><br>
            <br>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <input type="submit" value="<?php echo _("Submit") ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>
