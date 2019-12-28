<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
function getButtons($path = '../')
{
    return simplexml_load_file($path . 'buttons.xml');
}
