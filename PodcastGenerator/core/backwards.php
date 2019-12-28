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
    $varsToUnset = [
        'enablesocialnetworks',
        'dateformat',
        'enablehelphints',
        'cronAutoRegenerateRSScacheTime',
        'feed_iTunes_LINKS_Website',
        'feed_URL_replace'
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
    // Unset variables in config
    for ($i = 0; $i < sizeof($varsToUnset); $i++) {
        unsetConfig($absoluteurl . 'config.php', $varsToUnset[$i]);
    }
    // Remove tabs and copyright notice in the config
    $c = file_get_contents($absoluteurl . 'config.php');
    $c = str_replace("\t", '', $c);
    $c = str_replace("#################################################################

# Podcast Generator

# http://www.podcastgenerator.net

# developed by Alberto Betella

#

# Config.php file created automatically - v.2.7", "", $c);
    file_put_contents($absoluteurl . 'config.php', $c);
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
    // Update theme
    updateConfig($absoluteurl . 'config.php', 'theme_path', 'themes/default/');
    // Update version
    updateConfig($absoluteurl . 'config.php', 'podcastgen_version', $version);
    // Re-Enable $enablepgnewsinadmin
    updateConfig($absoluteurl . 'config.php', 'enablepgnewsinadmin', 'yes');
    sleep(0.5);
    header('Location: index.php');
    die();
}
