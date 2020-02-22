<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
// This function updates the config
function updateConfig($path, $key, $value)
{
    $content = file_get_contents($path);
    $lines = explode("\n", $content);
    for ($i = 0; $i < sizeof($lines); $i++) {
        // Skip empty lines
        if (strlen($lines[$i]) == 0)
            continue;
        // Skip comment lines
        if ($lines[$i][0] == '/' || $lines[$i][0] == '#')
            continue;

        // Remove tab at the beginning
        if ($lines[$i][0] == "\t")
            $lines[$i] = substr($lines[$i], 1);

        // Get the actual key
        if (substr($lines[$i], 1, strlen($key)) == $key) {
            // Get the comment first
            $comment = strpos($lines[$i], ';');
            if ($comment) {
                $comment = substr($lines[$i], $comment);
                // Cut away semicolon
                $comment = substr($comment, 1);
            }
            $lines[$i] = '$' . $key . ' = ';
            // Add qoutes if it is a string
            if (gettype($value) == 'string') {
                $lines[$i] .= '"' . $value . '";';
            } else {
                $lines[$i] .= $value . ';';
            }
            // Append comment
            $lines[$i] .= $comment;
        }
    }
    // Finally format the config file and make it "beatiful"
    $configStr = '';
    for ($i = 0; $i < sizeof($lines); $i++) {
        if ($lines[$i] == '')
            continue;
        // Skip empty lines
        $configStr .= $lines[$i] . "\n\n";
    }
    // Write to the actual config
    if (!file_put_contents($path, $configStr)) {
        return false;
    }
    return true;
}

// This function allows to get config strings
function getConfig($path = 'config.php')
{
    $configmap = array();
    $content = file_get_contents($path);
    $lines = explode("\n", $content);
    for ($i = 0; $i < sizeof($lines); $i++) {
        // Skip empty lines
        if (strlen($lines[$i]) == 0)
            continue;
        // Skip comment and php lines
        if ($lines[$i][0] == '/' || $lines[$i][0] == '#')
            continue;
        // Remove tab at the beginning
        if ($lines[$i][0] == "\t")
            $lines[$i] = substr($lines[$i], 1);

        preg_match('/\$(.+?) = ["\']{0,1}(.+?)["\']{0,1};/', $lines[$i], $output_array);
        if (sizeof($output_array) != 3) {
            continue;
        }
        // Cut of escape chars if there are any
        // Check if $output_array[2] is "
        if ($output_array[2] != '"') {
            $output_array[2] = str_replace("\\", '', $output_array[2]);
            $configmap[$output_array[1]] = $output_array[2];
        } else {
            $configmap[$output_array[1]] = '';
        }
    }
    // Pop first (<?php) element
    unset($configmap[""]);
    return $configmap;
}

function unsetConfig($path = "config.php", $key)
{
    $content = file_get_contents($path);
    $lines = explode("\n", $content);
    for ($i = 0; $i < sizeof($lines); $i++) {
        // Skip empty lines
        if (strlen($lines[$i]) == 0)
            continue;
        // Skip comment lines
        if ($lines[$i][0] == '/' || $lines[$i][0] == '#')
            continue;

        // Remove tab at the beginning
        if ($lines[$i][0] == "\t")
            $lines[$i] = substr($lines[$i], 1);

        // Get the actual key
        if (substr($lines[$i], 1, strlen($key)) == $key) {
            unset($lines[$i]);
        }
    }
    $configStr = '';
    for ($i = 0; $i < sizeof($lines); $i++) {
        $configStr .= $lines[$i] . "\n";
    }
    // Write to the actual config
    if (!file_put_contents($path, $configStr)) {
        return false;
    }
    return true;
}