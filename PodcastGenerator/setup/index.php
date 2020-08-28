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

<body class="bg-light">
    <div class="container m-auto">
        <div class="align-items-center justify-content-md-center p-3 row vh-100">
            <div class="col-xl-7 col-lg-7 col-md-10 col-sm-12 bg-white p-4 shadow">
                <h2>Podcast Generator - Setup</h2>
                <?php
                if ($isdev) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        WARNING!: You use a development version of Podcast Generator!<br>
                        Please use a release version rather than this. You can find them <a class="alert-link" href="https://github.com/PodcastGenerator/PodcastGenerator/releases" target="_blank">here</a>
                    </div>
                <?php
                }
                ?>
                <p>
                    Howdy and welcome to Podcast Generator <?php echo $version; ?>!<br>
                    Thanks for choosing a <a href="http://emilengler.com" target="_blank">Emil Engler</a> and <a href="http://betella.net" target="_blank">Alberto Betella</a> software, have a cookie 🍪!<br>
                    This is Free and Open Source Software
                    <hr>
                    <a href="step1.php" class="btn btn-block btn-success">Begin Installation</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>