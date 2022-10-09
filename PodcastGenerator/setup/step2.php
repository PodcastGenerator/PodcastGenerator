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
if (!isset($_SESSION)) {
    session_start();
}

// Dirs
$rootdir = dirname(__DIR__);
$dirs = [
    'Media' => ['path' => $rootdir . '/media', 'exists' => false, 'writable' => false],
    'Images' => ['path' => $rootdir . '/images', 'exists' => false, 'writable' => false],
    'Scripts' => ['path' => $rootdir /* index.php dir */, 'exists' => false, 'writable' => false]
];

// Creating all testfiles
foreach ($dirs as $name => $props) {
    $path = $props['path'];
    $exists = file_exists($path) && is_dir($path);
    $props['exists'] = $exists;

    if (!$exists) {
        continue; // no point trying write test
    }

    $testfile = $path . '/test.txt';

    $f = fopen($testfile, 'w');
    if ($f === false) {
        // if we can't open a file handle for write, we can't write in this dir
        $props['writable'] = false;
        continue;
    }

    // write some test content and close the handle
    fwrite($f, 'test');
    fclose($f);

    // verify that the test file exists
    if (file_exists($testfile)) {
        unlink($testfile);
        $props['writable'] = true;
    }
}

// are all directories writable?
$all_writable = true;
foreach ($dirs as $name => $props) {
    $all_writable |= $props['writable'];
}

function textColor($success)
{
    return $success ? 'green' : 'red';
}
function isIsNot($success)
{
    return $success ? 'is' : 'is not';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Podcast Generator - Step 2</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
</head>

<body class="bg-light">
    <div class="container m-auto">
        <div class="align-items-center justify-content-md-center p-3 row vh-100">
            <div class="col-xl-7 col-lg-7 col-md-10 col-sm-12 bg-white p-4 shadow">
                <h2>Podcast Generator - <small>Step 2</small></h2>
                <p><small>We are now checking if our data directories are writable so you can actually store the data.</small></p>
                <?php foreach ($dirs as $name => $props) { ?>
                    <p style="color: <?= textColor($props['writable']) ?>">
                        <span title="<?= $props['path'] ?>"><?= $name ?></span>
                        <?= isIsNot($props['writable']) ?> writable
                    </p>
                <?php } ?>
                <?php if (!$all_writable) { /* Try to adjust file permissions */ ?>
                    <p>Trying to adjust file permissions...</p>
                    <?php foreach ($dirs as $name => $props) { ?>
                        <ul>
                            <li>
                                <span title="<?= $props['path'] ?>"><?= $name ?></span>:
                                <?= chmod($props['path'], 0777) ? 'success' : 'failure' ?>
                            </li>
                        </ul>
                    <?php } ?>
                    <p style="color: red;"><strong>Please <a href="step2.php">reload</a> this page, if you still see this message you will need to adjust the permissions manually</strong></p>
                <?php } else { ?>
                    <a href="step3.php" class="btn btn-success btn-block">Continue</a>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>
