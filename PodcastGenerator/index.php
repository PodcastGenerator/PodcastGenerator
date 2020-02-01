<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
session_start();
require 'core/include.php';
// Check if a password is set
if($config['podcastPassword'] != "") {
    if(!isset($_SESSION['password'])) {
        header('Location: auth.php');
        die(_('Authentication required'));
    }
}

// Backwards compatibility: Redirect pre-3.0 archive pages to
// categories.php.
if(isset($_GET['p'])) {
    if($_GET['p'] == 'archive') {
        $redirect_url = $config['url'] . 'categories.php?cat=' . $_GET['cat'];
        header('Location: ' . $redirect_url);
        die();
    }
}

$episodes = getEpisodes(null);

// When calling name
// Backwards comp
$link = str_replace('?', '', $config['link']);
$link = str_replace('=', '', $link);
$link = str_replace('$url."', '', $link);

if(strtolower($config['max_recent']) != 'all') {
    $episodes = array_slice($episodes, 0, $config['max_recent']);
}

$splitted_episodes = array_chunk($episodes, intval($config['episodeperpage']));
$episode_chunk = null;
if(isset($_GET['page'])) {
    $episode_chunk = $splitted_episodes[intval(($_GET['page']) - 1)];
}
else {
    $episode_chunk = $splitted_episodes[0];
}

// Some translation strings
$more = _('More');
$download = _('Download');
$editdelete = _('Edit/Delete (Admin)');
$categories = _('Categories');

$buttons = getButtons('./');
require $config['theme_path']."index.php";
?>