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
$themes = array_map(
    function ($item) {
        return (object) [
            'path' => substr($item, 3) . '/',
            'json' => json_decode(file_get_contents($item) . '/theme.json')
        ];
    },
    glob('../themes/*', GLOB_ONLYDIR)
);

// Check if the theme is compatible
$themes = array_filter(
    $themes,
    function ($item) {
        global $version;
        return in_array(strval($version), $item->json->pg_versions);
    }
);

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
                <?php for ($i = 0; $i < count($themes); $i++) { ?>
                    <div class="col-lg-6">
                        <div class="card">
                            <img src="../<?= $themes[$i]->path ?>preview.png" class="card-img-top">
                            <div class="card-body">
                                <h3><?= htmlspecialchars($themes[$i]->json->name) ?></h3>
                                <p>Description: <?= htmlspecialchars($themes[$i]->json->description) ?></p>
                                <p>Author: <?= htmlspecialchars($themes[$i]->json->author) ?></p>
                                <p>Theme Version: <?= htmlspecialchars($themes[$i]->json->version) ?></p>
                                <p>Credits: <?= htmlspecialchars($themes[$i]->json->credits) ?></p>
                                <hr>
                                <?php if ($themes[$i]->path == htmlspecialchars($config['theme_path'])) { ?>
                                    <small><?= _('This theme is currently in use') ?></small>
                                <?php } else { ?>
                                    <form action="theme_change.php?change=<?= $i ?>" method="POST">
                                        <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                        <input class="btn btn-success" type="submit" value="<?= _('Switch theme') ?>">
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</body>

</html>
