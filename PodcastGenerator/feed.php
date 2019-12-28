<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require 'core/include.php';
header('Content-Type: application/xml');
generateRSS();
sleep(0.01);
$xml = file_get_contents($config['absoluteurl'] . $config['feed_dir']);
echo $xml;
?>