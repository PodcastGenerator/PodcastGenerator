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
    if (!$config['websub_server']) {
        return;
    }
    // Log an error and exit early if cURL is unavailable
    if (!function_exists('curl_init')) {
        error_log("cURL functions are not available.", 0);
        return false;
    }
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
        if (function_exists('curl_setopt_array')) {
            curl_setopt_array($handle, $opts);
        } else {
            foreach ($opts as $opt => $val) {
                curl_setopt($handle, $opt, $val);
            }
        }
        return curl_exec($handle);
    } catch (Exception $e) {
        // write a warning to stderr, but don't blow anything up
        error_log($e, 0);
        return false;
    } finally {
        if ($handle) {
            curl_close($handle);
        }
    }
}

function pingPodcastIndex()
{
    // Get the global config
    global $config, $version;

    // Exit early if Podcast Index isn't set up
    if (!$config['pi_api_key'] || !$config['pi_api_secret'] || !$config['pi_podcast_id']) {
        return;
    }

    // Set up our client
    require_once(__DIR__ . '/../vendor/autoload.php');
    $client = new PodcastIndex\Client([
        'app' => 'PodcastIndex/' . $version,
        'key' => $config['pi_api_key'],
        'secret' => $config['pi_api_secret']
    ]);

    try {
        $client->get('hub/pubnotify', array('id' => $config['pi_podcast_id']));
    } catch (Exception $e) {
        // write a warning to stderr, but don't blow anything up
        error_log($e, 0);
        return false;
    }
}

function pingServices()
{
    pingWebSub();
    pingPodcastIndex();
}
