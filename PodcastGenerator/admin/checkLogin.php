<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
    header('Location: login.php');
    die();
}
