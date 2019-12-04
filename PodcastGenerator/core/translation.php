<?php
// Set language
putenv('LC_ALL=en_EN');
setlocale(LC_ALL, 'en_EN');

// Specifiy location of translation sets
bindtextdomain("PodcastGenerator", $config["absoluteurl"].'components/locale');

// Choose domain
textdomain("PodcastGenerator");