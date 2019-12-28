<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require 'securitycheck.php';
session_start();
$languages = simplexml_load_file('../components/supported_languages/supported_languages.xml');
$supported_codes = array();
foreach($languages as $item) {
    array_push($supported_codes, $item->code);
}

if(isset($_GET['done'])) {
    // Use english as fallback language, if no valid language was provided
    if(!in_array($_POST['lang'], $supported_codes)) {
        $_POST['lang'] = 'en_US';
    }
    $_SESSION['lang'] = $_POST['lang'];
    header('Location: step2.php');
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Podcast Generator - Step 1</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
</head>

<body>
    <div class="container">
        <h1>Podcast Generator - Step 1</h1>
        <h3>Please choose a language</h3>
        <p>
            <form action="step1.php?done=1" method="POST">
                <select name="lang">
                    <?php
                    foreach ($languages as $item) {
                        echo '<option value=' . $item->code . '>' . $item->name . '</option>'."\n";
                    }
                    ?>
                </select>
                <br>
                <br>
                <input type="submit" value="Submit" class="btn btn-success">
            </form>
            <br>
            <small>If your desired language can't be choosen, you should execute <code>locale -a</code> and might append <code>.utf8</code> to <code>scriptlang</code> in the config</small>
        </p>
    </div>
</body>

</html>