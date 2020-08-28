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
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    die();
}
require '../core/include_admin.php';

if (isset($_GET['deleteReset'])) {
    if (file_exists("reset.php")) {
        unlink("reset.php");
    }
}

if (file_exists("reset.php")) {
    die(_('Login disabled for security reasons'));
}

if (isset($_GET['login'])) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = _('Missing fields');
        goto esc;
    }
    if (checkLogin($_POST['username'], $_POST['password'])) {
        $_SESSION['username'] = $_POST['username'];
        header('Location: index.php');
        die();
    } else {
        $error = _('Invalid username or password');
    }
    esc: echo ("");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - Admin</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>

<body class="bg-light">
    <div class="container m-auto">
        <div class="align-items-center justify-content-md-center p-3 row vh-100">
            <div class="col-xl-5 col-lg-5 col-md-10 col-sm-12 bg-white p-4">
                <h2><?php echo htmlspecialchars($config['podcast_title']); ?> - Login</h2>
                <?php
                if (isset($error)) {
                    echo '<strong><p style="color: red;">' . $error . '</p></strong>';
                }
                ?>
                <form action="login.php?login=1" method="POST">
                    <div class="form-group">
                        <?php echo _('Username'); ?>:<br>
                        <input class="form-control" type="text" name="username">
                    </div>
                    <div class="form-group">
                        <?php echo _('Password'); ?>:<br>
                        <input class="form-control" type="password" name="password"><br>
                        <small><a href="forgot.php"><?php echo _('Forgot Password?'); ?></a></small><br>
                        <br>
                        <input type="submit" value="<?php echo _('Sign In'); ?>" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>