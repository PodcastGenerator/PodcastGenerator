<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/bootstrap.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <?php
    include "js.php";
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <?php
        include "jumbotron.php";
        ?>
        <div class="row">
            <?php
            // IF name was passed, do this instead
            if (isset($_GET[$link])) {
                include 'singleepisode.php';
            } else {
                include 'listepisodes.php';
            }
            ?>
        </div>
        <?php
        echo '<nav>';
        echo '  <ul class="pagination">';
        for ($j = 0; $j < sizeof($splitted_episodes); $j++) {
            echo '  <li class="page-item"><a class="page-link" href="index.php?page=' . ($j + 1) . '">' . ($j + 1) . '</a></li>';
        }
        echo '  </ul>';
        echo '</nav>';

        ?>
        <hr>
        <p>Powered by <a href="http://podcastgenerator.net">Podcast Generator</a>, an open source podcast publishing solution | Theme based on <a href="https://getbootstrap.org">Bootstrap</a></p>
    </div>
</body>

</html>