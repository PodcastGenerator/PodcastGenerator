<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################
function backwards_3_1_to_3_2_4($absoluteurl)
{
    session_start();
    session_destroy();
    global $config;
    global $version;
    // Quit if version is not 3.1.x or 3.2
    if (
        !(
            $config['podcastgen_version'] == '3.1'
            || substr($config['podcastgen_version'], 0, 4) == '3.1.'
            || $config['podcastgen_version'] == '3.2'
            || $config['podcastgen_version'] == '3.2.1'
            || $config['podcastgen_version'] == '3.2.2'
            || $config['podcastgen_version'] == '3.2.3'
        )
    ) {
        return;
    }

    $languages = simplexml_load_file('../components/supported_languages/podcast_languages.xml');

    $matchedLanguage = null;
    foreach ($languages as $lang) {
        if ($config['feed_language'] == $lang->code) {
            $matchedLanguage = $lang;
            break;
        }
    }

    if ($matchedLanguage == null) {
        // Reset back to default of English, since we don't have a clue about
        // what language this should have been.
        $config['feed_language'] = 'en';
    } elseif (property_exists($matchedLanguage, 'alias')) {
        // Change to correct language code when a bad / obsolete code has been
        // used.
        $config['feed_language'] = $lang->alias;
    }

    // Default sort method for episodes in the RSS feed
    if (!isset($config['feed_sort'])) {
        $config['feed_sort'] = 'timestamp';
    }

    // Ensure pi_podcast_id is integer value
    if (!isset($config['pi_podcast_id']) || !is_int($config['pi_podcast_id'])) {
        $config['pi_podcast_id'] = 0;
    }

    $config_php = "<?php
\$podcastgen_version = '3.2.3'; // Version

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

\$indexfile = 'index.php';    // Path of the index file

\$podcastPassword = '';       // Password to protect the podcast generator webpages, this will NOT protect the audio or XML files. Leave blank to disable.

\$customtagsenabled = 'no';   // Advanced functionality for custom RSS tag input

\$timezone = '';              // Timezone used for displaying dates and times

#####################
# XML Feed stuff

\$podcast_guid = ''; // Globally unique identifier for your podcast

\$podcast_title = '" . $config['podcast_title'] . "';

\$podcast_subtitle = '" . $config['podcast_subtitle'] . "';

\$podcast_description = '" . $config['podcast_description'] . "';

\$podcast_cover = '" . (isset($config['podcast_cover']) ? $config['podcast_cover'] : 'itunes_image.jpg') . "';

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

\$feed_locked = ''; // podcast:locked status ('yes', 'no', '' for off)

\$copyright = '" . $config['copyright'] . "';   // Your copyright notice (e.g CC-BY)

\$feed_encoding = '" . $config['feed_encoding'] . "';

\$explicit_podcast = '" . $config['explicit_podcast'] . "'; //does your podcast contain explicit language? ('yes' or 'no')

\$users_json = '" . $config['users_json'] . "';

#####################
# WebSub

\$websub_server = '" . $config['websub_server'] .  "';

#####################
# Podcast Index

\$pi_api_key = '" . $config['pi_api_key'] .  "';
\$pi_api_secret = '" . $config['pi_api_secret'] .  "';

\$pi_podcast_id = " . $config['pi_podcast_id'] . "; // is the podcast in Podcast Index? This is its show ID there.

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
