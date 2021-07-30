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

generateRSS();
pingServices();
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config["podcast_title"]); ?> - <?= _('Regenerate Feed') ?></title>
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
        <h1 style='color: green;'><?= _('Successfully regenerated RSS feed') ?></h1>
        <a href="index.php"><?= _('Return') ?></a>
    </div>
</body>

</html>