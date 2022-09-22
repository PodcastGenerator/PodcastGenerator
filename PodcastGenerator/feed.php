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
if ($config['podcastPassword'] != "") {
    session_start();
    if (!isset($_SESSION['password'])) {
        header('Location: auth.php');
        die(_('Authentication required'));
    }
}

header('Content-Type: application/xml');
usleep(10000);

if (isset($_GET['cat'])) {
    $xml = generateRssFeed($config, $_GET['cat']);
} else {
    $xml = file_get_contents($config['absoluteurl'] . $config['feed_dir'] . 'feed.xml');
}

print($xml);
