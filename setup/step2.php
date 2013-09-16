<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('checkconfigexistence.php');

$PG_mainbody = NULL; //define

include	('set_permissions.php');

//print output

echo $PG_mainbody;

?>
