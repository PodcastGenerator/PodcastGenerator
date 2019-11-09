<?php
function getFreebox() {
    $_config = getConfig("config.php");
    if($_config["freebox"] != "yes") {
        return null;
    }
    return file_get_contents("freebox-content.txt");
}
?>