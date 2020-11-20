<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/bootstrap.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/custom.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/font-awesome.min.css">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($config["podcast_subtitle"]); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($config["author_name"]); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">

    <!--    Add meta propreties for social cards, depends if it's for the main page ou a single episode -->
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
        $img = $config["url"] . $config["img_dir"] . 'itunes_image.jpg';
        // The imgPG value has the highest priority
        if ($correctepisode["episode"]["imgPG"] != "") {
            $img = $correctepisode["episode"]["imgPG"];
        } elseif (
            file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.jpg') ||
            file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png')
        ) {
            // TODO Really ugly code, needs to be done more beatiful
            $filename = file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png') ?
                $config["url"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png' :
                $config["url"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.jpg';
            $img = $filename;
        }
        echo '<meta property="og:title" content="' . $config["podcast_title"] . ' - ' . $correctepisode["episode"]["titlePG"] . '" />' . "\n";
        echo '    <meta property="og:type" content="article" />' . "\n";
        echo '    <meta property="og:url" content="' . $config["url"] . 'index.php?name=' . $correctepisode["episode"]["filename"] . '" />' . "\n";
        echo '    <meta property="og:image" content="' . $img . '" />' . "\n";
        echo '    <meta property="og:description" content="' . $config["podcast_description"] . '" />' . "\n";
    } else {
        echo '    <meta property="og:title" content="' . $config["podcast_title"] . '" />' . "\n";
        echo '    <meta property="og:type" content="article" />' . "\n";
        echo '    <meta property="og:url" content="' . $config["url"] . '" />' . "\n";
        echo '    <meta property="og:image" content="' . $config["url"] . $config["img_dir"] . 'itunes_image.jpg" />' . "\n";
        echo '    <meta property="og:description" content="' . $config["podcast_description"] . '" />' . "\n";
    }
    ?>
</head>

<body class="bg-dark">
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
        if (!isset($no_episodes) && sizeof($episodes) > intval($config['episodeperpage'])) {
            echo '<nav>';
            echo '  <ul class="pagination">';
            for ($j = 0; $j < sizeof($splitted_episodes); $j++) {
                echo '  <li class="page-item"><a class="page-link bg-secondary text-white" href="' . $config['indexfile'] . '?page=' . ($j + 1) . '">' . ($j + 1) . '</a></li>';
            }
            echo '  </ul>';
            echo '</nav>';
        }
        ?>
        <hr>
        <p class="text-white">Powered by <a class="text-white-50" href="http://podcastgenerator.net">Podcast Generator</a>, an open source podcast publishing solution | Theme based on <a class="text-white-50" href="https://getbootstrap.org">Bootstrap</a></p>
    </div>
</body>

</html>
