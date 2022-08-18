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

use PodcastGenerator\Models\Admin\LiveItemFormModel;

$success = false; // no success until we have it

if (!isset($_GET['name'])) {
    die(_('No name given'));
}

checkPath($_GET['name']);

$uploadDir = $config['absoluteurl'] . $config['upload_dir'];
$imagesDir = $config['absoluteurl'] . $config['img_dir'];

$targetfile = $uploadDir . $_GET['name'];
$targetfile_without_ext = $uploadDir . pathinfo($targetfile, PATHINFO_FILENAME);

if (!file_exists($targetfile)) {
    die(_('Episode does not exist'));
}

LiveItemFormModel::initialize($config);

$liveItem = loadLiveItem($targetfile, $config);

if (isset($_GET['delete'])) {
    // Delete live item
    checkToken();

    deleteLiveItem($targetfile, $config);
    generateRSS();
    pingServices();

    header('Location: ' . $config['url'] . $config['indexfile']);
    die();
} elseif (count($_POST) > 0) {
    // Edit live item
    checkToken();

    // create and validate form model
    $model = LiveItemFormModel::fromForm($_GET, $_POST);
    if (!$model->validate()) {
        $error = _('Live item failed validation.');
        goto error;
    }

    // Process the cover image, if one was provided
    $coverImage = '';
    if (!empty($_FILES['cover']['name'])) {
        $model->saveCoverImageFile($_FILES['cover']);
    }

    if ($model->isValid()) {
        // update live item and save
        $model->apply($liveItem);
        saveLiveItem($liveItem, $targetfile);
        generateRSS();
        pingServices();

        $success = true;
    } else {
        $modelErr = $model->validationFor(''); // non-specific error messages
        if (!empty($modelErr) && !empty($error)) {
            $error .= ' ' . $modelErr;
        } elseif (!empty($modelErr)) {
            $error = $modelErr;
        }
    }

    error:
}

if (!isset($model) || $model == null) {
    $model = LiveItemFormModel::fromLiveItem($liveItem);
}

include 'views/live_edit_form.php';
live_edit_form($model, $config, _('Edit Live Item'), $success, $error);
