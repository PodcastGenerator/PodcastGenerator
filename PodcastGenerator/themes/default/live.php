<?php

// Translation strings
$more = _('More');
$editdelete = _('Edit/Delete (Admin)');
$directLink = _('Direct Stream Link');
$search = _('Search');
$categories = _('Categories');
$livestream = $config['liveitems_name'] != '' ? $config['liveitems_name'] : _('Live Stream');

// Date/time format string
$timeFmt = 'Y-m-d H:i';

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config["podcast_title"]) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/bootstrap.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/custom.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/font-awesome.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/dark.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <?php include "navbar.php"; ?>
    <br>
    <div class="container">
        <?php
            include "js.php";
            include "jumbotron.php";
        ?>
        <div class="row">
        <?php if (isset($_GET['name'])) {
            include 'singlelive.php';
        } elseif (isset($_GET['all'])) {
            include 'listlives.php';
        } else {
            include 'currentlive.php';
        } ?>
        </div>
        <hr>
        <p>
            Powered by <a href="http://podcastgenerator.net">Podcast Generator</a>,
            an open source podcast publishing solution
            | Theme based on <a href="https://getbootstrap.com">Bootstrap</a>
        </p>
    </div>
</body>

</html>
