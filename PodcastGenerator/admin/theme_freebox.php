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
            echo '<form action="theme_freebox.php?enable=1" method="POST">';
            echo '<input type="hidden" name="token" value=' . $_SESSION['token'] . '>';
            echo '<input class="btn btn-success" type="submit" value="' . _('Enable Freebox') . '">';
            echo '</form>';
        } else {
            echo '<form action="theme_freebox.php?disable=1" method="POST">';
            echo '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
            echo '<input class="btn btn-danger" type="submit" value="' . _('Disable Freebox') . '">';
            echo '</form>';
        }
        ?>
        <form action="theme_freebox.php?change=1" method="POST">
            <?php
            if (getFreebox('../') != null) {
            ?>
                <h3><?php echo _('Change Freebox content'); ?></h3>
                <?php echo _('Content'); ?>:<br>
                <textarea rows="10" cols="100" name="content"><?php echo htmlspecialchars(getFreebox('../')); ?></textarea><br><br>
                <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                <input type="submit" value="<?php echo _('Save'); ?>" class="btn btn-success">
            <?php
            }
            ?>
        </form>
    </div>
</body>

</html>
