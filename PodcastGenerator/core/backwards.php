<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

function backwards_3_1_to_3_3($absoluteurl)
{
    session_start();
    session_destroy();
    global $config;
    global $version;

    $currentVersion = $config['podcastgen_version'];

    $upgrading = false;

    // Upgrading from 3.1 -> 3.2
    if ($currentVersion == '3.1' || substr($currentVersion, 0, 4) == '3.1.') {
        $upgrading = true;

        // Fix up podcast language
        $languages = simplexml_load_file('../components/supported_languages/podcast_languages.xml');

        $matchedLanguage = null;
        foreach ($languages as $lang) {
            if ($config['feed_language'] == $lang->code) {
                $matchedLanguage = $lang;
                break;
            }
        }

        if ($matchedLanguage == null) {
            // Reset back to default of English, since we don't have a clue
            // about what language this should have been.
            $config['feed_language'] = 'en';
        } elseif (property_exists($matchedLanguage, 'alias')) {
            // Change to correct language code when a bad / obsolete code has
            // been used.
            $config['feed_language'] = $matchedLanguage->alias;
        }

        // Default sort method for episodes in the RSS feed
        if (!isset($config['feed_sort'])) {
            $config['feed_sort'] = 'timestamp';
        }

        // Other defaults for config settings added in 3.2
        if (!isset($config['customtagsenabled'])) {
            $config['customtagsenabled'] = 'no';
        }
        if (!isset($config['timezone'])) {
            $config['timezone'] = '';
        }
        if (!isset($config['podcast_guid'])) {
            $config['podcast_guid'] = '';
        }
        if (!isset($config['podcast_cover'])) {
            $config['podcast_cover'] = 'itunes_image.jpg';
        }
        if (!isset($config['feed_locked'])) {
            $config['feed_locked'] = '';
        }
        if (!isset($config['websub_server'])) {
            $config['websub_server'] = '';
        }
        if (!isset($config['pi_api_key'])) {
            $config['pi_api_key'] = '';
        }
        if (!isset($config['pi_api_secret'])) {
            $config['pi_api_secret'] = '';
        }
        if (!isset($config['pi_podcast_id'])) {
            $config['pi_podcast_id'] = 0;
        }
    }

    // Upgrading from 3.2 -> 3.3
    if ($upgrading || $currentVersion == '3.2' || substr($currentVersion, 0, 4) == '3.2.') {
        $upgrading = true;

        // Live items are disabled if upgrading from a version without them
        // Admins can turn on this feature on the live items management page
        $liveitems_enabled = isset($config['liveitems_enabled'])
            ? $config['liveitems_enabled']
            : 'no';
        $liveitems_default_stream = isset($config['liveitems_default_stream'])
            ? $config['liveitems_default_stream']
            : '';
        $liveitems_default_mimetype = isset($config['liveitems_default_mimetype'])
            ? $config['liveitems_default_mimetype']
            : '';

        $liveitems_max_pending = isset($config['liveitems_max_pending'])
            ? (int) $config['liveitems_max_pending']
            : 2; // only show next two pending live items by default
        $liveitems_latest_pending = isset($config['liveitems_latest_pending'])
            ? (int) $config['liveitems_latest_pending']
            : 14; // show up to two weeks of pending live items by default
        $liveitems_max_ended = isset($config['liveitems_max_ended'])
            ? (int) $config['liveitems_max_ended']
            : 1; // only show most recent ended live item by default
        $liveitems_earliest_ended = isset($config['liveitems_earliest_ended'])
            ? (int) $config['liveitems_earliest_ended']
            : 7; // show up to one week of ended live items by default

        $livefile = isset($config['livefile']) ? $config['livefile'] : 'live.php';
    }

    if (!$upgrading) {
        // If we have no upgrades, no need to go further
        return;
    }

    $config_php = "<?php
\$podcastgen_version = '" . $version . "'; // Version

\$first_installation = " . $config['first_installation'] . ";

\$installationKey = '" . $config['installationKey'] . "';

\$scriptlang = '" . $config['scriptlang'] . "';

\$url = '" . $config['url'] . "';

\$absoluteurl = '" . $config['absoluteurl'] . "'; // The location on the server

\$theme_path = '" . $config['theme_path'] . "';

\$upload_dir = '" . $config['upload_dir'] . "'; // 'media/' the default folder (Trailing slash required). Set chmod 755

\$img_dir = '" . $config['img_dir'] . "'; // (Trailing slash required). Set chmod 755

\$feed_dir = '" . $config['feed_dir'] . "'; // Where to create feed.xml (empty value = root directory). Set chmod 755

\$max_recent = " . $config['max_recent'] . "; // How many file to show in the home page

\$recent_episode_in_feed = '" . $config['recent_episode_in_feed'] . "'; // How many file to show in the XML feed (1,2,5 etc.. or 'All')

\$episodeperpage = " . $config['episodeperpage'] . ";

\$enablestreaming = '" . $config['enablestreaming'] . "'; // Enable mp3 streaming? ('yes' or 'no')

\$freebox = '" . $config['freebox'] . "'; // enable freely customizable box

\$enablepgnewsinadmin = '" . $config['enablepgnewsinadmin'] . "';

\$strictfilenamepolicy = '" . $config['strictfilenamepolicy'] . "'; // strictly rename files (just characters A to Z and numbers) 

\$categoriesenabled = '" . $config['categoriesenabled'] . "';

\$cronAutoIndex = " . $config['cronAutoIndex'] . "; //Auto Index New Episodes via Cron

\$cronAutoRegenerateRSS = " . $config['cronAutoRegenerateRSS'] . "; //Auto regenerate RSS via Cron

\$indexfile = '" . $config['indexfile'] . "';    // Path of the index file

\$podcastPassword = '" . $config['podcastPassword'] . "';       // Password to protect the podcast generator webpages, this will NOT protect the audio or XML files. Leave blank to disable.

\$customtagsenabled = '" . $config['customtagsenabled'] . "';   // Advanced functionality for custom RSS tag input

\$timezone = '" . $config['timezone'] . "';              // Timezone used for displaying dates and times

#####################
# XML Feed stuff

\$podcast_guid = '" . $config['podcast_guid'] . "'; // Globally unique identifier for your podcast

\$podcast_title = '" . $config['podcast_title'] . "';

\$podcast_subtitle = '" . $config['podcast_subtitle'] . "';

\$podcast_description = '" . $config['podcast_description'] . "';

\$podcast_cover = '" . $config['podcast_cover'] . "';

\$author_name = '" . $config['author_name'] . "';

\$author_email = '" . $config['author_email'] . "';

# The e-mail of the technical admin of the podcast
\$webmaster = '" . $config['author_email'] . "';

\$itunes_category[0] = '" . $config['itunes_category[0]'] . "'; // iTunes categories (mainCategory:subcategory)
\$itunes_category[1] = '" . $config['itunes_category[1]'] . "';
\$itunes_category[2] = '" . $config['itunes_category[2]'] . "';

\$link = '" . $config['link'] . "'; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)

