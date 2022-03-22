<?php
require '../core/Configuration.php';
require '../core/misc/functions.php';
require '../core/users.php';

$config = PodcastGenerator\Configuration::load('../config.php');
$users = getUsers();

if (isset($_GET['reset'])) {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password2'])) {
        $error = 'All fields need to be set';
        goto error;
    }
    if ($_POST['password'] != $_POST['password2']) {
        $error = 'Passwords do not match';
        goto error;
    }
    if (!array_key_exists($_POST['username'], $users)) {
        $error = 'User does not exist';
        goto error;
    }

    // No errors, continue
    $users[$_POST['username']] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ret = $config->set('users_json', str_replace('"', '\"', json_encode($users)), true);
    if (!$ret) {
        $error = 'Unknown error. Be sure the file the users.php file is writable';
        goto error;
    } else {
        header('Location: login.php?deleteReset=1');
        die();
    }
}

error:
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Podcast Generator Password Resetter</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $config['url'] ?>favicon.ico">
</head>

<body>
    <div class="container">
        <h1>Podcast Generator Password Resetter</h1>
        <b style="color: red;">
            Warning: This file is extremely dangerous! Delete it instantly after you no longer need it! It will try to
            self destruct it once it successfully resets a password but you should still check if the file exists and
            delete it in such a case! Otherwise ANYONE can reset your password easily and get access to your podcast.
        </b>
        <p>
            <?= isset($error) ? $error : '' ?>
        </p>
        <form action="reset.php?reset=1" method="POST">
            Username:<br>
            <select name="username">
                <?php foreach ($users as $key => $value) { ?>
                    <option value="<?= $key ?>"><?= $key ?></option>
                <?php } ?>
            </select><br>
            New Password:<br>
            <input type="password" name="password"><br>
            Repeat new password:<br>
            <input type="password" name="password2"><br><br>
            <input type="submit" value="Reset" class="btn btn-danger">
        </form>
    </div>
</body>

</html>