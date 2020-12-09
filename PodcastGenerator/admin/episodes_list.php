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

//$episodes = getEpisodes(null, $config, true);

// reads all episodes into an array
// and yes, I know this does basically what the other getEpisodes function does...
function getEpisodeArray(){
	$episodeFiles = glob('../media/*.xml');
	$episodes = [];
	foreach($episodeFiles as $episodeFile){
		$episodeXMLString = file_get_contents($episodeFile);
		$episode = simplexml_load_string($episodeXMLString, 'SimpleXMLElement', LIBXML_NOCDATA);
		// now get date and timestamp from date
		$dateString = substr(basename($episodeFile),0,10);
		$episode->episode->dateString = $dateString;
		$episode->episode->timestamp = strtotime($dateString);	
		$episode->episode->fileName = $episodeFile;		
		$episodes[] = $episode->episode;		
	}
	return $episodes;
}


$episodes = getEpisodeArray();
//echo "<pre>";
//print_r($episodes);
//die("</pre>");
// sorts into descending order, as future and recent episodes are most likely to be edited
usort($episodes, function($a, $b) {
	return $a['timestamp'] <=> $b['timestamp'];
});
$episodes = array_reverse($episodes);



?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Theme Buttons'); ?></title>
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
        <h1><?php echo _('Episodes'); ?></h1>
		<p><?php echo _("Click on the title of the podcast you want to edit/delete."); ?></p>
        <p><?php echo _("Dates in <span style='color:blue;'>blue</span> are in the future. Dates in <span style='color:green;'>green</span> have been posted.")?></p>
		<?php
        if (isset($error)) {
            echo '<strong><p style="color: red;">' . $error . '</p></strong>';
        }
        ?>
		<ul>
        <?php
			$now = time();
			foreach($episodes as $episode){
				echo "<li><span style='color:";
				echo ($episode->timestamp > $now) ? "blue":"green";
				echo ";'>". $episode->dateString."</span> - <a href='./episodes_edit.php?name=".$episode->fileName."'>".$episode->titlePG . "</a></li>\n";
			}
		?>
		</ul>
    </div>
</body>

</html>