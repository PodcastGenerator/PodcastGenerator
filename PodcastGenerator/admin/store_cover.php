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

if (isset($_GET['upload'])) {
    // Check if file is too big
    if ($_FILES['file']['size'] > $config['max_upload_form_size']) {
        $error = _('File is too big');
    }
    $imagesize = getimagesize($_FILES['file']['tmp_name']);
    // Verify if image is a square
    if ($imagesize[0] / $imagesize[1] != 1) {
        $error = _('Image is not quadratic');
        goto error;
    }

    // Now everything is cool and the file can uploaded
    if (!move_uploaded_file($_FILES['file']['tmp_name'], '../' . $config['img_dir'] . 'itunes_image.jpg')) {
        $error = _('File was not uploaded');
        goto error;
    } else {
        // Wait a few seconds so the upload can finish
        sleep(3);
        header('Location: store_cover.php');
        die();
    }
    error: echo "";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Store Cover'); ?></title>
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
        <h1><?php echo _('Change Cover'); ?></h1>
        <p><?php echo _('The cover art will be displayed in the podcast readers.'); ?></p>
        <?php
        if (isset($error)) {
            echo '<strong><p style="color: red;">' . $error . '</p></strong>';
        }
        ?>
        <h3><?php echo _('Current Cover'); ?></h3>
        <img src="../images/itunes_image.jpg" style="max-height: 350px; max-width: 350px;">
        <hr>
        <h3><?php echo _('Upload new cover'); ?></h3>
        <form action="store_cover.php?upload=1" method="POST" enctype="multipart/form-data">
            <?php echo _('Select file'); ?>:<br>
            <input type="file" name="file"><br><br>
            <input type="submit" value="<?php echo _('Upload'); ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>