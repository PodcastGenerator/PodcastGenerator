<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/bootstrap.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($config["podcast_subtitle"]); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($config["author_name"]); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    
    <!--    Add meta propreties for social cards, depends if it's for the main page ou a single episode
            TODO : manage imgPG to match with the singleepisode image instead of generic iTunes image-->
    <?php
    // IF name was passed, do this instead
    if (isset($_GET[$link])) {
        $correctepisode = array();
        for ($i = 0; $i < sizeof($episodes); $i++) {
            if ($episodes[$i]["episode"]["filename"] == $_GET[$link]) {
                $correctepisode = $episodes[$i];
                break;
            }
        }
        echo '<meta property="og:title" content="' . $config ["podcast_title"] . $correctepisode["episode"]["titlePG"] . ' "/>';
        echo '<meta property="og:type" content="article"/>';
        echo '<meta property="og:url" content="' . $config["url"] . 'index.php?name=' . $correctepisode["episode"]["filename"] . ' "/>';
        echo '<meta property="og:image" content="' . $config["url"] . $config["img_dir"] . 'itunes_image.jpg"/>';
        echo '<meta property="og:description" content="' . $config["podcast_description"] . ' "/>';
    } else {
        echo '<meta property="og:title" content="' . $config["podcast_title"] . ' "/>';
        echo '<meta property="og:type" content="article"/>';
        echo '<meta property="og:url" content="' . $config["url"] . '"/>';
        echo '<meta property="og:image" content="' . $config["url"] . $config["img_dir"] . 'itunes_image.jpg"/>';
        echo '<meta property="og:description" content="' . $config["podcast_description"] . ' "/>';
    }
    ?>    
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
        if (!isset($no_episodes)) {
            echo '<nav>';
            echo '  <ul class="pagination">';
            for ($j = 0; $j < sizeof($splitted_episodes); $j++) {
                echo '  <li class="page-item"><a class="page-link" href="' . $config['indexfile'] . '?page=' . ($j + 1) . '">' . ($j + 1) . '</a></li>';
            }
            echo '  </ul>';
            echo '</nav>';
        }
        ?>
        <hr>
        <p>Powered by <a href="http://podcastgenerator.net">Podcast Generator</a>, an open source podcast publishing solution | Theme based on <a href="https://getbootstrap.org">Bootstrap</a></p>
    </div>
</body>

</html>
