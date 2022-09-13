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

if (isset($_GET['remove'])) {
    checkToken();

    if (!$config->set('liveitem_default_cover', basename($filename), true)) {
        $error = _('Could not save configuration change for default live item cover');
        goto error;
    }

    generateRSS();
    header('Location: live_cover.php');
    die();
} elseif (isset($_GET['upload'])) {
    checkToken();

    $supportedMediaTypes = simplexml_load_file(
        $config['absoluteurl'] . 'components/supported_media/supported_media.xml'
    );

    // Check MIME type and extension
    $mimetype = mime_content_type($_FILES['file']['tmp_name']);
    $extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

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
    $imageSize = getimageSize($_FILES['file']['tmp_name']);
    if ($imageSize[0] / $imageSize[1] != 1) {
        $error = _('Image does not have square dimensions');
        goto error;
    }

    // change extension if it's not in the valid extensions list
    if (!in_array($extension, $validExts)) {
        $extension = $validExts[0];
    }
    $filename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME) . '.' . $extension;

    // Generate full, unique path for file
    $filename = makeUniqueFilename($config['absoluteurl'] . $config['img_dir'] . $filename);

    // Now everything is cool and the file can uploaded
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
        $error = _('Image was not uploaded successfully');
        goto error;
    }

    // Wait a few seconds so the upload can finish
    sleep(3);

    if (!$config->set('liveitem_default_cover', basename($filename), true)) {
        $error = _('Could not save configuration change for default live item default cover');
        goto error;
    }

    generateRSS();
    header('Location: live_cover.php');
    die();

    error:
}

$coverImage = $config['liveitem_default_cover'];
$isCustom = true;

if (empty($coverImage)) {
    $coverImage = $config['podcast_cover'];
    $isCustom = false;
}
$coverImage = $config['url'] . $config['img_dir'] . $coverImage;

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Default Live Cover') ?></title>
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
        <h1><?= _('Change Default Live Item Cover') ?></h1>
        <p><?= _('This cover art will be displayed for live items in supporting podcast players.') ?></p>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger" role="alert"><?= $error ?></div>
        <?php } ?>

        <h3><?= _('Current Cover') ?></h3>
        <img src="<?= $coverImage ?>" style="max-height: 350px; max-width: 350px;">
        <hr>

        <h3><?= _('Upload new cover') ?></h3>
        <form action="live_cover.php?upload=1" method="POST" enctype="multipart/form-data">
            <label for="file"><?= _('Select file') ?>:</label><br>
            <input type="file" id="file" name="file"><br><br>

            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _('Upload') ?>" class="btn btn-success">
        </form>

        <?php if ($isCustom) { ?>
            <h3><?= _('Reset default cover') ?></h3>
            <form action="live_cover.php?remove=1" method="POST">
                <p><?= _('This resets the live item cover to the same cover image for the podcast itself.') ?></p>
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                <input type="submit" value="<?= _('Reset') ?>" class="btn btn-danger">
            </form>
        <?php } ?>
    </div>
</body>

</html>
