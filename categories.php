<?php
require "core/include.php";
// Kill the connection if categories are disabled
if(strtolower($config["categoriesenabled"]) != "yes") {
    header("Location: index.php");
    die();
}
$categories = simplexml_load_file("categories.xml");
$episodes = null;
if(isset($_GET["cat"])) {
    $episodes = getEpisodes($_GET["cat"]);
}
$episode_chunk = $episodes;

// Backwards comp
$link = str_replace("?", "", $config["link"]);
$link = str_replace("=", "", $link);
$link = str_replace("\$url", "", $link);

$buttons = getButtons("./");
require $config["theme_path"]."categories.php";
?>