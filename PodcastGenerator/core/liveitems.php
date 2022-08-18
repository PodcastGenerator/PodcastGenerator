<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

require_once(__DIR__ . '/../vendor/autoload.php');

use PodcastGenerator\Configuration;
use Lootils\Uuid\Uuid;

define('LIVEITEM_STATUS_PENDING', 'pending');
define('LIVEITEM_STATUS_LIVE', 'live');
define('LIVEITEM_STATUS_ENDED', 'ended');

/**
 * Creates a standardized filename for the provided original filename.
 *
 * Because this function takes a directory name and file extension, it is useful
 * for both episode files and cover art files.
 *
 * @param string   $directory The path of the directory where the file will be
 *                            saved.
 * @param DateTime $datetime  The original start date/time of the live stream.
 * @param string   $title     The original title of the live item to be saved.
 * @param string   $ext       File extension to use (defaults to 'xml').
 * @return string             The file name and path to use for saving the file.
 */
function makeLiveItemFilename(string $directory, DateTime $date, string $title, string $ext = '.xml'): string
{
    $filename = strtolower(str_replace(' ', '_', $title));

    if (empty($ext)) {
        $ext = '.xml';
    } elseif (substr($ext, 0, 1) != '.') {
        $ext = '.' . $ext;
    }

    $fmtdate = $date->format('Y-m-d_Hi');

    $targetfile = $directory . '_live_' . $fmtdate . '_' . $filename . $ext;

    if (file_exists($targetfile)) {
        $appendix = 1;
        while (file_exists($targetfile)) {
            $targetfile = $directory . $fmtdate . '_' . $filename . '_' . $appendix . $ext;
            $appendix++;
        }
    }
    return $targetfile;
}

/**
 * Writes out the details of a live item to a file.
 *
 * @param array  $liveItem  The live item to save.
 * @param string $filePath  The path of the file in which to save the live item.
 * @return boolean          Whether or not the save was successful.
 */
function saveLiveItem($liveItem, $filePath)
{
    $longDesc = !empty($liveItem['longDesc']) ? $liveItem['longDesc'] : $liveItem['shortDesc'];
    $xml = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
    <liveItem>
        <guid>' . htmlspecialchars($liveItem['guid']) . '</guid>
        <title>' . htmlspecialchars($liveItem['title'], ENT_NOQUOTES) . '</title>
        <status>' . $liveItem['status'] . '</status>
        <shortDesc><![CDATA[' . $_POST['shortDesc'] . ']]></shortDesc>
        <longDesc><![CDATA[' . $longDesc . ']]></longDesc>
        <startTime>' . $liveItem['startTime']->format(DateTime::ISO8601) . '</startTime>
        <endTime>' . $liveItem['endTime']->format(DateTime::ISO8601) . '</endTime>
        <image path="' . htmlspecialchars($liveItem['image']['path']) . '">' . htmlspecialchars($liveItem['image']['url']) . '</image>
        <author>
            <name>' . htmlspecialchars($liveItem['author']['name']) . '</name>
            <email>' . htmlspecialchars($liveItem['author']['email']) . '</email>
        </author>
        <streamInfo>
            <url>' . htmlspecialchars($liveItem['streamInfo']['url']) . '</url>
            <mimeType>' . $liveItem['streamInfo']['mimeType'] . '</mimeType>
        </streamInfo>
        <customTags><![CDATA[' . $liveItem['customTags'] . ']]></customTags>
';

    if (!empty($liveItem['previousImages'])) {
        $xml .= '        <previousImages>' . "\n";
        foreach ($liveItem['previousImages'] as $previousImage) {
            if (empty($previousImage)) { continue; }
            $xml .= '            <image path="' . htmlspecialchars($previousImage) . '"/>' . "\n";
        }
        $xml .= '        </previousImages>' . "\n";
    }

    $xml .= '    </liveItem>
</PodcastGenerator>';

    if (!file_put_contents($filePath, $xml)) {
        return false;
    }

    // Get datetime
    $datetime = strtotime($liveItem['startTime']->format(DateTime::ISO8601)); // TODO: must be a method for this!
    // Set file date to this date
    return touch($filePath, $datetime);
}

/**
 * Creates a live item array from an XML object.
 * @internal
 *
 * @param SimpleXMLElement $item    The XML object holding the details of the
 *                                  live item.
 * @param string           $file    The full path of the file containing the XML
 *                                  for the live item.
 * @param Configuration    $config  The configuration object for the website.
 * @return array                    The live item details as a keyed array.
 */
