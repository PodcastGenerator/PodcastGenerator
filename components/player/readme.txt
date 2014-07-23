NOTE from Podcast Generator developer:
This is a super old player, it also has some "potential" security issues
It will be replaced by native HTML5 players, whose support is growing more and more.
If you want to completely delete the flash player, you can open player.php in this folder, erase all its content and save it empty.

------------------------------------------------------ 

Plugin Name: Audio player
Plugin URI: http://www.1pixelout.net/code/audio-player-wordpress-plugin/
Description: Highly configurable single track mp3 player.
Version: 1.2.3
Author: Martin Laine
Author URI: http://www.1pixelout.net

Change log:

	1.2.3 (01 March 2006)
	
		* Added page background and disable transparency option

	1.2.2 (14 February 2006)
	
		* Fixed a bug for the "replace all mp3 links" option (now case-insensitive)

	1.2.1 (07 February 2006)
	
		* Fixed a bug for the "replace all mp3 links" option (now supports extra attributes in a tags)

	1.2 (07 February 2006)
	
		* Implemented post/pre append clip feature
		* Amended player to allow for clip sequence playback
		* Improved plugn php code syntax
		* Minor improvements to slider bar appearance
		* Added configurable behaviour options: [audio] syntax, enclosure integration and mp3 link replace
		* Added configurable RSS alternate content option: insert download link, nothing or custom content
		* Player now closes automatically if you open another one on the same page
		* Fixed a problem with colour options in Flash 6
		* Added player preview to colour scheme configurator
		* Check for updates and automatic upgrade feature

	1.0.1 (31 December 2005)

		* All text fields now use device fonts (much crisper text rendering, support for many more characters and even smaller player file size)
		* General clean up and commenting of source code

	1.0 (26 December 2005)
		
		* Player now based on the emff player (http://www.marcreichelt.de/)
		* New thinner design (suggested by Don Bledsoe - http://screenwriterradio.com/)
		* More colour options
		* New slider function to move around the track
		* Simple scrolling ID3 tag support for title and artist (thanks to Ari - http://www.adrstudios.com/)
		* Time display now includes hours for very long tracks
		* Support for autostart and loop (suggested by gotjosh - http://gotblogua.gotjosh.net/)
		* Support for custom colours per player instance
		* Fixed an issue with rss feeds. Post content in rss feeds now only shows a link to the file rather than the player (thanks to Blair Kitchen - http://96rpm.the-blair.com/)
		* Better handling of buffering and file not found errors 

	0.7.1 beta (29 October 2005)

		* MP3 files are no longer pre-loaded (saves on bandwidth if you have multiple players on one page)

	0.7 beta (24 October 2005)

		* Added colour customisation.

	0.6 beta (23 October 2005)

		* Fixed bug in flash player: progress bar was not updating properly.

	0.5 beta (19 October 2005)

		* Moved player.swf to plugins folder
		* Default location of audio files is now top-level /audio folder
		* Better handling of paths and URIs
		* Added support for linking to external files

	0.2 beta (19 October 2005)

		* Bug fix: the paths to the flash player and the mp3 files didn?t respect the web path option. This caused problems for blogs that don?t live in the root of the domain (eg www.mydomain.com/blog/)

License:

    Copyright 2005-2006  Martin Laine  (email : martin@1pixelout.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
    