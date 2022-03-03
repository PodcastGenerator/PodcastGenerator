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
for ($i = 0; $i < count($themes_in_dir); $i++) {
    array_push($themes, [substr($themes_in_dir[$i], 3) . '/', json_decode(file_get_contents($themes_in_dir[$i] . '/theme.json'))]);
}
// Check if the theme is compatible
for ($i = 0; $i < count($themes); $i++) {
    if (in_array(strval($version), $themes[$i][1]->pg_versions)) {
        array_push($realthemes, $themes[$i]);
    }
}

$themes = $realthemes;
unset($realthemes);

if (isset($_GET['change'])) {
    checkToken();
    if ($_GET['change'] > count($themes)) {
        goto error;
    }
    updateConfig('../config.php', 'theme_path', $themes[$_GET['change']][0]);
    header('Location: theme_change.php');
    die();

    error:
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']) ?> - <?= _('Theme Change') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $config['url'] ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?= _('Change theme') ?></h1>
        <small><?= sprintf(_('You can upload themes to your %s folder'), '<code>themes/</code>') ?></small>
        <h3><?= _('Installed themes') ?></h3>
        <div class="row">
            <?php if (count($themes) == 0) { ?>
                <div class="col-lg-6"><p><?= _('No compatible themes installed') ?></p></div>
            <?php } else { ?>
                <?php for ($i = 0; $i < count($themes); $i++) {
        $json = $themes[$i][1]; ?>
                    <div class="col-lg-6">
                        <div class="card">
                            <img src="../<?= $themes[$i][0] ?>preview.png" class="card-img-top">
                            <div class="card-body">';
                                <h3><?= htmlspecialchars($json->name) ?></h3>
                                <p>Description: <?= htmlspecialchars($json->description) ?></p>
                                <p>Author: <?= htmlspecialchars($json->author) ?></p>
                                <p>Theme Version: <?= htmlspecialchars($json->version) ?></p>
                                <p>Credits: <?= htmlspecialchars($json->credits) ?></p>
                                <hr>
                                <?php if ($themes[$i][0] == htmlspecialchars($config['theme_path'])) { /* Check if this theme is the used theme */ ?>
                                    <small><?= _('This theme is currently in use') ?></small>';
                                <?php } else { ?>
                                    <form action="theme_change.php?change=<?= $i ?>" method="POST">
                                        <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                        <input class="btn btn-success" type="submit" value="<?= _('Switch theme') ?>">
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php
    } ?>
            <?php } ?>
        </div>
    </div>
</body>

</html>
