<?php
require "checkLogin.php";
require "../core/include_admin.php";

if (isset($_GET["disable"])) {
    updateConfig("../config.php", "freebox", "no");
    header("Location: theme_freebox.php");
    die();
}
if (isset($_GET["enable"])) {
    updateConfig("../config.php", "freebox", "yes");
    header("Location: theme_freebox.php");
    die();
}

if(isset($_GET["change"])) {
    updateFreebox("../", $_POST["content"]);
    header("Locaiotn: theme_freebox.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?> - Customize Freebox</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>

<body>
    <?php
    include "js.php";
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <h1>Customize Freebox</h1>
        <h3>Current Freebox</h3>
        <div class="card">
            <div class="card-body">
                <?php echo getFreebox("../"); ?>
            </div>
        </div>
        <h3>Enable / Disable Freebox</h3>
        <?php
        if (getFreebox("../") == null) {
            echo "<a href='theme_freebox.php?enable=1' class='btn btn-success'>Enable Freebox</a>";
        } else {
            echo "<a href='theme_freebox.php?disable=1' class='btn btn-danger'>Disable Freebox</a>";
        }
        ?>
        <h3>Change Freebox content</h3>
        <form action="theme_freebox.php?change=1" method="POST">
            Content:<br>
            <textarea rows="10" cols="100" name="content"><?php echo htmlspecialchars(getFreebox("../")); ?></textarea><br><br>
            <input type="submit" value="Save" class="btn btn-success">
        </form>
    </div>
</body>

</html>