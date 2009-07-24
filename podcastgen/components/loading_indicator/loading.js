<?php

if(!isset($PG_mainbody)) { // define variable if not set
	$PG_mainbody = ""; // empty value just to define the variable
}

// put css and javascript into a variable to be incorporated in page body
$PG_mainbody .= '<style type="text/css">

#status_notification{
	position: absolute; right: 0px; top: 0px; 
	display: none; visibility: hidden; 
	background-color: #cc3300;
	font-family: "Trebuchet MS", verdana, arial, sans-serif;
	font-size:14px; 
	color: white; 
	margin: 0px; padding: 3px
	}

	span > div#status_notification {
		position: fixed;
	}
	</style>

		<script language="javascript">
	function getScrollTop() {
		if ( document.documentElement.scrollTop )
			return document.documentElement.scrollTop;
		return document.body.scrollTop;
	}

	function showNotify( str ) {
		var elem = document.getElementById(\'status_notification\');
		elem.style.display = \'block\';
		elem.style.visibility = \'visible\';

		if ( elem.currentStyle && elem.currentStyle.position == \'absolute\' ) {
			elem.style.top = getScrollTop();
		}

		elem.innerHTML = str;
	}

	function hideNotify() {
		var elem = document.getElementById(\'status_notification\');
		elem.style.display = \'none\';
		elem.style.visibility = \'hidden\';
	}

	window.onscroll = function () {
		var elem = document.getElementById(\'status_notification\');
		if ( !elem.currentStyle || elem.currentStyle.position != \'absolute\' ) {
			window.onscroll = null;
		} else {
			window.onscroll = function () { elem.style.top = getScrollTop(); };
			document.getElementById(\'status_notification\').style.top = getScrollTop();
		}
	}
	</script>

		<span><div id="status_notification"></div></span>
		';

	?>