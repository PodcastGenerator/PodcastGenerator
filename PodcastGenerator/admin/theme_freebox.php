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
    updateConfig('../config.php', 'freebox', 'no');
    header('Location: theme_freebox.php');
    die();
}
if (isset($_GET['enable'])) {
    updateConfig('../config.php', 'freebox', 'yes');
    header('Location: theme_freebox.php');
    die();
}

if (isset($_GET['change'])) {
    updateFreebox('../', $_POST['content']);
    header('Location: theme_freebox.php');
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Customize Freebox'); ?></title>
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
        <h1><?php echo _('Customize Freebox'); ?></h1>
        <?php
        if (getFreebox('../') != null) {
            echo '
        <h3>' . _('Current Freebox') . '</h3>
        <div class="card">
            <div class="card-body">
                ' . getFreebox('../') . '
            </div>
        </div>';
        }
        ?>
        <h3><?php echo _('Enable / Disable Freebox'); ?></h3>
        <?php
        if (getFreebox('../') == null) {
            echo '<a href="theme_freebox.php?enable=1" class="btn btn-success">' . _('Enable Freebox') . '</a>';
        } else {
            echo '<a href="theme_freebox.php?disable=1" class="btn btn-danger">' . _('Disable Freebox') . '</a>';
        }
        ?>
        <h3><?php echo _('Change Freebox content'); ?></h3>
        <form action="theme_freebox.php?change=1" method="POST">
            <?php echo _('Content'); ?>:<br>
            <textarea rows="10" cols="100" name="content"><?php echo htmlspecialchars(getFreebox('../')); ?></textarea><br><br>
            <input type="submit" value="<?php echo _('Save'); ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>