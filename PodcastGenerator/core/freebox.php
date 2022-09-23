<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################
function getFreebox($path = null)
{
    global $config;
    if ($config['freebox'] != 'yes') {
        return null;
    }
    return file_get_contents($path . 'freebox-content.txt');
}

function updateFreebox($content, $path = null)
{
    return file_put_contents($path . 'freebox-content.txt', $content);
}
