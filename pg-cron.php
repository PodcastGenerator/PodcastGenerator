<?php
require "core/include.php";
if(isset($_GET["key"])) {
    if($_GET["key"] == $config["installation_key"]) {
        generateRSS("./");
    }
}
?>