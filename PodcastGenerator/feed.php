<?php
require 'core/include.php';
header('Content-Type: application/xml');
generateRSS();
sleep(0.01);
$xml = file_get_contents($config['absoluteurl'] . $config['feed_dir']);
echo $xml;
?>