<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require 'core/include.php';
// Kill the connection if categories are disabled
if(strtolower($config['categoriesenabled']) != 'yes') {
    header('Location: '.$config['indexfile']);
    die();
}
// Check for password
if($config['podcastPassword'] != "") {
    session_start();
    if(!isset($_SESSION['password'])) {
        header('Location: auth.php');
        die(_('Authentication required'));
    }
}
$categories_xml = simplexml_load_file('categories.xml');
$episodes = null;
if(isset($_GET['cat'])) {
    $episodes = getEpisodes($_GET['cat'], $config);
}
$episode_chunk = $episodes;

// Backwards comp
$link = str_replace('?', '', $config['link']);
$link = str_replace('=', '', $link);
$link = str_replace('$url."', '', $link);

// Some translation strings
$more = _('More');
$download = _('Download');
$editdelete = _('Edit/Delete (Admin)');
$filetype = _('Filetype');
$size = _('Size');
$duration = _('Duration');
$categories = _('Categories');

$buttons = getButtons('./');
require $config['theme_path'].'categories.php';
?>