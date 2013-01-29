<?php
//ATTACH TO EACH EPISODE (social networks)

//Define the full URL of a single episode
	$fullURL = $url.'?p=episode&amp;name='.$file_multimediale[0].'.'.$podcast_filetype; //full URL of the episode

	
// CUSTOMIZED CODE TO EMBED
// IF a file called embed-code.txt is manually created in the root of Podcast Generator. The content of that file will be displayed along with each episode (useful to add customized HTML code to each episode)
	if(file_exists("$absoluteurl"."embed-code.txt")){
		$embeddedcodetoshow = file_get_contents("$absoluteurl"."embed-code.txt");
		$PG_mainbody .= $embeddedcodetoshow; }
	
	
	//SOCIAL NETWORKS INTEGRATION
if (in_array(TRUE,$enablesocialnetworks)) { //IF at least one value is true
	$PG_mainbody .= displaySocialNetworkButtons($fullURL,$text_title,$enablesocialnetworks[0],$enablesocialnetworks[1],$enablesocialnetworks[2]); //0 is FB, 1 twitter, 2 G+
	}
	
	
?>