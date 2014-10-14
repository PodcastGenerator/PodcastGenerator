<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################


$loadjavascripts = '<script language="JavaScript" type="text/javascript" src="components/js/admin.js"></script>

<script language="JavaScript" type="text/javascript" src="components/js/jquery.js"></script>
';



if (isset($_GET["do"])) { 


	if ($_GET["do"]=="upload" OR $_GET["do"]=="freebox" OR $_GET["do"]=="edit" OR $_GET["do"]=="categories") {

	
	//DELETE FADING IN CONFIRMATION (e.g. are you sure u want to delete?)	
$loadjavascripts .='
	<script type="text/javascript">
$(document).ready(
    function() {
        $("#confirmdelete").click(function() {
            $("#confirmation").fadeToggle();
        });
    });
</script>';	


// DELETE categories confirmation
	$loadjavascripts .='
	<script type="text/javascript">
$(document).ready(
    function() {
        $(\'[id^="confirmdelete-"]\').click(function() {
            $(\'#confirmation-\'+$(this).attr(\'id\').replace(\'confirmdelete-\',\'\')).fadeToggle();
        });
});
</script>';




### INSERT EDITOR WYSIWYG in specified pages
// TinyMCE is loaded from an external (offical) URL. If no connection available then a simple textarea will be shown
// Note: the - entity_encoding : "raw" - into tinyMCE.init solves issues with html entities (conversion of letter with accents, and other characters) in the "long description" of episodes
// extended_valid_elements and custom_elements allows to insert new html elements (e.g. Google custom search engine)

//NB in future enable "code" from toolbar below just for freebox
		$loadjavascripts .='
		<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
		<script>
		tinymce.init({
		
			selector:"#long_description",
			extended_valid_elements : "gcse:search",
			custom_elements : "gcse:search,~gcse:search",
			entity_encoding : "raw",
			width: 400,
			height: 200,
			menubar: false,
			statusbar: false,
			plugins: [
					 "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
					 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
					 "save table contextmenu directionality emoticons template paste textcolor"
			],
		   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | forecolor | code", 

		});
		</script>
		';

		}
	
	}



?>