\$feed_language = '" . $config['feed_language'] . "';

\$feed_sort = '" . $config['feed_sort'] . "'; // sort method used to order episodes in the feed (by timestamp or by season/episode number)

\$feed_locked = '" . $config['feed_locked'] . "'; // podcast:locked status ('yes', 'no', '' for off)

\$copyright = '" . $config['copyright'] . "';   // Your copyright notice (e.g CC-BY)

\$feed_encoding = '" . $config['feed_encoding'] . "';

\$explicit_podcast = '" . $config['explicit_podcast'] . "'; //does your podcast contain explicit language? ('yes' or 'no')

\$users_json = '" . $config['users_json'] . "';

#####################
# WebSub

\$websub_server = '" . $config['websub_server'] . "';

#####################
# Podcast Index

\$pi_api_key = '" . $config['pi_api_key'] . "';
\$pi_api_secret = '" . $config['pi_api_secret'] . "';

\$pi_podcast_id = " . $config['pi_podcast_id'] . "; // is the podcast in Podcast Index? This is its show ID there.

#####################
# Live Items

\$liveitems_enabled = '" . $liveitems_enabled . "';

\$liveitems_default_stream = '" . $liveitems_default_stream . "';

\$liveitems_default_mimetype = '" . $liveitems_default_mimetype . "';

\$liveitems_max_pending = " . $liveitems_max_pending . ";

\$liveitems_latest_pending = " . $liveitems_latest_pending . ";

\$liveitems_max_ended = " . $liveitems_max_ended . ";

\$liveitems_earliest_ended = " . $liveitems_earliest_ended . ";

\$livefile = '" . $livefile . "';    // Path of the live index file

// END OF CONFIG
";
    file_put_contents($absoluteurl . 'config.php', $config_php);

    if (!file_exists($absoluteurl . 'customtags.xml')) {
        $catfile = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
    <customfeedtags><![CDATA[]]></customfeedtags>
</PodcastGenerator>';
        file_put_contents($absoluteurl . 'customtags.xml', $catfile);
    }
}
