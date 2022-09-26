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

use Lootils\Uuid\Uuid;
use PodcastGenerator\Models\Admin\LiveItemFormModel;

LiveItemFormModel::initialize($config);

$uploadDir = $config['absoluteurl'] . $config['upload_dir'];
$imagesDir = $config['absoluteurl'] . $config['img_dir'];

if (count($_POST) > 0) {
    // Edit live item
    checkToken();

    // create and validate form model
    $model = LiveItemFormModel::fromForm($_GET, $_POST);
    if (!$model->validate()) {
        $error = _('Live item failed validation.');
        goto error;
    }

    // Process the cover image, if one was provided
    if (!empty($_FILES['cover']['name'])) {
        $model->saveCoverImageFile($_FILES['cover']);
    }

    if ($model->isValid()) {
        // make name for live item
        $targetfile = makeLiveItemFilename($uploadDir, $model->startTime(), $model->title);

        // create array for live item fields, with some basic prepopulated values
        $liveItem = [
            'guid' => Uuid::createV4(),
            'filename' => basename($targetfile)
        ];

        // populate our live item object and save
        $model->applyChanges($liveItem);
        saveLiveItem($liveItem, $targetfile);
        generateRSS();
        pingServices();

        // redirect to the live item list page
        header('Location: ' . $config['url'] . 'admin/live_list.php');
        die();
    } else {
        $modelErr = $model->validationFor(''); // non-specific error messages
        if (!empty($modelErr) && !empty($error)) {
            $error .= ' ' . $modelErr;
        } elseif (!empty($modelErr)) {
            $error = $modelErr;
        }
    }

    error:
} else { // not a POST, or no form fields
    $model = LiveItemFormModel::forNewLiveItem();
}

$viewMeta = (object) [
    'title' => _('Create Live Item'),
    'action' => 'live_create.php',
    'success' => null,
    'error' => isset($error) ? $error : null,
    'newItem' => true,
];

include 'views/live_edit_form.php';
live_edit_form($model, $viewMeta, $config);
