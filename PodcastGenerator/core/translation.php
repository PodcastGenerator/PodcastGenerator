<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
// Set language
putenv('LANG=' . $config['scriptlang']);
setlocale(LC_ALL, $config['scriptlang']);
$domain = 'messages';
bindtextdomain($domain, $config['absoluteurl'] . 'components/locale');
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);
