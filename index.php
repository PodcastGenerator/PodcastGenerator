<?php
session_start();
require "core/include.php";
$episodes = getEpisodes();
require $config["theme_path"]."index.php";
?>