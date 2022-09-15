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

use PodcastGenerator\Models\Admin\EpisodeFormModel;

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

EpisodeFormModel::initialize($config);

$episode = loadEpisode($targetfile, $config);

if (isset($_GET['delete'])) {
    // Delete episode
    checkToken();

    deleteEpisode($targetfile, $config);
    generateRSS();
    pingServices();

    header('Location: ' . $config['url'] . $config['indexfile']);
    die();
} else if (count($_POST) > 0) {
    // Edit episode
    checkToken();

    // create and validate form model
    $model = EpisodeFormModel::fromForm($_GET, $_POST);
    if (!$model->validate()) {
        $error = _('Episode failed validation.');
        goto error;
    }

    $link = str_replace('?', '', $config['link']);
    $link = str_replace('=', '', $link);
    $link = str_replace('$url', '', $link);

    // build categories list from post data
    $categories = array();
    for ($i = 0; $i < 3; $i++) {
        $categories[$i] = isset($_POST['category'][$i])
            ? $_POST['category'][$i]
            : ($i == 0 ? 'uncategorized' : '');
    }

    // Process the cover image, if one was provided
    $coverImage = '';
    if (!empty($_FILES['cover']['name'])) {
        $model->saveCoverImageFile($_FILES['cover']);
    }

    if ($model->isValid()) {
        // update episode and save
        $model->applyChanges($episode);
        saveEpisode($episode, $targetfile);
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
    $model = EpisodeFormModel::fromEpisode($episode);
}

$viewMeta = (object) [
    'title' => _('Edit Episode'),
    'action' => 'episodes_edit.php?name=' . htmlspecialchars($model->name()),
    'success' => $success,
    'error' => isset($error) ? $error : null,
    'newItem' => false,
];

include 'views/episode_edit_form.php';
episode_edit_form($model, $viewMeta, $config);
