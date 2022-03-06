<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

/**
 * Get custom feed tags from configuration XML.
 *
 * @param  string $path  Path to the directory containing customtags.xml
 * @return string        A string containing the custom XML tags for the RSS feed
 */
function getCustomFeedTags($path = '../')
{
    $xml = simplexml_load_file($path . 'customtags.xml');
    return $xml ? $xml->customfeedtags : '';
}

/**
 * Save custom feed tags to configuration XML.
 *
 * @param  string $tags  A string containing the custom XML tags for the RSS feed
 * @param  string $path  Path to the directory containing customtags.xml
 * @return boolean       Whether or not the save completed successfully
 */
function saveCustomFeedTags($tags, $path = '../')
{
    $xml = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
    <customfeedtags><![CDATA[' . $tags . ']]></customfeedtags>
</PodcastGenerator>';
    return file_put_contents($path . 'customtags.xml', $xml);
}