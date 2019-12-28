<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require "../core/misc/globs.php";
if(file_exists("../config.php")) {
    header("Location: ../index.php");
    die();
}
?>