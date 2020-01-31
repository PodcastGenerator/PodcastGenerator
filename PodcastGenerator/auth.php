<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
session_start();
require 'core/include.php';
if($config['podcastPassword'] == "") {
    header('Location: index.php');
    die(_('This Podcast has no password'));
}
if($_SESSION['password'] == true) {
    header('Location: index.php');
    die(_('Already signed in'));
}
if(isset($_GET['login'])) {
    if($config['podcastPassword'] == $_POST['password']) {
        $_SESSION['password'] = true;
        header('Location: index.php');
        die(_('Success'));
    }
    else {
        $error = _('Invalid password');
        goto error;
    }
}

error:
echo '';
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo _('Password required'); ?></title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="core/bootstrap/style.css">
    </head>
    <body>
        <div class="container">
            <h1 style="color: #ff0000;"><?php echo $config['podcast_title']; ?> - <?php echo _('Password required'); ?></h1>
            <?php
            if(isset($error)) {
                echo '<p style="color: #ff0000;">'.$error.'</p>';
            }
            ?>
            <form action="auth.php?login=1" method="POST">
                <?php echo _('Enter Password'); ?>:<br>
                <input type="password" name="password"><br><br>
                <input type="submit" value="<?php echo _('Login'); ?>" class="btn btn-success">
            </form>
        </div>
    </body>
</html>