<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/bootstrap.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/custom.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($config["theme_path"]); ?>style/font-awesome.min.css">
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
        echo '<div class="row">';
        if (!isset($_GET["cat"])) {
        ?>
            <div class="col">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action" href="categories.php?cat=all"><?php echo _('All Episodes'); ?></a>
                    <?php
                    foreach ($categories_xml as $item) {
                        echo "<a class='list-group-item list-group-item-action' href=\"categories.php?cat=" . $item->id . "\">" . $item->description . "</a>";
                    }
                    ?>
                </div>
            </div>
        <?php
        } else {
            include 'listepisodes.php';
        }
        echo '</div>'
        ?>
    </div>
</body>

</html>