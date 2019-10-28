<!DOCTYPE html>
<html lang="<?php echo $config["feed_language"]; ?>">

<head>
    <title><?php echo $config["podcast_title"]; ?></title>
    <link rel="stylesheet" href="<?php echo $config["theme_path"]; ?>style/bootstrap.css">
    <meta charset="utf-8">
</head>

<body>
    <?php
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <?php
        include "jumbotron.php";
        ?>
    </div>
</body>

</html>