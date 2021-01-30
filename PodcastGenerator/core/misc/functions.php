<?php
// When this file is included, config MUST be included first!
function getmime($filename)
{
    global $config;
    require_once $config['absoluteurl'] . 'components/getid3/getid3.php';
    // Check if file is even readable
    if(!is_readable($filename))
        return false;
    // Analyze file to dtermine mime type
    $getID3 = new getID3;
    $fileinfo = $getID3->analyze($filename);
    return $fileinfo["mime_type"];
}

function checkLogin($username, $password_plain)
{
    global $config;
    $users =  json_decode($config['users_json'], true);
    foreach($users as $uname => $password_hash) {
        if($username == $uname) {
            // This is the correct user, now verify password
            return password_verify($password_plain, $password_hash);
        }
    }
    return false;
}

function addUser($username, $password_plain)
{
    global $config;
    $users =  json_decode($config['users_json'], true);
    // Check if user exists
    if(array_key_exists($username, $users))
        return false;
    $users[$username] = password_hash($password_plain, PASSWORD_DEFAULT);
    return updateConfig($config['absoluteurl'] . 'config.php', 'users_json', str_replace('"', '\"', json_encode($users)));
}

function deleteUser($username)
{
    global $config;
    $users =  json_decode($config['users_json'], true);
    unset($users[$username]);
    return updateConfig($config['absoluteurl'] . 'config.php', 'users_json', str_replace('"', '\"', json_encode($users)));
}

function changeUserPassword($username, $new_password_plain)
{
    global $config;
    $users = json_decode($config['users_json'], true);
    // Check if user exists
    if(!array_key_exists($username, $users))
        return false;
    $users[$username] = password_hash($new_password_plain, PASSWORD_DEFAULT);
    return updateConfig($config['absoluteurl'] . 'config.php', 'users_json', str_replace('"', '\"', json_encode($users)));
}

function getUsers()
{
    global $config;
    return json_decode($config['users_json'], true);
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
    if(!isset($_POST['token']) || ($_POST['token'] != $_SESSION['token']))
        die("Potential CSRF attack");
}

function checkPath($path)
{
    if(preg_match('/\.\./', $path) === 1)
        die("Potential escape attack");
}
