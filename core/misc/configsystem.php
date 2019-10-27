<?php
// This function updates the config
function updateConfig($path, $key, $value) {
    $content = file_get_contents($path);
    $lines = explode("\n", $content);
    for($i = 0; $i < sizeof($lines); $i++) {
        // Skip empty lines
        if(strlen($lines[$i]) == 0)
            continue;
        // Skip comment lines
        if($lines[$i][0] == "/" || $lines[$i][0] == "#")
            continue;

        // Get the actual key
        if(substr($lines[$i], 1, strlen($key)) == $key) {
            // Get the comment first
            $comment = strpos($lines[$i], ";");
            if($comment) {
                $comment = substr($lines[$i], $comment);
                // Cut away semicolon
                $comment = substr($comment, 1);
            }
            $lines[$i] = "\$".$key." = ";
            // Add qoutes if it is a string
            if(gettype($value) == "string") {
                $lines[$i] .= "\"$value\";";
            }
            else {
                $lines[$i] .= "$value;";
            }
            // Append comment
            $lines[$i] .= $comment;
        }
    }
    $configStr = "";
    for($i = 0; $i < sizeof($lines); $i++) {
        $configStr .= $lines[$i]."\n";
    }
    // Write to the actual config
    if(!file_put_contents($path, $configStr)) {
        return false;
    }
    return true;
}

// This function allows to get config strings
function getConfig($path = "config.php") {
    $configmap = array();
    $content = file_get_contents($path);
    $lines = explode("\n", $content);
    for($i = 0; $i < sizeof($lines); $i++) {
        // Skip empty lines
        if(strlen($lines[$i]) == 0)
            continue;
        // Skip comment and php lines
        if($lines[$i][0] == "/" || $lines[$i][0] == "#")
            continue;

        preg_match('/\$(.+?) = ["]{0,1}(.+?)["]{0,1};/', $lines[$i], $output_array);
        // CHeck if $output_array[2] is "
        if($output_array[2] != "\"")
            $configmap[$output_array[1]] = $output_array[2];
        else
            $configmap[$output_array[1]] = "";
    }
    // Pop first (<?php) element
    unset($configmap[""]);
    return $configmap;
}