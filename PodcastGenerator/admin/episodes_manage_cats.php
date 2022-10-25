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

$categories = new PodcastGenerator\CategoryManager('../categories.xml');

// If episode is deleted
if (isset($_GET['del'])) {
    checkToken();

    $slug = $_GET['del'];
    $categories->deleteCategoryBySlug($slug);

    if ($categories->saveChanges()) {
        header('Location: episodes_manage_cats.php');
        exit();
    }

    $error = sprintf(_('Could not delete category "%s".'), $slug);
    goto error;
}
// If episode is added
if (isset($_GET['add'])) {
    checkToken();

    $description = $_POST['categoryname'];
    $category = PodcastGenerator\Models\Category::newFromDescription($description);
    if ($category === false) {
        $error = _('Cannot create a category without a name!');
        goto error;
    }

    if (!$category->isValid()) {
        $error = sprintf(_('Cannot create category "%s": %s'), $description, _('Invalid data'));
        goto error;
    }

    try {
        $categories->addCategory($category);
    } catch (Exception $e) {
        $error = sprintf(_('Cannot create category "%s": %s'), $description, $e->getMessage());
        goto error;
    }

    if ($categories->saveChanges()) {
        header('Location: episodes_manage_cats.php');
        exit();
    }

    $error = sprintf(_('Cannot create category "%s": %s'), $description, _('Failed to save category data'));
    goto error;
}

error:

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
        <?php foreach ($categories->getCategories() as $item) { ?>
            <form action="episodes_manage_cats.php?del=<?= htmlspecialchars($item->slug) ?>" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <a href="<?= htmlspecialchars($catsPageBaseLink . $item->slug) ?>">
                <?= htmlspecialchars($item->name) ?>
            </a>
            <input class="btn btn-sm btn-danger" type="submit" value="<?= _('Delete') ?>">
            </form>
            <br>
        <?php } ?>
    </div>
</body>

</html>
