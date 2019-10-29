<?php
require "securitycheck.php";
require "createconf.php";
require "createstuff.php";

if(isset($_GET["create"])) {
    $p = $_POST;
    if(empty($p["username"]) || empty($p["password"]) || empty($p["password2"])) {
        $error = "Emtyp fields";
    }
    if(!isset($error)) {
        if($p["password"] != $p["password2"]) {
            $error = "Passwords don't match";
        }
        // Now create the config file
        if(!isset($error)) {
            if(createconf($p["username"], $p["password"]))
                $success = true;
            else
                $error = "Failure while creating the config file";
            if(createstuff())
                $success = true;
            else
                $error = "Failure while creating categories file";
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Podcast Generator - Step 2</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../core/bootstrap/style.css">
    </head>
    <body>
        <div class="container">
            <h1>Podcast Generator - Step 2</h1>
            <p>
                We are now creating the admin account for the admin area.<br>
                <form method="POST" action="step2.php?create=1">
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
                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
                <br>
                <?php
                if(isset($error)) {
                    echo "<strong><p style=\"color: red;\">Error: $error</p>";
                }
                if(isset($success)) {
                    header("Location: ../index.php");
                    die();
                }
                ?>
                
                <br>
            </p>
        </div>
    </body>
</html>