<?php
session_start();
if(isset($_SESSION["username"])) {
    header("Location: index.php");
    die();
}
require "../core/include_admin.php";

if(isset($_GET["login"])) {
    if(empty($_POST["username"]) || empty($_POST["password"])) {
        $error = "Missing fields";
        goto esc;
    }
    if($_POST["username"] == $config["username"] && password_verify($_POST["password"], $config["userpassword"])) {
        $_SESSION["username"] = $config["username"];
        header("Location: index.php");
        die();
    }
    else {
        $error = "Invalid username or password";
    }
    esc:
    echo ("");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo htmlspecialchars($config["podcast_title"]); ?> - Admin</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../core/bootstrap/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    </head>
    <body>
        <div class="container">
            <h1><?php echo htmlspecialchars($config["podcast_title"]); ?> - Login</h1>
            <?php
            if(isset($error)) {
                echo "<strong><p style='color: red;'>$error</p></strong>";
            }
            ?>            
            <form action="login.php?login=1" method="POST">
                Username:<br>
                <input type="text" name="username"><br>
                Password:<br>
                <input type="password" name="password"><br>
                <br>
                <input type="submit" value="Sign In" class="btn btn-success">
            </form>
        </div>
    </body>
</html>