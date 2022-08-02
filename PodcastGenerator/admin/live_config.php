<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

require 'checkLogin.php';
require '../core/include_admin.php';

require_once(__DIR__ . '/../vendor/autoload.php');

if (isset($_GET['edit'])) {
    checkToken();

    $changed = false;
    foreach ($_POST as $key => $value) {
        $changed = true;
        $config[$key] = $value;
    }
    if ($config->save()) {
        header('Location: live_config.php');
        die();
    } else {
        $error = _('Could not save configuration changes');
    }
}

$mimetypesXml = simplexml_load_file($config['absoluteurl'] . 'components/supported_media/supported_media.xml');
$mimetypes = array();
foreach ($mimetypesXml->mediaFile as $mediaFile) {
    $mimetype = (string) $mediaFile->mimetype;
    $primary = substr($mimetype, 0, 6);
    if ($primary == 'audio/' || $primary == 'video/') {
        $mimetypes[] = $mimetype;
    }
}
$mimetypes = array_unique($mimetypes);

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Live Items') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        .txt {
            width: 100%;
        }
    </style>
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
        <h1><?= _('Change Live Items Configuration') ?></h1>

        <?php if (isset($error)) { ?>
            <p style="color: red;"><?= $error ?></p>
        <?php } ?>

        <form action="live_config.php?edit=1" method="POST">

            <label for="liveitems_default_stream"><?= _('Default live stream URL') ?>:</label><br>
            <small>
                <?= _('The URL of the live stream normally used for live episode recordings.') ?>
            </small>
            <br>
            <input type="url" id="liveitems_default_stream" name="liveitems_default_stream"
                   value="<?= htmlspecialchars($config['liveitems_default_stream']) ?>" required
                   style="width: 100%;">
            <br>
            <hr>

            <label for="liveitems_default_mimetype"><?= _('Default live stream MIME type') ?>:</label><br>
            <small>
                <?= _('The MIME content type for the above live stream.') ?>
            </small>
            <br>
            <select id="liveitems_default_mimetype" name="liveitems_default_mimetype">
                <?php foreach ($mimetypes as $mime) { ?>
                    <option value="<?= $mime ?>" <?= selectedAttr($config['liveitems_default_mimetype'], $mime) ?>>
                        <?= $mime ?>
                    </option>
                <?php } ?>
            </select>
            <br>
            <hr>

            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>
