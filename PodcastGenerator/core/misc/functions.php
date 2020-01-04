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
    $users =  json_decode(file_get_contents($config['absoluteurl'] . 'users.json'));
    foreach($users as $uname => $password_hash) {
        if($username == $uname) {
            // This is the correct user, now verify password
            return password_verify($password_plain, $password_hash);
        }
    }
    return false;
}