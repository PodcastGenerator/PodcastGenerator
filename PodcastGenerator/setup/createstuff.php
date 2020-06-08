<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
// Create other files, do this silent
function createstuff() {
    if(!file_exists("../freebox-content.txt")) {
        $freebox_text = "<p>This is a Freebox. You can put any valid HTML in here. Or disable this feature if you don't need it.</p>";
        file_put_contents("../freebox-content.txt", $freebox_text);
    }
    if(!file_exists("../buttons.xml")) {
        $content = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
    <button>
        <name>RSS</name>
        <href>feed.xml</href>
        <class>btn btn-warning</class>
    </button>
    <button>
        <name>iTunes</name>
        <href>feed.xml</href>
        <class>btn btn-primary</class>
        <protocol>itpc</protocol>
    </button>
</PodcastGenerator>';
        file_put_contents("../buttons.xml", $content);
    }
    if(!file_exists("../categories.xml")) {
        $catfile = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
    <category>
    <id>uncategorized</id>
    <description>Uncategorized</description>
    </category>
</PodcastGenerator>';
        if(!file_put_contents("../categories.xml", $catfile)) {
            return false;
        }
    }
    return true;
}
?>