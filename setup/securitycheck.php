<?php
require "../core/globs.php";
if(file_exists("../config.php")) {
    header("Location: ../index.php");
    die();
}
?>