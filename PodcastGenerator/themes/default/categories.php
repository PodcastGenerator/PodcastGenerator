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
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <?php
        include "js.php";
        include "jumbotron.php";
        if (!isset($_GET["cat"])) {
        ?>
            <ul>
                <?php
                foreach ($categories_xml as $item) {
                    echo "<li><a href=\"categories.php?cat=" . htmlspecialchars($item->id) . "\">" . htmlspecialchars($item->description) . "</a></li>";
                }
                ?>
            </ul>
            <hr>
            <a href="categories.php?cat=all">All Episodes</a>
        <?php
        } else {
            include 'listepisodes.php';
        }
        ?>
    </div>
</body>

</html>