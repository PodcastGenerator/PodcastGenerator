<?php
// Set language
putenv('LANG=' . $config['scriptlang']);
setlocale(LC_ALL, $config['scriptlang']);
$domain = 'messages';
bindtextdomain($domain, $config['absoluteurl'].'components/locale');
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);