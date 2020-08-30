<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
function backwards_3_1_to_3_2($absoluteurl)
{
    global $config;
    global $version;
    // Quit if version is not 3.0
    if(!($config['podcastgen_version'] == '3.1')) {
        return;
    }
    $config_php = "<?php
\$podcastgen_version = \"3.2\"; // Version

\$first_installation = ".$config['first_installation'].";

\$installationKey = \"".$config['installationKey']."\";

\$scriptlang = \"".$config['scriptlang']."\";

\$url = \"".$config['url']."\";

\$absoluteurl = \"".$config['absoluteurl']."\"; // The location on the server

\$theme_path = \"".$config['theme_path']."\";

\$upload_dir = \"".$config['upload_dir']."\"; // \"media/\" the default folder (Trailing slash required). Set chmod 755

\$img_dir = \"".$config['img_dir']."\"; // (Trailing slash required). Set chmod 755

\$feed_dir = \"".$config['feed_dir']."\"; // Where to create feed.xml (empty value = root directory). Set chmod 755

\$max_recent = ".$config['max_recent']."; // How many file to show in the home page

\$recent_episode_in_feed = \"".$config['recent_episode_in_feed']."\"; // How many file to show in the XML feed (1,2,5 etc.. or \"All\")

\$episodeperpage = ".$config['episodeperpage'].";

\$enablestreaming = \"".$config['enablestreaming']."\"; // Enable mp3 streaming? (\"yes\" or \"no\")

\$freebox = \"".$config['freebox']."\"; // enable freely customizable box

\$enablepgnewsinadmin = \"".$config['enablepgnewsinadmin']."\";

\$strictfilenamepolicy = \"".$config['strictfilenamepolicy']."\"; // strictly rename files (just characters A to Z and numbers) 

\$categoriesenabled = \"".$config['categoriesenabled']."\";

\$cronAutoIndex = ".$config['cronAutoIndex']."; //Auto Index New Episodes via Cron

\$cronAutoRegenerateRSS = ".$config['cronAutoRegenerateRSS']."; //Auto regenerate RSS via Cron

\$indexfile = \"index.php\";    // Path of the index file

\$podcastPassword = \"\";       // Password to protect the podcast generator webpages, this will NOT protect the audio or XML files. Leave blank to disable.

#####################
# XML Feed stuff

\$podcast_title = \"".$config['podcast_title']."\";

\$podcast_subtitle = \"".$config['podcast_subtitle']."\";

\$podcast_description = \"".$config['podcast_description']."\";

\$author_name = \"".$config['author_name']."\";

\$author_email = \"".$config['author_email']."\";

# The e-amil of the technical admin of the podcast
\$webmaster = \"".$config['author_email']."\";

\$itunes_category[0] = \"".$config['itunes_category[0]']."\"; // iTunes categories (mainCategory:subcategory)
\$itunes_category[1] = \"".$config['itunes_category[1]']."\";
\$itunes_category[2] = \"".$config['itunes_category[2]']."\";

\$link = \"".$config['link']."\"; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)

\$feed_language = \"".$config['feed_language']."\";

\$copyright = \"".$config['copyright']."\";   // Your copyright notice (e.g CC-BY)

\$feed_encoding = \"".$config['feed_encoding']."\";

\$explicit_podcast = \"".$config['explicit_podcast']."\"; //does your podcast contain explicit language? (\"yes\" or \"no\")

\$users_json = \"".$config['users_json']."\";

// END OF CONFIG
";
    file_put_contents($absoluteurl . 'config.php', $config_php);
}
