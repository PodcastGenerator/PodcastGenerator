<?php
function getmime($filename)
{
    // Check if file is even readable
    if(!is_readable($filename))
        return false;
    return mime_content_type($filename);
}

function checkLogin($username, $password_plain)
{
    global $config;
    $users =  json_decode(file_get_contents($config['absoluteurl'] . 'users.json'), true);
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
    $users =  json_decode(file_get_contents($config['absoluteurl'] . 'users.json'), true);
    // Check if user exists
    if(array_key_exists($username, $users))
        return false;
    $users[$username] = password_hash($password_plain, PASSWORD_DEFAULT);
    return file_put_contents($config['absoluteurl'] . 'users.json', json_encode($users));
}

function deleteUser($username)
{
    global $config;
    $users =  json_decode(file_get_contents($config['absoluteurl'] . 'users.json'), true);
    unset($users[$username]);
    return file_put_contents($config['absoluteurl'] . 'users.json', json_encode($users));
}

function changeUserPassword($username, $new_password_plain)
{
    global $config;
    $users =  json_decode(file_get_contents($config['absoluteurl'] . 'users.json'), true);
    // Check if user exists
    if(!array_key_exists($username, $users))
        return false;
    $users[$username] = password_hash($new_password_plain, PASSWORD_DEFAULT);
    return file_put_contents($config['absoluteurl'] . 'users.json', json_encode($users));
}

function getUsers()
{
    global $config;
    return json_decode(file_get_contents($config['absoluteurl'] . 'users.json'), true);
}