<?php
session_start();
require "core/include.php";
// Regenerate RSS feed on every page access
generateRSS("./");
$episodes = getEpisodes(null);

if(strtolower($config["max_recent"]) != "all") {
    $episodes = array_slice($episodes, 0, $config["max_recent"]);
}

$buttons = getButtons("./");
require $config["theme_path"]."index.php";
?>