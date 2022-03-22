<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

require_once(__DIR__ . '/../../vendor/autoload.php');

function getmime($filename)
{
    // Check if file is even readable
    if (!is_readable($filename)) {
        return false;
    }
    // Analyze file to determine mime type
    $getID3 = new getID3();
    $fileinfo = $getID3->analyze($filename);
    return $fileinfo["mime_type"];
}

function checkLogin($username, $password_plain)
{
    global $config;
    $users =  json_decode($config['users_json'], true);
    foreach ($users as $uname => $password_hash) {
        if ($username == $uname) {
            // This is the correct user, now verify password
            return password_verify($password_plain, $password_hash);
        }
    }
    return false;
}

function randomString($length = 8)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function checkToken()
{
    if (!isset($_POST['token']) || ($_POST['token'] != $_SESSION['token'])) {
        die("Potential CSRF attack");
    }
}

function checkPath($path)
{
    if (preg_match('/\.\./', $path) === 1) {
        die("Potential escape attack");
    }
}

function isWellFormedXml($xmlString)
{
    if ($xmlString == null || $xmlString == '') {
        return true;
    }

    // Heredoc screws up the XML declaration, so we need to put it in like this.
    // Not including the declaration makes simplexml angry, so we need it.
    $wrapped = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $wrapped .= <<<XML
<checkXml xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
          xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"
          xmlns:atom="http://www.w3.org/2005/Atom"
          xmlns:podcast="https://podcastindex.org/namespace/1.0">
$xmlString
</checkXml>
XML;

    try {
        $xml = simplexml_load_string($wrapped);
        return $xml !== false;
    } catch (Exception $ex) {
        return false;
    }
}

/**
 * Creates a unique filename based on the provided path.
 *
 * @param string $path  The full path of the filename that needs to be made unique.
 * @return string       A unique filename in the same directory.
 */
function makeUniqueFilename($path)
{
    // make sure that we have a real directory path with filename attached
    // just putting $path into realpath() will fail if the file doesn't exist
    $pathinfo = pathinfo($path);
    $realpath = realpath($pathinfo['dirname']) . '/' . $pathinfo['basename'];

    // if the existing path doesn't exist, we're unique!
    if (!file_exists($realpath)) {
        return $realpath;
    }

    // otherwise, append a number and increment it until we find a unique path
    $appendix = 0;
    $pathinfo = pathinfo($realpath);
    while (file_exists($realpath)) {
        $appendix += 1;
        $realpath = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $appendix . '.' . $pathinfo['extension'];
    }
    return $realpath;
}
