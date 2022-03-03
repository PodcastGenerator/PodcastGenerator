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

$curl_str = "curl";
$episodes = getEpisodes(null, $config);
if (count($episodes) == 0) {
    $curl_str = "No episodes found";
}
for ($i = 0; $i < count($episodes); $i++) {
    $filename = $episodes[$i]["episode"]["filename"];
    $curl_str .= " -O " . $config['url'] . $config['upload_dir'] . $filename;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']) ?> - Bulk Download</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        iframe {
            width: 1000px;
            height: 500px;
        }
    </style>
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
        <h1><?= _('Bulk download episodes') ?></h1>
        <p>
        <?=
        _('If you want to bulk download all episodes, you need to have curl installed.
        On Windows 10, macOS and most Linux distributions it is already pre-installed. Please
        open your Terminal app on macOS and Linux or open the start menu and search for cmd.exe on Windows.
        After this paste the following (long) command into the terminal/cmd.exe and press enter.')
        ?>
        <br>
        <code>
<pre>
<?= $curl_str ?>
</pre>
        </code>
        </p>
    </div>
</body>

</html>