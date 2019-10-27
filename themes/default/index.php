<!DOCTYPE html>
<html lang="<?php echo $config["feed_language"]; ?>">

<head>
    <title><?php echo $config["podcast_title"]; ?></title>
    <link rel="stylesheet" href="<?php echo $config["theme_path"]; ?>style/bootstrap.css">
</head>

<body>
    <?php
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <div class="jumbotron">
            <h1 class="display-4"><?php echo $config["podcast_title"]; ?></h1>
            <p class="lead"><?php echo $config["podcast_description"]; ?></p>
            <small>TODO: Add buttons here</small>
        </div>
    </div>
</body>

</html>