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
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Podcast Generator Configuration'); ?></title>
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
        <h1><?php echo _('Change Podcast Generator Configuration'); ?></h1>
        <form action="pg_config.php?edit=1" method="POST">
            <?php echo _('Enable Audio and Video Player'); ?>:<br>
            <small><?php echo _('Enable streaming in web browser'); ?></small><br>
            <input type="radio" name="enablestreaming" value="yes" checked> <?php echo _('Yes'); ?> <input type="radio" name="enablestreaming" value="no"> <?php echo _('No'); ?><br>
            <hr>
            <?php echo _('Enable Freebox'); ?>:<br>
            <small><?php echo _('Freebox allows you to write freely what you wish, add links or text through a visual editor in the admin section.'); ?></small><br>
            <input type="radio" name="freebox" value="yes" checked> <?php echo _('Yes'); ?> <input type="radio" name="freebox" value="no"> <?php echo _('No'); ?><br>
            <hr>
            <?php echo _('Enable categories'); ?>:<br>
            <small><?php echo _('Enable categories feature to make thematic lists of your podcasts.'); ?></small><br>
            <input type="radio" name="categoriesenabled" value="yes" checked> <?php echo _('Yes'); ?> <input type="radio" name="categoriesenabled" value="no"> <?php echo _('No'); ?><br>
            <hr>
            <?php echo _('How many recent episodes in the homepage?'); ?><br>
            <?php
            if (strtolower($config['max_recent']) == 'all') {
                echo '<input type="number" name="max_recent" value="4" min="1"> <input type="radio" value="all" name="max_recent"> ' . _('All') . '<br>';
            } else {
                echo '<input type="radio" value="all" name="max_recent" checked> ' . _('All') . '<br>';
            }
            ?>
            <hr>
            <?php echo _('Use cron to regenerate the RSS feed'); ?>:<br>
            <input type="text" value="<?php echo htmlspecialchars($config['url']) . "pg-cron.php?key=" . htmlspecialchars($config['installationKey']); ?>" style="width: 100%;" readonly><br>
            <hr>
            <input type="submit" value="<?php echo _("Submit"); ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>