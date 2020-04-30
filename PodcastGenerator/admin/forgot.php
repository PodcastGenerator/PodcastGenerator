<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo _('Password Reset') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>
<body>
    <div class="container">
        <h1><?php echo _('Reset your password'); ?></h1>
        <h3><?php echo _('No password, no problem!'); ?></h3>
        <p>
        <?php
        echo _('We offer a tool which allows you to easily recover your password. However, the danger of this is that as long as this file is online, anyone can reset this password so this file shouldn\'t be online that long.');
        echo '<br>';
        echo sprintf(_('In the ZIP file of this version which you downloaded there is a file in the %s folder called %s. Upload this file to into the admin directory with the name %s'), "contrib/recover", "reset.php", "reset.php");
        echo _('If you no don\'t have this ZIP file anymore, don\'t worry! All versions of Podcast Generator ever released since 2.5 are available on the Podcast Generator Homepage.');
        ?>
        </p>
    </div>
</body>
</html>