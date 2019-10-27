<?php
include "misc/security.php";
include "misc/configsystem.php";
// Until Podcast Generator 3.0 passwords were stored in MD5, which is inseucre since 2005
// This file is wizard to convert old password to a more secure algorithim
$config = getConfig("config.php");
// Check if the hash is MD5
if(strlen($config["userpassword"]) == 32) {
    header("Location: core/misc/passwordconverter.php");
    die();
}
unset($config);