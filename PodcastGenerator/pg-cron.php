<?php
require 'core/include.php';
// Only work when cronAutoIndex is enabled
if(isset($_GET['key']) && $config['cronAutoIndex'] == "1") {
    if($_GET['key'] == $config['installationKey']) {
        generateRSS();
    }
}
?>