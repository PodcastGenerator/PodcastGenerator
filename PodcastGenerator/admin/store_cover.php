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
    checkToken();

    $supportedMediaTypes = simplexml_load_file(
        $config['absoluteurl'] . 'components/supported_media/supported_media.xml'
    );

    // Check MIME type and extension
    $mimetype = mime_content_type($_FILES['file']['tmp_name']);
    $fileext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

    $validExts = array();
    foreach ($supportedMediaTypes->mediaFile as $item) {
        if (strpos($item->mimetype, 'image/') !== 0) {
            continue;
        }
        if ($item->mimetype == $mimetype) {
            $validExts[] = (string) $item->extension;
        }
    }

    if (empty($validExts)) {
        $error = _('Image format is not supported');
        goto error;
    }

    // Verify if image is a square
    $imagesize = getimagesize($_FILES['file']['tmp_name']);
    if ($imagesize[0] / $imagesize[1] != 1) {
        $error = _('Image does not have square dimensions');
        goto error;
    }

    // change extension if it's not in the valid extensions list
    if (!in_array($fileext, $validExts)) {
        $fileext = $validExts[0];
    }
    $filename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME) . '.' . $fileext;

    // Generate full, unique path for file
    $filename = makeUniqueFilename($config['absoluteurl'] . $config['img_dir'] . $filename);

    // Now everything is cool and the file can uploaded
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
        $error = _('Image was not uploaded successfully');
        goto error;
    }

    // Wait a few seconds so the upload can finish
    sleep(3);
    updateConfig('../config.php', 'podcast_cover', basename($filename));
    generateRSS();
    header('Location: store_cover.php');
    die();

    error:
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Store Cover') ?></title>
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
        <h1><?= _('Change Cover') ?></h1>
        <p><?= _('The cover art will be displayed in the podcast readers.') ?></p>
        <?php if (isset($error)) { ?>
            <strong><p style="color: red;"><?= $error ?></p></strong>
        <?php } ?>

        <h3><?= _('Current Cover') ?></h3>
        <img src="<?= $config['url'] . $config['img_dir'] . $config['podcast_cover'] ?>"
             style="max-height: 350px; max-width: 350px;">
        <hr>

        <h3><?= _('Upload new cover') ?></h3>
        <form action="store_cover.php?upload=1" method="POST" enctype="multipart/form-data">
            <label for="file"><?= _('Select file') ?>:</label><br>
            <input type="file" id="file" name="file"><br><br>

            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _('Upload') ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>
