<?php
require "checkLogin.php";
require "../core/include_admin.php";

if(isset($_GET["upload"])) {
    // Check if file is too big
    if($_FILES["file"]["size"] > $config["max_upload_form_size"]) {
        $error = "File is too big";
    }
    $imagesize = getimagesize($_FILES["file"]["tmp_name"]);
    // Verify if image is a square
    if($imagesize[0] / $imagesize[1] != 1) {
        $error = "Image is not quadratic";
        goto error;
    }

    // Now everything is cool and the file can uploaded
    if(!move_uploaded_file($_FILES["file"]["tmp_name"], "../" . $config["img_dir"] . "itunes_cover.jpg")) {
        $error = "File was not uploaded";
        goto error;
    }
    else {
        // Wait a few seconds so the upload can finish
        sleep(3);
        header("Location: store_cover.php");
        die();
    }
    error:
    echo "";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?> - Admin</title>
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
        <h1>Change Cover</h1>
        <p>The cover art will be displayed in the podcast readers.</p>
        <?php
        if(isset($error)) {
            echo "<strong><p style='color: red;'>$error</p></strong>";
        }
        ?>
        <h3>Current Cover</h3>
        <img src="../images/itunes_cover.jpg" style="max-height: 350px; max-width: 350px;">
        <hr>
        <h3>Upload new cover</h3>
        <form action="store_cover.php?upload=1" method="POST" enctype="multipart/form-data">
            Select file:<br>
            <input type="file" name="file"><br><br>
            <input type="submit" value="Upload" class="btn btn-success">
        </form>
    </div>
</body>

</html>