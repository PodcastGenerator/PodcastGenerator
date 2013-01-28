<?php
//ATTACH TO EACH EPISODE (social networks)

//Define the full URL of a single episode
	$fullURL = $url.'?p=episode&amp;name='.$file_multimediale[0].'.'.$podcast_filetype; //full URL of the episode


	
	
	
	//SOCIAL NETWORKS INTEGRATION
if (in_array(TRUE,$enablesocialnetworks)) { //IF at least one value is true
	$PG_mainbody .= displaySocialNetworkButtons($fullURL,$text_title,$enablesocialnetworks[0],$enablesocialnetworks[1],$enablesocialnetworks[2]); //0 is FB, 1 twitter, 2 G+
	}
	
	
?>