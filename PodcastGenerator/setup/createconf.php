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
function randomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function createconf($username, $password) {
    require "../core/misc/globs.php";
    $installtime = time();
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    // Replace config stuff
    $url = str_replace("setup/step3.php?create=1", "", $url);
    $absoluteurl = realpath("../")."/";
    $installationKey = randomString();
    $password = password_hash($password, PASSWORD_DEFAULT);

    $config = "<?php
\$podcastgen_version = \"$version\"; // Version

\$first_installation = $installtime;

\$installationKey = \"$installationKey\";

\$scriptlang = \"".$_SESSION['lang']."\";

\$url = \"$url\";

\$absoluteurl = \"$absoluteurl\"; // The location on the server

\$theme_path = \"themes/default/\";

\$upload_dir = \"media/\"; // \"media/\" the default folder (Trailing slash required). Set chmod 755

\$img_dir = \"images/\"; // (Trailing slash required). Set chmod 755

\$feed_dir = \"\"; // Where to create feed.xml (empty value = root directory). Set chmod 755

\$max_recent = \"all\"; // How many file to show in the home page

\$recent_episode_in_feed = \"All\"; // How many file to show in the XML feed (1,2,5 etc.. or \"All\")

\$episodeperpage = 10;

\$enablestreaming = \"yes\"; // Enable mp3 streaming? (\"yes\" or \"no\")

\$freebox = \"yes\"; // enable freely customizable box

\$enablepgnewsinadmin = \"yes\";

\$strictfilenamepolicy = \"no\"; // strictly rename files (just characters A to Z and numbers) 

\$categoriesenabled = \"yes\";

\$cronAutoIndex = 1; //Auto Index New Episodes via Cron

\$cronAutoRegenerateRSS = 1; //Auto regenerate RSS via Cron

\$indexfile = \"index.php\";    // Path of the index file

\$podcastPassword = \"\";       // Password to protect the podcast generator webpages, this will NOT protect the audio or XML files. Leave blank to disable.

#####################
# XML Feed stuff

\$podcast_title = \"Podcast Title\";

\$podcast_subtitle = \"Subtitle\";

\$podcast_description = \"A little description of your podcast.\";

\$author_name = \"Podcast Generator User\";

\$author_email = \"podcastgenerator@example.com\";

# The e-amil of the technical admin of the podcast
\$webmaster = \"podcastadmin@example.com\";

\$itunes_category[0] = \"Arts\"; // iTunes categories (mainCategory:subcategory)
\$itunes_category[1] = \"\";
\$itunes_category[2] = \"\";

\$link = \"?name=\"; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)

\$feed_language = \"en\";

\$copyright = \"All rights reserved\";   // Your copyright notice (e.g CC-BY)

\$feed_encoding = \"utf-8\";

\$explicit_podcast = \"no\"; //does your podcast contain explicit language? (\"yes\" or \"no\")

\$users_json = \"{\\\"".$username."\\\": \\\"".str_replace("\$", "\\\$", $password)."\\\"}\";

// END OF CONFIG
";
    $f = fopen("../config.php", 'w');
    fwrite($f, $config);
    fclose($f);
    // Check if file exists
    if(file_exists("../config.php")) {
        return true;
    }
    return false;
}