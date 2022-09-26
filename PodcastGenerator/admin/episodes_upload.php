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

require_once(__DIR__ . '/../vendor/autoload.php');

use Lootils\Uuid\Uuid;
use PodcastGenerator\Models\Admin\EpisodeFormModel;

EpisodeFormModel::initialize($config);

$uploadDir = $config['absoluteurl'] . $config['upload_dir'];

if (count($_POST) > 0) {
    checkToken();

    // create and validate form model
    $model = EpisodeFormModel::fromForm($_GET, $_POST);
    if (!$model->validate()) {
        $error = _('Episode failed validation.');
        goto error;
    }

    if (!$model->saveEpisodeMediaFile($_FILES['file'])) {
        $error = _('Episode file failed validation.');
        goto error;
    }

    // Order of precedence for episode art:
    // 1. The episode cover uploaded on the form
    // 2. The cover art embedded in the episode mp3
    // 3. The show cover art

    // add the Episode Cover
    if (!empty($_FILES['cover']['name'])) {
        // User has uploaded a specific cover image
        $model->saveCoverImageFile($_FILES['cover']);
    } else {
        $model->saveCoverImageFromMediaFile();
    }

    if ($model->isValid()) {
        // make name for episode
        $targetFile = $uploadDir . $model->name();

        // create array for episode fields, with some basic prepopulated values
        $episode = [
            'episode' => [
                'guid' => $model->guid ?? Uuid::createV4(),
                'filename' => basename($targetFile),
                'filemtime' => $model->filemtime()
            ]
        ];

        // populate our episode object and save
        $model->applyChanges($episode);
        saveEpisode($episode, $targetFile);
        generateRSS();
        pingServices();

        // redirect to the episodes list page
        header('Location: ' . $config['url'] . 'admin/episodes_list.php');
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
} else {
    $model = EpisodeFormModel::forNewEpisode();
}

$viewMeta = (object) [
    'title' => _('Upload Episode'),
    'action' => 'episodes_upload.php',
    'success' => null,
    'error' => isset($error) ? $error : null,
    'newItem' => true,
];

include 'views/episode_edit_form.php';
episode_edit_form($model, $viewMeta, $config);
