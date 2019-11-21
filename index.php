<?php
session_start();
require "core/include.php";
$episodes = getEpisodes(null);

// When calling name
// Backwards comp
$link = str_replace("?", "", $config["link"]);
$link = str_replace("=", "", $link);
$link = str_replace("\$url", "", $link);

if(strtolower($config["max_recent"]) != "all") {
    $episodes = array_slice($episodes, 0, $config["max_recent"]);
}

$buttons = getButtons("./");
require $config["theme_path"]."index.php";
?>