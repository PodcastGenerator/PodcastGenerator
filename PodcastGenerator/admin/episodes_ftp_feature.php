<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require 'checkLogin.php';
require '../core/include_admin.php';

if (isset($_GET['start'])) {
    $new_files = array();
    // Get all files and check if they have an XML file associated
    if ($handle = opendir('../' . $config['upload_dir'])) {
        while (false !== ($entry = readdir($handle))) {
            // Skip dotfiles
            if (substr($entry, 0, 1) == '.') {
                continue;
            }
            // Skip XML files
            if (pathinfo('../' . $config['upload_dir'] . $entry, PATHINFO_EXTENSION) == 'xml') {
                continue;
            }
            // Check if an XML file for that episode exists
            if (file_exists('../' . $config['upload_dir'] . pathinfo('../' . $config['upload_dir'] . $entry, PATHINFO_FILENAME) . '.xml')) {
                continue;
            }
            array_push($new_files, $entry);
        }
    }
    require '../components/getid3/getid3.php';
    // Generate XML from audio file (with mostly empty values)
    for ($i = 0; $i < sizeof($new_files); $i++) {
        // Skip files if they are not strictly named
        if ($config['strictfilenamepolicy'] == 'yes') {
            if (!preg_match('/^[\w.]+$/', $new_files[$i])) {
                continue;
            }
        }
        // Get audio metadata (duration, bitrate etc)
        $getID3 = new getID3;
        $fileinfo = $getID3->analyze('../' . $config['upload_dir'] . $new_files[$i]);
        $duration = $fileinfo['playtime_string'];           // Get duration
        $bitrate = $fileinfo['audio']['bitrate'];           // Get bitrate
        $frequency = $fileinfo['audio']['sample_rate'];     // Frequency

        $episodefeed = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
	<episode>
	    <titlePG><![CDATA[' . pathinfo('../' . $config['upload_dir'] . $new_files[$i], PATHINFO_FILENAME) . ']]></titlePG>
	    <shortdescPG><![CDATA[]]></shortdescPG>
	    <longdescPG><![CDATA[]]></longdescPG>
	    <imgPG></imgPG>
	    <categoriesPG>
	        <category1PG>uncategorized</category1PG>
	        <category2PG></category2PG>
	        <category3PG></category3PG>
	    </categoriesPG>
	    <keywordsPG><![CDATA[]]></keywordsPG>
	    <explicitPG>' . $config['explicit_podcast'] . '</explicitPG>
	    <authorPG>
	        <namePG>' . $config['author_name'] . '</namePG>
	        <emailPG>' . $config['author_email'] . '</emailPG>
	    </authorPG>
	    <fileInfoPG>
	        <size>' . intval(filesize('../' . $config['upload_dir'] . $new_files[$i]) / 1000 / 1000) . '</size>
	        <duration>' . $duration . '</duration>
	        <bitrate>' . substr(strval($bitrate), 0, 3) . '</bitrate>
	        <frequency>' . $frequency . '</frequency>
	    </fileInfoPG>
	</episode>
</PodcastGenerator>';
        // Select new filenames (with date) if not already exsits
        preg_match('/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/', $new_files[$i], $output_array);
        $fname = $new_files[$i];
        if (sizeof($output_array) == 0) {
            $new_filename = '../' . $config['upload_dir'] . date('Y-m-d') . '-' . $new_files[$i];
            $appendix = 1;
            while (file_exists($new_filename)) {
                $new_filename = '../' . $config['upload_dir'] . date('Y-m-d') . '-' . $appendix . '-' . basename($new_files[$i]);
                $appendix++;
            }
            rename('../' . $config['upload_dir'] . $new_files[$i], $new_filename);
            $fname = $new_filename;
        }
        // Write XML file
        file_put_contents('../' . $config['upload_dir'] . pathinfo($fname, PATHINFO_FILENAME) . '.xml', $episodefeed);
        // Regenarte RSS feed
        generateRSS();
        $success = _('New episodes were added');
        sleep(0.2);
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('FTP Feature'); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?php echo _('FTP Auto Indexing'); ?></h1>
        <?php
        if (!isset($_GET['start'])) {
            echo '<a href="episodes_ftp_feature.php?start=1" class="btn btn-success">' . _('Begin') . '<a>';
        }
        if (isset($success)) {
            echo '<p>' . htmlspecialchars($success) . '</p>';
        }
        ?>
    </div>
</body>

</html>