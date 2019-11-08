<?php
require "core/include.php";
$categories = simplexml_load_file("categories.xml");
require $config["theme_path"]."categories.php";
?>