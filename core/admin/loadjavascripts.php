<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

//$loadjavascripts = '<script language="JavaScript" type="text/javascript" src="components/limitfield/limitfield.js"></script>';

//$loadjavascripts .= '<script language="JavaScript" type="text/javascript" src="components/countwords/countwords.js"></script>';

//$loadjavascripts .= '<script language="JavaScript" type="text/javascript" src="components/maxselect/maxselect.js"></script>';

//$loadjavascripts .= '<script language="JavaScript" type="text/javascript" src="components/prototype/prototype.js"></script>';

//$loadjavascripts .= '<script language="JavaScript" type="text/javascript" src="components/scriptaculous/scriptaculous.js?load=effects"></script>';

$loadjavascripts = '<script language="JavaScript" type="text/javascript" src="components/js/admin.js"></script>

<script language="JavaScript" type="text/javascript" src="components/js/jquery.js"></script>
';



### INSERT EDITOR WYSIWYG in specified pages
// Note: the - entity_encoding : "raw" - into tinyMCE.init solves issues with html entities (conversion of letter with accents, and other characters) in the "long description" of episodes
if (isset($_GET["do"])) { 





	if ($_GET["do"]=="upload" OR $_GET["do"]=="freebox" OR $_GET["do"]=="edit") {

	//FADING IN CONFIRMATION (e.g. are you sure u want to delete?)
	$loadjavascripts .='
	<script type="text/javascript">
	$(document).ready(
    function() {
        $("#confirmdelete").click(function() {
            $("#confirmation").fadeToggle();
        });
    });
</script>


	';
	
	//EDITOR WYSIWYG
		$loadjavascripts .='
		<script language="javascript" type="text/javascript" src="components/tiny_mce/tiny_mce.js"></script>

			<script language="javascript" type="text/javascript">

		tinyMCE.init({

			entity_encoding : "raw",

				theme : "advanced",

				mode: "exact",

				elements : "long_description",

				language : "en",
				
				theme_advanced_toolbar_location : "top",
				theme_advanced_buttons1 : "bold,italic,underline,separator,"
				+ "justifyleft,justifycenter,justifyright,separator,"
				+ "forecolor,bullist,numlist,link,unlink,separator,"
				+ "image,separator,undo,redo,code",
				height:"200px",
				width:"400px"
				});
			</script>
		';

				
		}
			
	
			

	}




?>