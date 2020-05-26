<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require "securitycheck.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Podcast Generator - Setup</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
</head>

<body>
    <div class="container">
        <h1>Podcast Generator - Setup</h1>
        <?php
        if ($isdev) {
        ?>
            <div class="alert alert-danger" role="alert">
                <p>
                    WARNING!: You use a development version of Podcast Generator!<br>
                    Please use a release version rather than this. You can find them <a href="https://github.com/PodcastGenerator/PodcastGenerator/releases">here</a>
                </p>
            </div>
        <?php
        }
        ?>
        <p>
            Howdy and welcome to Podcast Generator <?php echo $version; ?>!<br>
            Thanks for choosing a <a href="http://emilengler.com" target="_blank">Emil Engler</a> and <a href="http://betella.net" target="_blank">Alberto Betella</a> software, have a cookie 🍪!<br>
            This is Free and Open Source Software<br>
            <br>
            <hr>
            <a href="step1.php" class="btn btn-success">Begin Installation</a>
        </p>
    </div>
</body>

</html>