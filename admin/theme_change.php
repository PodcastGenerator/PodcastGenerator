<?php
require "checkLogin.php";
require "../core/include_admin.php";

// Get all themes
$themes = array();
$themes_in_dir = glob("../themes" . "/*", GLOB_ONLYDIR);
$realthemes = array();
for ($i = 0; $i < sizeof($themes_in_dir); $i++) {
    array_push($themes, [substr($themes_in_dir[$i], 3) . "/", json_decode(file_get_contents($themes_in_dir[$i] . "/theme.json"))]);
}
// Check if the theme is compatible
for ($i = 0; $i < sizeof($themes); $i++) {
    if (in_array(strval($version), $themes[$i][1]->pg_versions)) {
        array_push($realthemes, $themes[$i]);
    }
}

$themes = $realthemes;
unset($realthemes);

if(isset($_GET["change"])) {
    if($_GET["change"] > sizeof($themes)) {
        goto error;
    }
    updateConfig("../config.php", "theme_path", $themes[$_GET["change"]][0]);
    header("Location: theme_change.php");
    die();

    error:
    echo "";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $config["podcast_title"]; ?> - Admin</title>
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
        <h1>Change theme</h1>
        <small>You can upload themes to your <code>themes/</code> folder</small>
        <h3>Installed themes</h3>
        <div class="row">
            <?php
            for ($i = 0; $i < sizeof($themes); $i++) {
                $json = $themes[$i][1];
                echo '<div class="col-lg-6">';
                echo '<div class="card">';
                echo '<img src="../' . $themes[$i][0] . 'preview.png" class="card-img-top">';
                echo '<div class="card-body">';
                echo '<h3>'.$json->name.'</h3>';
                echo '<p>Description: '.$json->description.'</p>';
                echo '<p>Author: '.$json->author.'</p>';
                echo '<p>Theme Version: '.$json->version.'</p>';
                echo '<p>Credits: '.$json->credits.'</p>';
                echo '<hr>';
                // Check if this theme is the used theme and or not
                if($themes[$i][0] == $config["theme_path"]) {
                    echo '<small>This theme is currently in use</small>';
                }
                else {
                    echo '<a href="theme_change.php?change='.$i.'" class="btn btn-success">Switch theme</a>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>

</html>