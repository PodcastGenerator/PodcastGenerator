<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require 'checkLogin.php';
require '../core/include_admin.php';

// Get all themes
$themes = array();
$themes_in_dir = glob('../themes' . '/*', GLOB_ONLYDIR);
$realthemes = array();
for ($i = 0; $i < sizeof($themes_in_dir); $i++) {
    array_push($themes, [substr($themes_in_dir[$i], 3) . '/', json_decode(file_get_contents($themes_in_dir[$i] . '/theme.json'))]);
}
// Check if the theme is compatible
for ($i = 0; $i < sizeof($themes); $i++) {
    if (in_array(strval($version), $themes[$i][1]->pg_versions)) {
        array_push($realthemes, $themes[$i]);
    }
}

$themes = $realthemes;
unset($realthemes);

if (isset($_GET['change'])) {
    if ($_GET['change'] > sizeof($themes)) {
        goto error;
    }
    updateConfig('../config.php', 'theme_path', $themes[$_GET['change']][0]);
    header('Location: theme_change.php');
    die();

    error: echo "";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Theme Change') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?php echo _('Change theme'); ?></h1>
        <small><?php echo sprintf('You can upload themes to your %s folder', '<code>themes/</code>'); ?></small>
        <h3><?php echo _('Installed themes'); ?></h3>
        <div class="row">
            <?php
            for ($i = 0; $i < sizeof($themes); $i++) {
                $json = $themes[$i][1];
                echo '<div class="col-lg-6">';
                echo '<div class="card">';
                echo '<img src="../' . $themes[$i][0] . 'preview.png" class="card-img-top">';
                echo '<div class="card-body">';
                echo '<h3>' . htmlspecialchars($json->name) . '</h3>';
                echo '<p>Description: ' . htmlspecialchars($json->description) . '</p>';
                echo '<p>Author: ' . htmlspecialchars($json->author) . '</p>';
                echo '<p>Theme Version: ' . htmlspecialchars($json->version) . '</p>';
                echo '<p>Credits: ' . htmlspecialchars($json->credits) . '</p>';
                echo '<hr>';
                // Check if this theme is the used theme and or not
                if ($themes[$i][0] == htmlspecialchars($config['theme_path'])) {
                    echo '<small>' . _('This theme is currently in use') . '</small>';
                } else {
                    echo '<a href="theme_change.php?change=' . $i . '" class="btn btn-success">' . _('Switch theme') . '</a>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>

</html>