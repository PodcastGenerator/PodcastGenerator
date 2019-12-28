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
} else if (isset($_GET['edit'])) {
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
} else if (isset($_GET['del'])) {
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

error: echo "";
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Theme Buttons'); ?></title>
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
        <h1><?php echo _('Change Buttons'); ?></h1>
        <small><?php echo _('Click on the button you wish to edit'); ?></small><br>
        <?php
        if (isset($error)) {
            echo '<strong><p style="color: red;">' . $error . '</p></strong>';
        }
        ?>
        <?php
        if (!isset($_GET['name'])) {
            foreach ($buttons as $item) {
                echo '<a href="theme_buttons.php?name=' . htmlspecialchars($item->name) . '">' . htmlspecialchars($item->name) . '</a><br>';
            }
        } else {
            $btn = null;
            foreach ($buttons as $item) {
                if ($item->name == $_GET['name']) {
                    $btn = $item;
                }
            }
        ?>
            <form action="theme_buttons.php?edit=1&name=<?php echo htmlspecialchars($_GET['name']); ?>" method="POST">
                <?php echo _('Name (needs to be unique)'); ?>:<br>
                <input type="text" name="name" value="<?php echo htmlspecialchars($btn->name); ?>"><br>
                <?php echo _('Link (where it should point to)'); ?> :<br>
                <input type="text" name="href" value="<?php echo htmlspecialchars($btn->href); ?>"><br>
                <?php echo sprintf('CSS Classes (depends on theme, you can use %s in the default theme)', '<a href="https://getbootstrap.com/docs/4.3/components/buttons/">bootstrap</a>'); ?>:<br>
                <input type="text" name="class" value="<?php echo htmlspecialchars($btn->class); ?>"><br>
                <?php echo _("Protocol (Leave it blank if you don't know what you are doing)"); ?>:<br>
                <input type="text" name="protocol" value="<?php echo htmlspecialchars($btn->protocol); ?>"><br><br>
                <input type="submit" value="<?php echo _('Submit'); ?>" class="btn btn-success">
            </form>
            <hr>
            <a href="theme_buttons.php?del=1&name=<?php echo htmlspecialchars($_GET['name']); ?>" class="btn btn-danger"><?php echo _('Delete Button'); ?></a>
        <?php
        }
        ?>
        <?php
        if (!isset($_GET['name'])) {
        ?>
            <hr>
            <h3><?php echo _('Add Button'); ?></h3>
            <form action="theme_buttons.php?add=1&name=<?php echo htmlspecialchars($_GET['name']); ?>" method="POST">
                <?php echo _('Name (needs to be unique)'); ?>:<br>
                <input type="text" name="name" value="<?php echo htmlspecialchars($btn->name); ?>"><br>
                <?php echo _('Link (where it should point to)'); ?>:<br>
                <input type="text" name="href" value="<?php echo htmlspecialchars($btn->href); ?>"><br>
                <?php echo sprintf('CSS Classes (depends on theme, you can use %s in the default theme)', '<a href="https://getbootstrap.com/docs/4.3/components/buttons/">bootstrap</a>'); ?>:<br>
                <input type="text" name="class" value="<?php echo htmlspecialchars($btn->class); ?>"><br>
                <?php echo _("Protocol (Leave it blank if you don't know what you are doing)"); ?>:<br>
                <input type="text" name="protocol" value="<?php echo htmlspecialchars($btn->protocol); ?>"><br><br>
                <input type="submit" value="<?php echo _('Submit'); ?>" class="btn btn-success">
            </form>
        <?php
        }
        ?>
    </div>
</body>

</html>