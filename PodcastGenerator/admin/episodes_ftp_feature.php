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

if(isset($_GET['start'])) {
    checkToken();
    $num_added = indexEpisodes($config);
    if($num_added) {
        generateRSS();
        pingServices();
        $success = sprintf(_('Added %d new episode(s)'), $num_added);
    } else {
        $success = _('No new episodes were found');
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('FTP Feature'); ?></title>
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
        <h1><?php echo _('FTP Auto Indexing'); ?></h1>
        <?php
        if (!isset($_GET['start'])) {
            echo '<form action="episodes_ftp_feature.php?start=1" method="POST">';
            echo '<input type="hidden" name="token" value="' . $_SESSION['token']  . '">';
            echo '<input class="btn btn-success" type="submit" value="' . _('Begin') . '">';
            echo '</form>';
        }
        if (isset($success)) {
            echo '<p>' . htmlspecialchars($success) . '</p>';
        }
        ?>
    </div>
</body>

</html>
