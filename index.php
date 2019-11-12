<?php
session_start();
require "core/include.php";
// Regenerate RSS feed on every page access
generateRSS("./");
$episodes = getEpisodes();
$buttons = getButtons("./");
require $config["theme_path"]."index.php";
?>