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
    if (empty($_POST['cat1'])) {
        $error = _('Category 1 needs to be set');
        goto error;
    }
    updateConfig('../config.php', 'itunes_category[0]', $_POST['cat1']);
    updateConfig('../config.php', 'itunes_category[1]', $_POST['cat2']);
    updateConfig('../config.php', 'itunes_category[2]', $_POST['cat3']);
    generateRSS();
    header('Location: store_cat.php');
    die();
}

error: echo "";
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Select Podcast Category'); ?></title>
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
        <h1><?php echo _('Select Podcast Categories'); ?></h1>
        <small><?php echo _('Select or change Podcast Categories (for iTunes)'); ?></small>
        <?php
        if (isset($error)) {
            echo '<strong><p style="color: red;">' . $error . '</p></strong>';
        }
        ?>
        <form action="store_cat.php?edit=1" method="POST">
            <h3><?php echo _('Category'); ?> 1:</h3>
            <select name="cat1">
                <?php
                foreach ($categories as $item) {
                    if ($config["itunes_category[0]"] == $item->id) {
                        echo '<option value="' . htmlspecialchars($item->id) . '" selected>' . htmlspecialchars($item->description) . '</option>';
                    } else {
                        echo '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->description) . '</option>';
                    }
                }
                ?>
            </select>
            <hr>
            <h3><?php echo _('Category') ?> 2:</h3>
            <select name="cat2">
                <?php
                if ($config["itunes_category[1]"] == "") {
                    echo '<option value="null" selected></option>';
                } else {
                    echo '<option value="null"></option>';
                }
                foreach ($categories as $item) {
                    if ($config["itunes_category[1]"] == $item->id) {
                        echo '<option value="' . htmlspecialchars($item->id) . '" selected>' . htmlspecialchars($item->description) . '</option>';
                    } else {
                        echo '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->description) . '</option>';
                    }
                }
                ?>
            </select>
            <hr>
            <h3><?php echo _('Category'); ?> 3:</h3>
            <select name="cat3">
                <?php
                if ($config["itunes_category[2]"] == "") {
                    echo '<option value="null" selected></option>';
                } else {
                    echo '<option value="null"></option>';
                }
                foreach ($categories as $item) {
                    if ($config["itunes_category[2]"] == $item->id) {
                        echo '<option value="' . htmlspecialchars($item->id) . '" selected>' . htmlspecialchars($item->description) . '</option>';
                    } else {
                        echo '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->description) . '</option>';
                    }
                }
                ?>
            </select>
            <hr>
            <input type="submit" value="<?php echo _('Save') ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>