<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################
function pingWebSub()
{
    // Get the global config
    global $config;
    // Exit early if no WebSub service isn't set up
    if (!$config['websub_server']) { return; }
    // Ping it
    $data = array(
        'hub.mode' => 'publish',
        'hub.url' => $config['url'] . $config['feed_dir'] . 'feed.xml'
    );
    $opts = array(
        CURLOPT_URL => $config['websub_server'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
    );
    try {
        $handle = curl_init();
        curl_setopt_array($handle, $opts);
        $result = curl_exec($handle);
    }
    catch (Exception $e) {
        // write a warning to stderr, but don't blow anything up
        error_log($e, 0);
    }
    finally {
        if ($handle) curl_close($handle);
    }
}

function pingServices()
{
    pingWebSub();
}
