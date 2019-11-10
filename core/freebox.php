<?php
function getFreebox($path = null) {
    $_config = getConfig($path . "config.php");
    if($_config["freebox"] != "yes") {
        return null;
    }
    return file_get_contents($path . "freebox-content.txt");
}

function updateFreebox($path = null, $content) {
    return file_put_contents($path . "freebox-content.txt", $content);
}
?>