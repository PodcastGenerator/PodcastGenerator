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
foreach ($languages as $item) {
    array_push($supported_codes, $item->code);
}

if (isset($_GET['done'])) {
    // Use english as fallback language, if no valid language was provided
    if (!in_array($_POST['lang'], $supported_codes)) {
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

<body class="bg-light">
    <div class="container m-auto">
        <div class="align-items-center justify-content-md-center p-3 row vh-100">
            <div class="col-xl-7 col-lg-7 col-md-10 col-sm-12 bg-white p-4 shadow">
                <h2>Podcast Generator - <small>Step 1</small></h2>
                <p class="lead">Please choose a language</p>
                <form action="step1.php?done=1" method="POST">
                    <select class="custom-select mb-4" name="lang">
                        <?php
                        foreach ($languages as $item) {
                            echo '<option value=' . $item->code . '>' . $item->name . '</option>' . "\n";
                        }
                        ?>
                    </select>
                    <input type="submit" value="Submit" class="btn btn-success mb-3 btn-block">
                </form>
                <small>If your desired language can't be choosen, you should execute <code>locale -a</code> and might append <code>.utf8</code> to <code>scriptlang</code> in the config</small>
            </div>
        </div>
    </div>
</body>

</html>