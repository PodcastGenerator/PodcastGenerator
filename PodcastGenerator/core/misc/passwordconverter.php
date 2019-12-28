<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
// TODO: Port the entire file to the backwards comp. function
require 'configsystem.php';
$config = getConfig('../../config.php');
// Check if the hash is MD5
if (strlen($config['userpassword']) != 32) {
    header('Location: ../../index.php');
    die('Password is already secure');
}
if (isset($_GET['convert'])) {
    $p = $_POST;
    $newpassword = password_hash($p['password'], PASSWORD_DEFAULT);
    $newpassword = str_replace('$', '\$', $newpassword);
    if (md5($p['password']) != $config['userpassword']) {
        $error = 'Password is not correct';
        goto error;
    }
    updateConfig('../../config.php', 'userpassword', $newpassword);
    sleep(0.5);
    header('Refresh:0');
    die('Password updated');
}
error: echo "";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Password Converter</title>
</head>

<body>
    <h1>Password Converter</h1>
    <p>
        <?php
        if (isset($error)) {
            echo '<strong><p style="color: red;">' . $error . '</p></strong>';
        }
        ?>
        The way your pasword is stored, is broken for over 14 years as of 2019.<br>
        This agent will help you to make it secure again.<br>
        <form action="passwordconverter.php?convert=1" method="POST">
            Current Password:<br>
            <input type="password" name="password"><br><br>
            <input type="submit" value="Submit"><br>
        </form>
    </p>
</body>

</html>