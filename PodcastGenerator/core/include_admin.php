<?php
// This file is intended to be only used in the admin directory
if(!file_exists("../config.php")) {
    header("Location: ../setup/");
    die();
}
include "misc/configsystem.php";
include "misc/globs.php";
include "buttons.php";
// Until Podcast Generator 3.0 passwords were stored in MD5, which is inseucre since 2005
// This file is wizard to convert old password to a more secure algorithim
$config = getConfig("../config.php");
// Check if the hash is MD5
if(strlen($config["userpassword"]) == 32) {
    header("Location: ../core/misc/passwordconverter.php");
    die();
}
include "feed_generator.php";
include "freebox.php";
include "backwards.php";
backwards_2_7_to_3_0($config["absoluteurl"]);