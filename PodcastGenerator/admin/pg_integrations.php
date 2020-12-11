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

if (isset($_GET['edit'])) {
    foreach ($_POST as $key => $value) {
        updateConfig('../config.php', $key, $value);
    }
    header('Location: pg_integrations.php');
    die();

    error: echo("");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']) ?> - <?= _('Podcast Generator Configuration'); ?></title>
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
        <h1><?= _('Manage integrations') ?></h1>
        <?php
        if (isset($error)) {
            echo '<p style="color: red;"><strong>' . $error . '</strong></p>';
        } ?>
        <form action="pg_integrations.php?edit=1" method="POST">
            <section>
                <h2><?= _('WebSub') ?>:</h2>
                <?= _('Server address') ?>:<br>
                <small><?= _('This is the full address of the WebSub hub to alert when the podcast is updated.') ?></small><br>
                <input type="text" name="websub_server" value="<?= htmlspecialchars($config['websub_server']) ?>"><br>
                <hr>
            </section>
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>