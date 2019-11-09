<?php
require "checkLogin.php";
require "../core/include_admin.php";

generateRSS();
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $config["podcast_title"]; ?> - Regenerate Feed</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
</head>

<body>
    <?php
    include "js.php";
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <h1 style='color: green;'>Successfully regenrated RSS feed</h1>
        <a href="index.php">Return</a>
    </div>
</body>

</html>