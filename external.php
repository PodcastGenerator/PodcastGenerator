<?php
require "core/include.php";
if (isset($_GET["name"])) {
    $buttons = getButtons("./");
    // Regenerate feed on every access (just for the case)
    generateRSS("./");
    foreach ($buttons as $item) {
        if ($_GET["name"] == $item->name) {
            if (!isset($item->protocol)) {
                header("Location: " . $item->href);
                die();
            } else {
                header("Location: " . $item->protocol . "://" . str_replace("http://", "", str_replace("https://", "", $config["url"])) . $item->href);
                die();
            }
        }
    }
}
