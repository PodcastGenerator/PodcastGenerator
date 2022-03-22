<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

// This file is intended to be only used in the admin directory
if (!file_exists('../config.php')) {
    header('Location: ../setup/');
    die();
}

include 'Configuration.php';
include 'misc/globs.php';

$config = PodcastGenerator\Configuration::load('../config.php');
if ($config['podcastgen_version'] != $version) {
    // Backwards compatibility
    include 'backwards.php';
    backwards_3_1_to_3_3($config['absoluteurl']);
    die("Please refresh");
}

include 'episodes.php';
include 'feed_generator.php';
include 'pinger.php';
include 'buttons.php';
include 'customtags.php';
include 'freebox.php';
// Until Podcast Generator 3.0 passwords were stored in MD5, which is insecure since 2005
// This file is wizard to convert old password to a more secure algorithm
// Load useful functions
include 'misc/functions.php';
include 'users.php';
// Load HTML helper functions
include 'html_helpers.php';
// Load translations
include 'translation.php';
