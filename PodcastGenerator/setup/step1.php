<?php
require "securitycheck.php";

// Dirs
$media = "../media/";
$images = "../images/";
$scripts = "../";

$media_write = false;
$images_write = false;
$scripts_write = false;

$testfile = "test.txt";

// Creating all testfiles
// TODO Loop this and put the strings in arrays
// Checking media
$f = fopen($media.$testfile, 'w');
fwrite($f, "test");
fclose($f);

// Now create test file for images
$f = fopen($images.$testfile, 'w');
fwrite($f, "test");
fclose($f);

// Now do this with the root
$f = fopen($scripts.$testfile, 'w');
fwrite($f, "test");
fclose($f);

// Now verify if the files actually exists
if(file_exists($media.$testfile)) {
    unlink($media.$testfile);
    $media_write = true;
}

if(file_exists($images.$testfile)) {
    unlink($images.$testfile);
    $images_write = true;
}

if(file_exists($scripts.$testfile)) {
    unlink($scripts.$testfile);
    $scripts_write = true;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Podcast Generator - Step 1</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../core/bootstrap/style.css">
    </head>
    <body>
        <div class="container">
            <h1>Podcast Generator - Step 1</h1>
            <p>
                We are now checking of our data direcotires are writable so you can actual store the data.<br>
                <?php
                if($media_write)
                    echo "<p style=\"color: green;\">Media is writeable</p>";
                else
                    echo "<p style=\"color: red;\">Media is not writeable</p>";
                if($images_write)
                    echo "<p style=\"color: green;\">Images is writeable</p>";
                else
                    echo "<p style=\"color: red;\">Images is not writeable</p>";
                if($scripts_write)
                    echo "<p style=\"color: green;\">Scripts is writeable</p>";
                else
                    echo "<p style=\"color: red;\">Scripts is not writeable</p>";
                // Try to adjust file permissions
                if(!$media_write || !$images_write || !$scripts_write) {
                    echo "<p>Try to adjust file permissions</p>";
                    chmod("$media_directory", 0777);
                    chmod("$images_directory", 0777);
                    chmod("$script_directory", 0777);
                    echo "<strong><p style=\"color: red;\">Please <a href=\"step1.php\">reload</a> this page, if you still see this page you need to adjust the permissions manually</p></strong>";
                }
                else {
                    echo "<a href=\"step2.php\" class=\"btn btn-success\">Continue</a>";
                }
                ?>
                <br>
            </p>
        </div>
    </body>
</html>