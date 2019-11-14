<?php
require "checkLogin.php";
require "../core/include_admin.php";

if (isset($_GET["edit"])) {
    foreach ($_POST as $key => $value) {
        updateConfig("../config.php", $key, $value);
    }
    header("Location: pg_config.php");
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?> - Podcast Generator Configuration</title>
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
        <h1>Change Podcast Generator Configuration</h1>
        <form action="pg_config.php?edit=1" method="POST">
            Enable Audio and Video Player:<br>
            <small>Enable streaming in web browser</small><br>
            <input type="radio" name="enablestreaming" value="yes" checked> Yes <input type="radio" name="enablestreaming" value="no"> No<br>
            <hr>
            Enable Freebox:<br>
            <small>Freebox allows you to write freely what you wish, add links or text through a visual editor in the admin section.</small><br>
            <input type="radio" name="freebox" value="yes" checked> Yes <input type="radio" name="freebox" value="no"> No<br>
            <hr>
            Enable categories:<br>
            <small> Enable categories feature to make thematic lists of your podcasts. </small><br>
            <input type="radio" name="categoriesenabled" value="yes" checked> Yes <input type="radio" name="categoriesenabled" value="no"> No<br>
            <hr>
            How many recent episodes in the homepage?<br>
            <input type="number" name="max_recent" value="4" min="1"><br>
            <hr>
            Select a date format:<br>
            <select name="dateformat">
                <option value="d-m-Y">Date / Month / Year</option>
                <option value="m-d-Y">Month / Day / Year</option>
                <option value="y-m-d" selected>Year / Month / Day</option>
            </select>
            <hr>
            Use cron to regenerate the RSS feed:<br>
            <input type="text" value="<?php echo htmlspecialchars($config["url"]) . "pg-cron.php?key=" . htmlspecialchars($config["installation_key"]); ?>" style="width: 100%;" readonly><br>
            <hr>
            <input type="submit" value="Submit" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>