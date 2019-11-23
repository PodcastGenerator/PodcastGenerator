<?php
session_start();
require "core/include.php";
generateRSS("./");
$episodes = getEpisodes(null);

// When calling name
// Backwards comp
$link = str_replace("?", "", $config["link"]);
$link = str_replace("=", "", $link);
$link = str_replace("\$url", "", $link);

if(strtolower($config["max_recent"]) != "all") {
    $episodes = array_slice($episodes, 0, $config["max_recent"]);
}

$splitted_episodes = array_chunk($episodes, intval($config["episodeperpage"]));
$episode_chunk = null;
if(isset($_GET["page"])) {
    $episode_chunk = $splitted_episodes[intval(($_GET["page"]) - 1)];
}
else {
    $episode_chunk = $splitted_episodes[0];
}

$buttons = getButtons("./");
require $config["theme_path"]."index.php";
?>