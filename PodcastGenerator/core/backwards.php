<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
function backwards_3_0_to_3_1($absoluteurl)
{
    global $config;
    global $version;
    // Quit if version is not 3.0
    if($config['podcastgen_version'] != '3.0') {
        return;
    }
}