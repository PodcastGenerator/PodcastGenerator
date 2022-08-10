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

$mimetypes = getSupportedMimeTypes($config, ['audio', 'video']);

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
            <div class="row">
                <div class="col-6">
                    <h2><?= _('Display and feed configuration') ?></h2>
                    <hr>

                    <div class="form-group">
                        <label for="liveitems_max_pending"><?= _('Maximum pending items') ?></label><br>
                        <small class="form-description">
                            <?= _('The maximum number of pending live items to show on the site and feed.') ?>
                        </small>
                        <input id="liveitems_max_pending" name="liveitems_max_pending"
                               type="number" min="0" step="1"
                               value="<?= $config['liveitems_max_pending'] ?>"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="liveitems_latest_pending"><?= _('Latest pending item date') ?></label><br>
                        <small class="form-description">
                            <?= _('Do not show pending live items that are more than this number of days away.') ?>
                        </small>
                        <input id="liveitems_latest_pending" name="liveitems_latest_pending"
                               type="number" min="0" step="1"
                               value="<?= $config['liveitems_latest_pending'] ?>"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="liveitems_max_ended"><?= _('Maximum ended items') ?></label><br>
                        <small class="form-description">
                            <?= _('The maximum number of ended live items to show on the site and feed.') ?>
                        </small>
                        <input id="liveitems_max_ended" name="liveitems_max_ended"
                               type="number" min="0" step="1"
                               value="<?= $config['liveitems_max_ended'] ?>"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="liveitems_earliest_ended"><?= _('Earliest ended item date') ?></label><br>
                        <small class="form-description">
                            <?= _('Do not show ended live items that are more than this number of days in the past.') ?>
                        </small>
                        <input id="liveitems_earliest_ended" name="liveitems_earliest_ended"
                               type="number" min="0" step="1"
                               value="<?= $config['liveitems_earliest_ended'] ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="col-6">
                    <h2><?= _('Live item defaults') ?></h2>
                    <hr>

                    <div class="form-group">
                        <label for="liveitems_default_stream"><?= _('Default live stream URL') ?>:</label><br>
                        <small class="form-description">
                            <?= _('The URL of the live stream normally used for live episode recordings.') ?>
                        </small>
                        <input id="liveitems_default_stream" name="liveitems_default_stream"
                               type="url" required
                               value="<?= htmlspecialchars($config['liveitems_default_stream']) ?>"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="liveitems_default_mimetype"><?= _('Default live stream MIME type') ?>:</label><br>
                        <small class="form-description">
                            <?= _('The MIME content type for the above live stream.') ?>
                        </small>
                        <br>
                        <select id="liveitems_default_mimetype" name="liveitems_default_mimetype" class="form-control">
                            <?php foreach ($mimetypes as $mime) { ?>
                                <option value="<?= $mime ?>" <?= selectedAttr($config['liveitems_default_mimetype'], $mime) ?>>
                                    <?= $mime ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                    <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success">
                </div>
            </div>
        </form>
    </div>
</body>

</html>
