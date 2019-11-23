<?php
require "core/include.php";
$categories = simplexml_load_file("categories.xml");
$episodes = getEpisodes($_GET["cat"]);
$episode_chunk = $episodes;

// Backwards comp
$link = str_replace("?", "", $config["link"]);
$link = str_replace("=", "", $link);
$link = str_replace("\$url", "", $link);

require $config["theme_path"]."categories.php";
?>