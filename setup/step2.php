<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('checkconfigexistence.php');

$PG_mainbody = NULL; //define

include	('set_permissions.php');

//print output

echo $PG_mainbody;

?>