function array_liveitem(SimpleXMLElement $item, string $file, Configuration $config)
{
    $filemtime = filemtime($config['absoluteurl'] . $config['upload_dir'] . $file);
    $liveItem = [
        'guid' => new Uuid('urn:uuid:' . $item->guid),
        'title' => (string) $item->title,
        'status' => (string) $item->status,
        'startTime' => new DateTime((string) $item->startTime),
        'endTime' => new DateTime((string) $item->endTime),
        'shortDesc' => (string) $item->shortDesc,
        'longDesc' => (string) $item->longDesc,
        'image' => [
            'path' => (string) $item->image->attributes()->path,
            'url' => (string) $item->image
        ],
        'author' => [
            'name' => (string) $item->author->name,
            'email' => (string) $item->author->email
        ],
        'streamInfo' => [
            'url' => (string) $item->streamInfo->url,
            'mimeType' => (string) $item->streamInfo->mimeType
        ],
        'customTags' => (string) $item->customTags,
        'filename' => $file,
        'filemtime' => $filemtime,
        'moddate' => date('Y-m-d', $filemtime),
        'previousImages' => []
    ];

    if (isset($item->previousImages)) {
        foreach ($item->previousImages->children() as $prevImage) {
            $liveItem['previousImages'][] = $prevImage->attributes()['path'];
        }
    }

    return $liveItem;
}

const LIVE_ITEM_FILE_MATCHER = '/^_live_\d{4}(-\d{2}){2}_\d{4}(_.+)?\.xml$/';

/**
 * Gets file details for all live items on the website.
 * @internal
 *
 * @param Configuration $config  The configuration object for the website.
 * @return array                 A sorted array of live item files.
 */
function getLiveItemFiles(Configuration $config): array
{
    $uploadDir = $config['absoluteurl'] . $config['upload_dir'];

    // Load live item file data
    $files = array();
    if ($handle = opendir($uploadDir)) {
        while (false !== ($entry = readdir($handle))) {
            if (!preg_match(LIVE_ITEM_FILE_MATCHER, $entry)) {
                continue;
            }

            $filePath = $uploadDir . $entry;
            array_push($files, [
                'filename' => $entry,
                'path' => $filePath,
                'lastModified' => filemtime($filePath),
                'data' => simplexml_load_file($filePath, null, LIBXML_NOCDATA)
            ]);
        }
    }

    usort($files, function ($a, $b) {
        return $a['lastModified'] - $b['lastModified'];
    });

    return $files;
}

/**
 * Gets all live items saved on the website.
 *
 * @param Configuration $config  The configuration object for the website.
 * @return array                 A sorted array of live items.
 */
function getLiveItems(Configuration $config): array
{
    $liveItems = getLiveItemFiles($config);

    return array_map(
        function ($ep) use ($config) {
            return array_liveitem($ep['data']->liveItem, $ep['filename'], $config);
        },
        $liveItems
    );
}

function loadLiveItem(string $liveItemFile, Configuration $config) {
    $xmlData = simplexml_load_file($liveItemFile);
    return array_liveitem($xmlData->liveItem, pathinfo($liveItemFile, PATHINFO_BASENAME), $config);
}

/**
 * Deletes a live item and its related cover image, if any.
 *
 * This function does not regenerate the XML feed or ping third party services.
 * Whoever calls this function should ensure that those tasks are taken care of.
 *
 * On failure, some files related to a podcast episode may still be left around
 * the media and images directories.
 *
 * @param string        $liveItemFile  The full path of the live item XML file.
 * @param Configuration $config        The PG configuration object.
 * @return boolean                     true on success, false on failure.
 */
function deleteLiveItem(string $liveItemFile, Configuration $config): bool {
    $imagesDir = $config['absoluteurl'] . $config['img_dir'];

    $filesToDelete = [$liveItemFile];

    $xmlData = simplexml_load_file($liveItemFile);
    $coverImg = (string) $xmlData->liveItem->image->attributes()['path'];
    if (!empty($coverImg)) {
        $filesToDelete[] = $coverImg;
    }

    if (isset($xmlData->liveItem->previousImages)) {
        foreach ($xmlData->liveItem->previousImages->children() as $prevImage) {
            $filesToDelete[] = $prevImage->attributes()['path'];
        }
    }

    // Go through the list of files and delete each one
    foreach ($filesToDelete as $file) {
        if (!unlink($file)) {
            return false;
        }
    }
    return true;
}
