<?php
require "core/include.php";
$categories = simplexml_load_file("categories.xml");
$episodes = getEpisodes($_GET["cat"]);
require $config["theme_path"]."categories.php";
?>