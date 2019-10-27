<?php
require "../core/misc/globs.php";
if(file_exists("../config.php")) {
    header("Location: ../index.php");
    die();
}
?>