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
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']) ?> - Admin</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        iframe {
            width: 1000px;
            height: 500px;
        }
    </style>
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
        <?php //# 'Podcast Generator' is a proper name. ?>
        <h1><?= _('Welcome to your Podcast Generator Admin Interface') ?></h1>
        <?php if ($config['enablepgnewsinadmin'] == 'yes') { ?>
            <iframe width="100%" src='<?= $news_url ?>'></iframe>
        <?php } ?>

        <div class="row align-items-end justify-content-end">
            <div class="col-3">
                <small class="text-muted">
                    <?php //# 'Podcast Generator' is a proper name. ?>
                    <?= sprintf(_('Podcast Generator v%s'), $config['podcastgen_version']) ?>
                </small>
            </div>
        </div>
    </div>
</body>

</html>