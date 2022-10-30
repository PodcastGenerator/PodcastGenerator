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

// If episode is deleted
if (isset($_GET['del'])) {
    checkToken();
    $cats_xml = simplexml_load_file('../categories.xml');
    // Get index of item
    foreach ($cats_xml as $item) {
        if ($item->id == $_GET['del']) {
            // Delete the actual node
            $dom = dom_import_simplexml($item);
            $dom->parentNode->removeChild($dom);
        }
    }
    // Write to file
    $cats_xml->asXML('../categories.xml');
    header('Location: episodes_manage_cats.php');
}
// If episode is added
if (isset($_GET['add'])) {
    checkToken();
    $cats_xml = simplexml_load_file('../categories.xml');
    $description = $_POST['categoryname'];
    // These chars should be replaced with an underscore
    $chars_to_replace = [' ', '&', '"', '\'', '<', '>'];
    $id = $description;
    for ($i = 0; $i < count($chars_to_replace); $i++) {
        $id = strtolower(str_replace($chars_to_replace[$i], '_', $id));
    }
    // Check if this episode already exists
    foreach ($cats_xml as $item) {
        if ($item->id == $id) {
            $error = _("Category already exists");
        }
    }
    if (!isset($error)) {
        $cats_xml->addChild('category');
        // Check for an empty item (which is the last)
        foreach ($cats_xml as $item) {
            if (!isset($item->id) && !isset($item->description)) {
                $item->addChild('id', $id);
                $item->addChild('description', htmlspecialchars($description));
                break;
            }
        }
    }
    $cats_xml->asXML('../categories.xml');
    header('Location: episodes_manage_cats.php');
}

$cats_xml = simplexml_load_file('../categories.xml');

$catsPageBaseLink = $config['url'] . 'categories.php?cat=';

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Manage categories') ?></title>
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
        <h1><?= _('Manage categories') ?></h1>
        <h3><?= _('Add category') ?></h3>
        <form action="episodes_manage_cats.php?add=1" method="POST">
            <label for="categoryname" class="req"><?= _('Category Name') ?>:</label><br>
            <input type="text" id="categoryname" name="categoryname" placeholder="<?= _('Category Name') ?>"><br><br>
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _('Add') ?>" class="btn btn-success"><br><br>
        </form>
        <h3><?= _('Current Categories') ?></h3>
        <?php foreach ($cats_xml as $item) { ?>
            <form action="episodes_manage_cats.php?del=<?= htmlspecialchars($item->id) ?>" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <a href="<?= htmlspecialchars($catsPageBaseLink . $item->id) ?>">
                <?= htmlspecialchars($item->description) ?>
            </a>
            <input class="btn btn-sm btn-danger" type="submit" value="<?= _('Delete') ?>">
            </form>
            <br>
        <?php } ?>
    </div>
</body>

</html>
