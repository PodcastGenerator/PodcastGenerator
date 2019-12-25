<?php
function backwards_2_7_to_3_0($absoluteurl) {
    global $config;
    global $version;
    // Quit if version is not 2.7
    if($config['podcastgen_version'] != '2.7') {
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
        'setup/step3.php',
        'setup/step4.php'
    ];
    $dirsToDelete = [
        'components/js',
        'components/lastRSS',
        'components/locale',
        'components/php-gettext',
        'core/admin',
        'setup/style'
    ];
    // Delete files
    for($i = 0; $i < sizeof($filesToDelete); $i++) {
        unlink($absoluteurl . $filesToDelete[$i]);
    }
    // Delete directories
    for($i = 0; $i < sizeof($dirsToDelete); $i++) {
        array_map('unlink', glob($absoluteurl . $dirsToDelete[$i]."/*.*"));
        rmdir($absoluteurl . $dirsToDelete[$i]);
    }
    // Update theme
    updateConfig('config.php', 'theme_path', 'themes/default/');
    // Update version
    updateConfig($absoluteurl . 'config.php', 'podcastgen_version', $version);
}