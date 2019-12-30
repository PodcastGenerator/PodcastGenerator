<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
function backwards_2_7_to_3_0($absoluteurl)
{
    global $config;
    global $version;
    // Quit if version is not 2.7
    if ($config['podcastgen_version'] != '2.7') {
        return;
    }
    // Delete useless stuff that is no longer supported/required
    $filesToDelete = [
        'loading.gif',
        'download.php',
        'LICENSE',
        'README',
        'core/functions.php',
        'core/includes.php',
        'core/language.php',
        'templates.php',
        'themes.php',
        'setup/checkconfigexistence.php',
        'setup/firstcreateconfig.php',
        'setup/set_path.php',
        'setup/set_permissions.php',
        'setup/step4.php',
        'components/locale/README.txt'
    ];
    $langsToDelete = [
        'cs_CS',
        'ee_ET',
        'en_EN',
        'es_ES',
        'fr_FR',
        'it_IT',
        'ja_JP',
        'ko_KR',
        'nb_NO',
        'nl_NL',
        'pl_PL',
        'pt_BR',
        'ru_RU',
        'sk_SK',
        'tr_TR',
        'zh_CN'
    ];
    $dirsToDelete = [
        'components/js',
        'components/lastRSS',
        'components/php-gettext',
        'core/admin',
        'setup/style',
    ];
    // Delete files
    for ($i = 0; $i < sizeof($filesToDelete); $i++) {
        if (file_exists($absoluteurl . $filesToDelete[$i])) {
            unlink($absoluteurl . $filesToDelete[$i]);
        }
    }
    // Delete directories
    for ($i = 0; $i < sizeof($dirsToDelete); $i++) {
        array_map('unlink', glob($absoluteurl . $dirsToDelete[$i] . "/*.*"));
        rmdir($absoluteurl . $dirsToDelete[$i]);
    }
    // Delete languages
    for ($i = 0; $i < sizeof($langsToDelete); $i++) {
        array_map('unlink', glob($absoluteurl . 'components/locale/' . $langsToDelete[$i] . "/LC_MESSAGES/*.*"));
        rmdir($absoluteurl . 'components/locale/' . $langsToDelete[$i] . '/LC_MESSAGES');
        rmdir($absoluteurl . 'components/locale/' . $langsToDelete[$i]);
    }
    if (in_array($config['scriptlang'], $langsToDelete)) {
        updateConfig($absoluteurl . 'config.php', 'scriptlang', 'en_US');
    }
    $config = getConfig($absoluteurl . 'config.php');
    file_put_contents($absoluteurl . 'config.php',
    $config = "<?php
\$podcastgen_version = \"3.0\"; // Version

\$first_installation = ".$config['first_installation'].";

\$installationKey = \"".$config['installationKey']."\";

\$scriptlang = \"".$config['scriptlang']."\";

\$url = \"".$config['url']."\";

\$absoluteurl = \"".$config['absoluteurl']."\"; // The location on the server

\$theme_path = \"themes/default/\";

\$username = \"".$config['username']."\";

\$userpassword = \"".str_replace('$', '\$', $config['userpassword'])."\";

\$max_upload_form_size = \"".$config['max_upload_form_size']."\"; //e.g.: \"30000000\" (about 30MB)

\$upload_dir = \"".$config['upload_dir']."\"; // \"media/\" the default folder (Trailing slash required). Set chmod 755

\$img_dir = \"".$config['upload_dir']."\"; // (Trailing slash required). Set chmod 755

\$feed_dir = \"".$config['feed_dir']."\"; // Where to create feed.xml (empty value = root directory). Set chmod 755

\$max_recent = ".$config['max_recent']."; // How many file to show in the home page

\$recent_episode_in_feed = \"".$config['recent_episode_in_feed']."\"; // How many file to show in the XML feed (1,2,5 etc.. or \"All\")

\$episodeperpage = ".$config['episodeperpage'].";

\$enablestreaming = \"".$config['enablestreaming']."\"; // Enable mp3 streaming? (\"yes\" or \"no\")

\$freebox = \"".$config['freebox']."\"; // enable freely customizable box

\$enablepgnewsinadmin = \"".$config['enablepgnewsinadmin']."\";

\$strictfilenamepolicy = \"".$config['strictfilenamepolicy']."\"; // strictly rename files (just characters A to Z and numbers) 

\$categoriesenabled = \"".$config['categoriesenabled']."\";

\$cronAutoIndex = ".$config['cronAutoIndex']."; //Auto Index New Episodes via Cron

\$cronAutoRegenerateRSS = ".$config['cronAutoRegenerateRSS']."; //Auto regenerate RSS via Cron

#####################
# XML Feed stuff

\$podcast_title = \"".$config['podcast_title']."\";

\$podcast_subtitle = \"".$config['podcast_subtitle']."\";

\$podcast_description = \"".$config['podcast_description']."\";

\$author_name = \"".$config['author_name']."\";

\$author_email = \"".$config['author_email']."\";

\$itunes_category[0] = \"".$config['itunes_category[0]']."\"; // iTunes categories (mainCategory:subcategory)
\$itunes_category[1] = \"".$config['itunes_category[1]']."\";
\$itunes_category[2] = \"".$config['itunes_category[2]']."\";

\$link = \"".$config['link']."\"; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)

\$feed_language = \"".$config['feed_language']."\";

\$copyright = \"".$config['copyright']."\";   // Your copyright notice (e.g CC-BY)

\$feed_encoding = \"".$config['feed_encoding']."\";

\$explicit_podcast = \"".$config['explicit_podcast']."\"; //does your podcast contain explicit language? (\"yes\" or \"no\")

// END OF CONFIG
");
    // Create buttons
    $buttons_xml = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
    <button>
        <name>RSS</name>
        <href>feed.xml</href>
        <class>btn btn-warning</class>
    </button>
    <button>
        <name>iTunes</name>
        <href>feed.xml</href>
        <class>btn btn-primary</class>
        <protocol>itpc</protocol>
    </button>
</PodcastGenerator>';
    file_put_contents($absoluteurl . 'buttons.xml', $buttons_xml);
    sleep(0.5);
    header('Location: index.php');
    die();
}
