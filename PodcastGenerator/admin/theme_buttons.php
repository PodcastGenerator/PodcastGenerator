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

$buttons = getButtons();

if (isset($_GET['add'])) {
    checkToken();
    $exists = false;
    foreach ($buttons as $item) {
        if ($item->name == $_GET['name']) {
            $exists = true;
            break;
        }
    }
    if ($exists) {
        $error = _('Item exists');
        goto error;
    }

    if (empty($_POST['name']) || empty($_POST['href']) || empty($_POST['class'])) {
        $error = _('Name, Link and CSS Class needs to be set');
        goto error;
    }

    if (empty($_POST['protocol'])) {
        $item = $buttons->addChild("button");
        $item->addChild("name", $_POST["name"]);
        $item->addChild("href", $_POST["href"]);
        $item->addChild("class", $_POST["class"]);
    } else {
        $item = $buttons->addChild('button');
        $item->addChild("name", $_POST["name"]);
        $item->addChild("href", $_POST["href"]);
        $item->addChild("class", $_POST["class"]);
        $item->addChild("protocol", $_POST["protocol"]);
    }

    $buttons->asXML('../buttons.xml');
    header('Location: theme_buttons.php');
    die();
} elseif (isset($_GET['edit'])) {
    checkToken();
    // Find item
    foreach ($buttons as $item) {
        if ($item->name == $_GET['name']) {
            $item->name = $_POST['name'];
            $item->href = $_POST['href'];
            $item->class = $_POST['class'];
            if (!empty($_POST['protocol'])) {
                $item->protocol = $_POST['protocol'];
            }
        }
    }
    $buttons->asXML('../buttons.xml');
    header('Location: theme_buttons.php');
    die();
} elseif (isset($_GET['del'])) {
    checkToken();
    // Find item
    foreach ($buttons as $item) {
        if ($item->name == $_GET['name']) {
            // Delete the actual node
            $dom = dom_import_simplexml($item);
            $dom->parentNode->removeChild($dom);
        }
    }
    $buttons->asXML('../buttons.xml');
    header('Location: theme_buttons.php');
    die();
}

error:

$name = null;
$btn = null;
if (isset($_GET['name'])) {
    $name = $_GET['name'];
    foreach ($buttons as $item) {
        if ($item->name == $name) {
            $btn = $item;
            break;
        }
    }
} else {
    $btn = (object) ['name' => $name, 'href' => null, 'class' => null, 'protocol' => null];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Theme Buttons') ?></title>
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
        <h1><?= _('Change Buttons') ?></h1>
        <small><?= _('Click on the button you wish to edit') ?></small><br>
        <?php if (isset($error)) { ?>
            <strong><p style="color: red;"><?= $error ?></p></strong>
        <?php } ?>

        <?php if ($name == null) { ?>
            <ul>
            <?php foreach ($buttons as $item) { ?>
                <li>
                    <a href="theme_buttons.php?name=<?= htmlspecialchars($item->name) ?>">
                        <?= htmlspecialchars($item->name) ?>
                    </a>
                </li>
            <?php } ?>
            </ul>
        <?php } else { ?>
            <form action="theme_buttons.php?edit=1&name=<?= htmlspecialchars($name) ?>" method="POST">
                <label for="name_e"><?= _('Name') ?>:</label> (<?= _('needs to be unique') ?>)<br>
                <input type="text" id="name_e" name="name" value="<?= htmlspecialchars($btn->name) ?>"><br>

                <label for="href_e"><?= _('Link') ?>:</label> (<?= _('where it should point to') ?>)<br>
                <input type="text" id="href_e" name="href" value="<?= htmlspecialchars($btn->href) ?>"><br>

                <label for="class_e"><?= _('CSS Classes') ?></label>
                (<?= sprintf(
                    _('depends on theme, you can use %s in the default theme'),
                    '<a href="https://getbootstrap.com/docs/4.3/components/buttons/">bootstrap</a>'
                ) ?>)<br>
                <input type="text" id="class_e" name="class" value="<?= htmlspecialchars($btn->class) ?>"><br>

                <label for="protocol_e"><?= _('Protocol') ?>:</label>
                (<?= _('Leave it blank if you don\'t know what you are doing') ?><br>
                <input type="text" id="protocol_e" name="protocol" value="<?= htmlspecialchars($btn->protocol) ?>">
                <br>

                <br>
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                <input type="submit" value="<?= _('Submit') ?>" class="btn btn-success">
            </form>
            <hr>

            <form action="theme_buttons.php?del=1&name=<?= htmlspecialchars($name) ?>" method="POST">
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                <input class="btn btn-danger" type="submit" value="<?= _('Delete Button') ?>">
            </form>
        <?php } ?>

        <?php if (!isset($_GET['name'])) { ?>
            <hr>
            <h3><?= _('Add Button') ?></h3>
            <form action="theme_buttons.php?add=1&name=<?= htmlspecialchars($name) ?>" method="POST">
                <label for="name_a"><?= _('Name') ?>:</label> (<?= _('needs to be unique') ?>)<br>
                <input type="text" id="name_a" name="name" value="<?= htmlspecialchars($btn->name) ?>"><br>

                <label for="href_e"><?= _('Link') ?>:</label> (<?= _('where it should point to') ?>)<br>
                <input type="text" id="href_a" name="href" value="<?= htmlspecialchars($btn->href) ?>"><br>

                <label for="class_e"><?= _('CSS Classes') ?></label>
                (<?= sprintf(
                    _('depends on theme, you can use %s in the default theme'),
                    '<a href="https://getbootstrap.com/docs/4.3/components/buttons/">bootstrap</a>'
                ) ?>)<br>
                <input type="text" id="class_a" name="class" value="<?= htmlspecialchars($btn->class) ?>"><br>

                <label for="protocol_e"><?= _('Protocol') ?>:</label>
                (<?= _('Leave it blank if you don\'t know what you are doing') ?><br>
                <input type="text" id="protocol_a" name="protocol" value="<?= htmlspecialchars($btn->protocol) ?>"><br>

                <br>
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                <input type="submit" value="<?= _('Submit') ?>" class="btn btn-success">
            </form>
        <?php } ?>
    </div>
</body>

</html>
