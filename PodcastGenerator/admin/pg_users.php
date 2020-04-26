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
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Manage users'); ?></title>
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
        <h1><?php echo _('Manage users'); ?></h1>
        <small><?php echo _('It may take a few seconds until the changes are visible'); ?></small><br>
        <?php
        if (isset($_GET['userchange'])) {
            echo '<strong style="color: green;">' . _('User changed successfully') . '</strong>';
        } elseif (isset($_GET['usercreate'])) {
            echo '<strong style="color: green;">' . _('User created successfully') . '</strong>';
        } elseif (isset($_GET['userdelete'])) {
            echo '<strong style="color: red;">' . _('User deleted successfully') . '</strong>';
        } else {
            // If no GETS are set, display all users
            if (sizeof($_GET) == 0) {
                echo '<h3>' . _('List of users') . '</h3>';
                echo '<ul>';
                foreach ($users as $username => $password) {
                    echo '<li><a href="pg_users.php?username=' . $username . '">' . $username . '</a></li>';
                }
                echo '</ul>';
        ?>
                <h3><?php echo _('Create User'); ?></h3>
                <form action="pg_users.php?create=1" method="POST">
                    <?php echo _('Username') ?>:<br>
                    <input type="text" name="username"><br>
                    <?php echo _('Password') ?>:<br>
                    <input type="password" name="password"><br>
                    <br>
                    <input type="submit" value="<?php echo _('Submit'); ?>" class="btn btn-success"><br>
                </form>
            <?php
            }
            // List a specific user
            if (isset($_GET['username'])) {
                if (!array_key_exists($_GET['username'], $users)) {
                    $error = _('User does not exist');
                    goto error;
                }
            ?>
                <form action="pg_users.php?change=<?php echo $_GET['username']; ?>" method="POST">
                    <?php echo _('Username'); ?>:<br>
                    <input type="text" name="username" value="<?php echo $_GET['username']; ?>" disabled> <small><?php echo _('You cannot edit usernames'); ?></small><br>
                    <?php echo _('New Password'); ?><br>
                    <input type="password" name="password"><br>
                    <?php echo _('Repeat new password'); ?><br>
                    <input type="password" name="password2"><br>
                    <br>
                    <input type="submit" value="<?php echo _('Change'); ?>" class="btn btn-success">
                </form>
                <hr>
                <h3><?php echo _('Delete user'); ?></h3>
                <?php
                // Don't permit to delete the logged in user
                if ($_GET['username'] == $_SESSION['username']) {
                    echo '<p>' . _('You cannot delete yourself') . '</p>';
                } else {
                    echo '<a href="pg_users.php?delete=' . $_GET['username'] . '" class="btn btn-danger">Delete</a>';
                }
                ?>
            <?php
            }
            ?>
        <?php
            error: if (isset($error)) {
                echo '<p style="color: red;">' . $error . '</p>';
            }
        }
        ?>
    </div>
</body>

</html>