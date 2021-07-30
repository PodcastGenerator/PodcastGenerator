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

$users = getUsers();

// Change password case
if (isset($_GET['change'])) {
    checkToken();
    if (!changeUserPassword($_GET['change'], $_POST['password'])) {
        $error = _('Error while changing password');
        goto error;
    } else {
        header('Location: pg_users.php?userchange=1');
        die();
    }
}
// Delete user case
else if (isset($_GET['delete'])) {
    checkToken();
    // Check if the deleted user is the logged in user
    // Don't permit to delete the logged in user
    if ($_GET['delete'] == $_SESSION['username']) {
        $error = _('You cannot delete yourself');
        goto error;
    }
    // Check if user exists
    if (!array_key_exists($_GET['delete'], $users)) {
        $error = _('User does not exists');
        goto error;
    }
    if (!deleteUser($_GET['delete'])) {
        $error = _('Unknown error while deleting user');
        goto error;
    } else {
        header('Location: pg_users.php?userdelete=1');
        die();
    }
}
// Create user case
else if (isset($_GET['create'])) {
    checkToken();
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = _('Missing fields');
        goto error;
    }
    if (!addUser($_POST['username'], $_POST['password'])) {
        $error = _('Error while creating user');
        goto error;
    }
    header('Location: pg_users.php?usercreate=1');
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Manage users') ?></title>
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
        <h1><?= _('Manage users') ?></h1>
        <small><?= _('It may take a few seconds until the changes are visible') ?></small><br>
        <?php if (isset($_GET['userchange'])) { ?>
            <strong style="color: green;"><?= _('User changed successfully') ?></strong>
        <?php } elseif (isset($_GET['usercreate'])) { ?>
            <strong style="color: green;"><?= _('User created successfully') ?></strong>
        <?php } elseif (isset($_GET['userdelete'])) { ?>
            <strong style="color: red;"><?= _('User deleted successfully') ?></strong>
        <?php } else { ?>
            <?php if (sizeof($_GET) == 0) { /* If no GETS are set, display all users */ ?>
                <h3><? _('List of users') ?></h3>
                <ul>
                <?php foreach ($users as $username => $password) { ?>
                    <li><a href="pg_users.php?username=<?= $username ?>"><?= $username ?></a></li>
                <?php } ?>
                </ul>

                <h3><?= _('Create User') ?></h3>
                <form action="pg_users.php?create=1" method="POST">
                    <?= _('Username') ?>:<br>
                    <input type="text" name="username"><br>
                    <?= _('Password') ?>:<br>
                    <input type="password" name="password"><br>
                    <br>
                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                    <input type="submit" value="<?= _('Submit') ?>" class="btn btn-success"><br>
                </form>
            <?php } ?>
            <?php if (isset($_GET['username'])) { /* List a specific user */ ?>
                <?php
                    if (!array_key_exists($_GET['username'], $users)) {
                        $error = _('User does not exist');
                        goto error;
                    }
                ?>
                <form action="pg_users.php?change=<?= $_GET['username'] ?>" method="POST">
                    <?= _('Username') ?>:<br>
                    <input type="text" name="username" value="<?= $_GET['username']; ?>" disabled> <small><?= _('You cannot edit usernames') ?></small><br>
                    <?= _('New Password') ?><br>
                    <input type="password" name="password"><br>
                    <?= _('Repeat new password') ?><br>
                    <input type="password" name="password2"><br>
                    <br>
                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                    <input type="submit" value="<?= _('Change') ?>" class="btn btn-success">
                </form>
                <hr>
                <h3><?= _('Delete user') ?></h3>
                <?php if ($_GET['username'] == $_SESSION['username']) { /* Don't permit to delete the logged in user */ ?>
                    <p><?= _('You cannot delete yourself') ?></p>
                <?php } else { ?>
                    <form action="pg_users.php?delete=<?= $_GET['username'] ?>" method="POST">
                        <input type="hidden" name="token" value="<?= $_SESSION['token' ] ?>">
                        <input class="btn btn-danger" type="submit" value="<?= _('Delete') ?>">
                    </form>
                <?php } ?>
            <?php } ?>
            <?php error: if (isset($error)) { ?>
                <p style="color: red;"><?= $error ?></p>
            <?php } ?>
        <?php } ?>
    </div>
</body>

</html>
