<?php
require_once(__DIR__ . '/functions.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config["podcast_title"]) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/bootstrap.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/custom.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/font-awesome.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/dark.css">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($config["podcast_subtitle"]) ?>">
    <meta name="author" content="<?= htmlspecialchars($config["author_name"]) ?>">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="alternate" type="application/rss+xml" title="Subscribe to <?= htmlspecialchars($config["podcast_title"]) ?>" href="feed.xml">

    <!--    Add meta properties for social cards, depends if it's for the main page or a single episode -->
    <?php if (isset($_GET[$link])) {
        /* IF name was passed, do this instead */
        $correctepisode = array();
        for ($i = 0; $i < count($episodes); $i++) {
            if ($episodes[$i]["episode"]["filename"] == $_GET[$link]) {
                $correctepisode = $episodes[$i];
                break;
            }
        }
        $img = $config["url"] . $config["img_dir"] . $config['podcast_cover'];
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
        } ?>
        <meta property="og:title" content="<?= $config["podcast_title"] . ' - ' . $correctepisode["episode"]["titlePG"] ?>" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="'<?= $config["url"] . 'index.php?name=' . $correctepisode["episode"]["filename"] ?>" />
        <meta property="og:image" content="<?= $img ?>" />
        <meta property="og:description" content="<?= $config["podcast_description"] ?>" />

        <?php if (strtolower($config["enablestreaming"]) == "yes") {
            // Get mime
            $mime = getmime($config["absoluteurl"] . $config["upload_dir"] . $correctepisode["episode"]["filename"]);
            if (!$mime) {
                $mime = null;
            }
            $type = '';
            if (substr($mime, 0, 5) == 'video') {
                $type = 'video';
            } elseif (substr($mime, 0, 5) == 'audio' || $mime == 'application/ogg') {
                $type = 'audio';
            }
            if ($type == 'audio' || $type == 'video') {
                ?><meta property="og:<?= $type ?>" content="<?= $config["url"] . $config["upload_dir"] . $correctepisode["episode"]["filename"] ?>" /><?php
                if ($mime) {
                    ?><meta property="og:<?= $type ?>:type" content="<?= $mime ?>" /><?php
                }
            }
        } ?>
    <?php } else { ?>
        <meta property="og:title" content="<?= $config["podcast_title"] ?>" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="<?= $config["url"] ?>" />
        <meta property="og:image" content="<?= $config["url"] . $config["img_dir"] . $config['podcast_cover'] ?>" />
        <meta property="og:description" content="<?= $config["podcast_description"] ?>" />
    <?php } ?>
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
        <?php if (!isset($_GET[$link]) && !isset($no_episodes) && count($episodes) > intval($config['episodeperpage'])) { ?>
            <nav aria-label="<?= _('Page navigation') ?>">
                <?php pagination($config['indexfile'] . '?page=%page%', count($splitted_episodes) - 1, $_GET['page'] ?? 1); ?>
            </nav>
        <?php } ?>
        <hr>
        <p>Powered by <a href="http://podcastgenerator.net">Podcast Generator</a>, an open source podcast publishing solution | Theme based on <a href="https://getbootstrap.com">Bootstrap</a></p>
    </div>
</body>

</html>
