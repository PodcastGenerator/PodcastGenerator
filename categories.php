<?php
require "core/include.php";
$config = getConfig();
$categories = simplexml_load_file("categories.xml");
require $config["theme_path"]."categories.php";
?>