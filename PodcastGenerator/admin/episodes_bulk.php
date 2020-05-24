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
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - Bulk Download</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        iframe {
            width: 1000px;
            height: 500px;
        }
    </style>
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
        <h1><?php echo _('Bulk download episodes'); ?></h1>
        <?php
        $curl_str = "curl";
        $episodes = getEpisodes(null, $config);
        if(sizeof($episodes) == 0)
            $curl_str = "No episodes found";
        for($i = 0; $i < sizeof($episodes); $i++) {
            $filename = $episodes[$i]["episode"]["filename"];
            $curl_str .= " -O " . $config['url'] . $config['upload_dir'] . $filename;
        }
        ?>
        <p>
        <?php
        echo _('If you want to bulk download all episodes, you need to have curl installed.
        On Windows 10, macOS and most Linux distributions it is already pre-installed. Please
        open your Terminal app on macOS and Linux or open the start menu and search for cmd.exe on Windows.
        After this paste the following (long) command into the terminal/cmd.exe and press enter.');
        ?>
        <br>
        <code>
<pre>
<?php echo $curl_str; ?>
</pre>
        </code>
        </p>
    </div>
</body>

</html>