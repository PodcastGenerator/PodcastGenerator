<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require "securitycheck.php";
require "createconf.php";
require "createstuff.php";
if (!isset($_SESSION))
    session_start();

if (isset($_GET["create"])) {
    $p = $_POST;
    if (empty($p["username"]) || empty($p["password"]) || empty($p["password2"])) {
        $error = "Emtyp fields";
    }
    if (!isset($error)) {
        if ($p["password"] != $p["password2"]) {
            $error = "Passwords don't match";
        }
        // Now create the config file
        if (!isset($error)) {
            if (createconf($p["username"], $p["password"]))
                $success = true;
            else
                $error = "Failure while creating the config file";
            if (createstuff())
                $success = true;
            else
                $error = "Failure while creating categories file";
        }
        if ($success) {
            session_destroy();
            header("Location: ../index.php");
            die();
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Podcast Generator - Step 3</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
</head>

<body class="bg-light">
    <div class="container m-auto">
        <div class="align-items-center justify-content-md-center p-3 row vh-100">
            <div class="col-xl-7 col-lg-7 col-md-10 col-sm-12 bg-white p-4 shadow">
                <h2>Podcast Generator - <small>Step 3</small></h2>
                <p><small>We are now creating the admin account for the admin area.</small></p>
                <form method="POST" action="step3.php?create=1">
                    <div class="form-group">
                        <label for="username">Enter Username:</label>
                        <input type="text" class="form-control" name="username" id="username" name="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Enter Password:</label>
                        <input type="password" class="form-control" name="password" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="password2">Repeat Password:</label>
                        <input type="password" class="form-control" name="password2" id="password2" name="password2">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-success btn-block">Submit</button>
                </form>
                <?php
                if (isset($error)) {
                    echo "<strong><p style=\"color: red;\">Error: $error</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>