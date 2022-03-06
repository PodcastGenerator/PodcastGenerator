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

$categories = simplexml_load_file('../components/itunes_categories/itunes_categories.xml');

if (isset($_GET['edit'])) {
    checkToken();
    if (empty($_POST['cat1'])) {
        $error = _('Category 1 needs to be set');
        goto error;
    }
    updateConfig('../config.php', 'itunes_category[0]', $_POST['cat1']);
    updateConfig('../config.php', 'itunes_category[1]', $_POST['cat2'], true);
    updateConfig('../config.php', 'itunes_category[2]', $_POST['cat3'], true);
    generateRSS();
    pingServices();
    header('Location: store_cat.php');
    die();
}

error:
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Select Podcast Category') ?></title>
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
        <h1><?= _('Select Podcast Categories') ?></h1>
        <small><?= _('Select or change Podcast Categories (for iTunes)') ?></small>
        <?php if (isset($error)) { ?>
            <strong><p style="color: red;"><?= $error ?></p></strong>
        <?php } ?>
        <form action="store_cat.php?edit=1" method="POST">
            <h3><label for="cat1"><?= sprintf(_('Category %d'), 1) ?>:</label></h3>
            <select id="cat1" name="cat1">
                <?php foreach ($categories as $item) { ?>
                    <?php if ($config["itunes_category[0]"] == $item->id) { ?>
                        <option value="<?= htmlspecialchars($item->id) ?>" selected>
                            <?= htmlspecialchars($item->description) ?>
                        </option>
                    <?php } else { ?>
                        <option value="<?= htmlspecialchars($item->id) ?>">
                            <?= htmlspecialchars($item->description) ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
            <hr>

            <h3><label for="cat2"><?= sprintf(_('Category %d'), 2) ?>:</label></h3>
            <select id="cat2" name="cat2">
                <?php if ($config["itunes_category[1]"] == "") { ?>
                    <option value="null" selected></option>
                <?php } else { ?>
                    <option value="null"></option>
                <?php } ?>
                <?php foreach ($categories as $item) { ?>
                    <?php if ($config["itunes_category[1]"] == $item->id) { ?>
                        <option value="<?= htmlspecialchars($item->id) ?>" selected>
                            <?= htmlspecialchars($item->description) ?>
                        </option>
                    <?php } else { ?>
                        <option value="<?= htmlspecialchars($item->id) ?>">
                            <?= htmlspecialchars($item->description) ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
            <hr>

            <h3><label for="cat3"><?= sprintf(_('Category %d'), 3) ?>:</label></h3>
            <select id="cat3" name="cat3">
                <?php if ($config["itunes_category[2]"] == "") { ?>
                    <option value="null" selected></option>
                <?php } else { ?>
                    <option value="null"></option>
                <?php } ?>
                <?php foreach ($categories as $item) { ?>
                    <?php if ($config["itunes_category[2]"] == $item->id) { ?>
                        <option value="<?= htmlspecialchars($item->id) ?>" selected>
                            <?= htmlspecialchars($item->description) ?>
                        </option>
                    <?php } else { ?>
                        <option value="<?= htmlspecialchars($item->id) ?>">
                            <?= htmlspecialchars($item->description) ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
            <hr>

            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _('Save') ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>
