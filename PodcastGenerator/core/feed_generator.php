<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

function write_itunes_category(\XMLWriter $writer, $categoryName)
{
    $cat_seg = explode(':', $categoryName, 2);

    $writer->startElementNs('itunes', 'category', null);
    $writer->writeAttribute('text', $cat_seg[0]);

    if (count($cat_seg) > 1) {
        $writer->startElementNs('itunes', 'category', null);
        $writer->writeAttribute('text', $cat_seg[1]);
        $writer->endElement();
    }

    $writer->endElement();
}

function write_episode_item(\XMLWriter $writer, $file, $feedContext)
{
    $config = $feedContext->config;

    $link = str_replace('?', '', $config['link']);
    $link = str_replace('=', '', $link);
    $link = str_replace('$url', '', $link);

    // encode special characters in file name
    $encodedFilename = str_replace('+', '%20', urlencode($file['filename']));
    $enclosureUrl = $feedContext->uploadUrl . $encodedFilename;

    // Skip files with no read permission
    $mimetype = getmime($feedContext->uploadDir . $file['filename']);
    if (!$mimetype) {
        $mimetype = null;
    }

    $author = null;
    if (!empty($file['data']->episode->authorPG->emailPG)) {
        $author = $file['data']->episode->authorPG->emailPG;
        if (!empty($file['data']->episode->authorPG->namePG)) {
            $author .= ' (' . $file['data']->episode->authorPG->namePG . ')';
        }
    } else {
        $author = $config['author_email'] . ' (' . $config['author_name'] . ')';
    }

    // Generate GUID if a pregenerated GUID is missing for the episode
    $guid = isset($file['data']->episode->guid)
        ? $file['data']->episode->guid
        : $config['url'] . "?" . $link . "=" . $file['filename'];

        $hasLongDesc = isset($file['data']->episode->longdescPG) && trim($file['data']->episode->longdescPG) != "";

    // Check if this episode has a cover art
    $basename = pathinfo($file['filename'], PATHINFO_FILENAME);
    $has_cover = false;
    if (!empty($file['data']->episode->imgPG)) {
        $has_cover = $file['data']->episode->imgPG;
    } elseif (
        file_exists($feedContext->imagesDir . $basename . '.jpg')
        || file_exists($feedContext->imagesDir . $basename . '.png')
    ) {
        $ext = file_exists($feedContext->imagesDir . $basename . '.png') ? '.png' : '.jpg';
        $has_cover = $feedContext->imagesUrl . $basename . $ext;
    }

    $writer->startElement('item');
    $writer->writeElement('title', $file['data']->episode->titlePG);

    if (!empty($file['data']->episode->episodeNumPG)) {
        $writer->writeElementNs('itunes', 'episode', null, $file['data']->episode->episodeNumPG);
        $writer->writeElementNs('podcast', 'episode', null, $file['data']->episode->episodeNumPG);
    }
    if (!empty($file['data']->episode->seasonNumPG)) {
        $writer->writeElementNs('itunes', 'season', null, $file['data']->episode->seasonNumPG);
        $writer->writeElementNs('podcast', 'season', null, $file['data']->episode->seasonNumPG);
    }

    $writer->startElementNs('itunes', 'subtitle', null);
    $writer->writeCdata($file['data']->episode->shortdescPG);
    $writer->fullEndElement();

    $writer->startElement('description');
    $writer->writeCdata($hasLongDesc ? $file['data']->episode->longdescPG : $file['data']->episode->shortdescPG);
    $writer->fullEndElement();

    $writer->startElementNs('itunes', 'summary', null);
    $writer->writeCdata($hasLongDesc ? $file['data']->episode->longdescPG : $file['data']->episode->shortdescPG);
    $writer->fullEndElement();

    $writer->writeElement('link', $config['url'] . '?' . $link . '=' . $encodedFilename);

    $writer->startElement('enclosure');
    $writer->writeAttribute('url', $enclosureUrl);
    $writer->writeAttribute('length', (string) filesize($feedContext->uploadDir . $file['filename']));
    $writer->writeAttribute('type', $mimetype);
    $writer->fullEndElement();

    $writer->writeElement('guid', $guid);

    $writer->writeElementNs('itunes', 'duration', null, $file['data']->episode->fileInfoPG->duration);

    $writer->writeElement('author', $author);
    if (!empty($file['data']->episode->authorPG->namePG)) {
        $writer->writeElementNs('itunes', 'author', null, $file['data']->episode->authorPG->namePG);
    } else {
        $writer->writeElementNs('itunes', 'author', null, $config['author_name']);
    }

    if ($file['data']->episode->keywordsPG != "") {
        $writer->writeElementNs('itunes', 'keywords', null, $file['data']->episode->keywordsPG);
    }

    $writer->writeElementNs('itunes', 'explicit', null, $file['data']->episode->explicitPG);
    if (((string) $file['data']->episode->itunesBlock) == 'yes') {
        $writer->writeElementNs('itunes', 'block', null, 'Yes');
    }

    $episodeType = (string) $file['data']->episode->episodeType;
    if (!empty($episodeType)) {
        $writer->writeElementNs('itunes', 'episodeType', null, $episodeType);
    }

    // If image is set
    if ($has_cover) {
        $writer->startElementNs('itunes', 'image', null);
        $writer->writeAttribute('href', $has_cover);
        $writer->endElement();

        $writer->startElementNs('googleplay', 'image', null);
        $writer->writeAttribute('href', $has_cover);
        $writer->endElement();
    }

    $writer->writeElement('pubDate', date('r', $file['lastModified']));

    if (!empty($file['data']->episode->customTagsPG)) {
        $writer->writeRaw($file['data']->episode->customTagsPG);
    }

    $writer->endElement();
}

function write_live_item(\XMLWriter $writer, $liveItem, $feedContext)
{
    $config = $feedContext->config;

    $link = str_replace(['?', '=', '$url'], '', $config['link']);

    $encodedFileName = substr($liveItem['filename'], 6, strlen($liveItem['filename']) - 10);
    $encodedFilename = str_replace('+', '%20', urlencode($encodedFileName));

    $streamUrl = (string) $liveItem['data']->liveItem->streamInfo->url;
    if (empty($streamUrl)) {
        $streamUrl = $feedContext->config['liveitems_default_stream'];
        $streamMimeType = $feedContext->config['liveitems_default_mimetype'];
    } else {
        $streamMimeType = (string) $liveItem['data']->liveItem->streamInfo->mimeType;
    }

    if (!empty($liveItem['data']->liveItem->author->email)) {
        $author = $liveItem['data']->liveItem->author->email;
        if (!empty($liveItem['data']->liveItem->author->name)) {
            $author .= ' (' . $liveItem['data']->liveItem->author->name . ')';
        }
    } else {
        $author = $config['author_email'] . ' (' . $config['author_name'] . ')';
    }

    $hasLongDesc = isset($liveItem['data']->liveItem->longDesc) && trim($liveItem['data']->liveItem->longDesc) != '';

    // Check if this live item has cover art
    $cover = false;
    if (!empty($liveItem['data']->liveItem->image)) {
        $cover = $liveItem['data']->liveItem->image;
    }

    // start writing out the live item

    $writer->startElementNs('podcast', 'liveItem', null);
    $writer->writeAttribute('status', $liveItem['data']->liveItem->status);
    $writer->writeAttribute('start', $liveItem['data']->liveItem->startTime);
    $writer->writeAttribute('end', $liveItem['data']->liveItem->endTime);

    $writer->writeElement('title', $liveItem['data']->liveItem->title);
    $writer->writeElement('guid', $liveItem['data']->liveItem->guid);

    $writer->startElementNs('itunes', 'subtitle', null);
    $writer->writeCdata($liveItem['data']->liveItem->shortDesc);
    $writer->fullEndElement();

    $writer->startElement('description');
    $writer->writeCdata($hasLongDesc ? $liveItem['data']->liveItem->longDesc : $liveItem['data']->liveItem->shortDesc);
    $writer->fullEndElement();

    $writer->startElementNs('itunes', 'summary', null);
    $writer->writeCdata($hasLongDesc ? $liveItem['data']->liveItem->longDesc : $liveItem['data']->liveItem->shortDesc);
    $writer->fullEndElement();

    $writer->writeElement('link', $config['url'] . 'live.php?' . $link . '=' . $encodedFilename);

    $writer->startElement('enclosure');
    $writer->writeAttribute('url', $streamUrl);
    $writer->writeAttribute('type', $streamMimeType);
    $writer->writeAttribute('length', '33'); // dummy value as attr is required
    $writer->endElement();

    // If image is set
    if ($cover) {
        $writer->startElementNs('itunes', 'image', null);
        $writer->writeAttribute('href', $cover);
        $writer->endElement();

        $writer->startElementNs('googleplay', 'image', null);
        $writer->writeAttribute('href', $cover);
        $writer->endElement();
    }

    if (!empty($liveItem['data']->liveItem->customTags)) {
        $writer->writeRaw($liveItem['data']->liveItem->customTags);
    }

    $writer->endElement();
}

function generateRssFeed($_config, $category = null)
{
    $genTime = new DateTimeImmutable();

    // We use the media directory a lot, and possibly also the images directory
    // Stick them in variables instead of concatenating all the time
    $uploadDir = $_config['absoluteurl'] . $_config['upload_dir'];
    $uploadUrl = $_config['url'] . $_config['upload_dir'];

    $imagesDir = $_config['absoluteurl'] . $_config['img_dir'];
    $imagesUrl = $_config['url'] . $_config['img_dir'];

    $feedContext = (object) [
        'uploadDir' => $uploadDir,
        'uploadUrl' => $uploadUrl,
        'imagesDir' => $imagesDir,
        'imagesUrl' => $imagesUrl,
        'config' => $_config
    ];

    $podcastCoverUrl = $imagesUrl . $_config['podcast_cover'];

    $categoryDescription = null;
    if (!empty($category)) {
        $feedUrl = $_config['url'] . 'feed.php?cat=' . $category;
        // Get category description
        $cats = simplexml_load_file($_config['absoluteurl'] . 'categories.xml');
        foreach ($cats as $item) {
            if ($category == $item->id) {
                $categoryDescription = $item->description;
                break;
            }
        }
    } else {
        $feedUrl = $_config['url'] . $_config['feed_dir'] . 'feed.xml';
    }

    $feedTitle = $_config['podcast_title'];
    if (!empty($categoryDescription)) {
        $feedTitle .= " - " . $categoryDescription;
    }

    $writer = new \XMLWriter();
    $writer->openMemory();
    $writer->setIndent(true);
    $writer->setIndentString("\t");

    // Set the feed header with relevant podcast informations

    $writer->startDocument('1.0', $_config['feed_encoding']);
    $writer->writeComment(' generator="Podcast Generator ' . PG_VERSION . '" ');

    $writer->startElement('rss');
    $writer->writeAttributeNs('xmlns', 'itunes', null, 'http://www.itunes.com/dtds/podcast-1.0.dtd');
    $writer->writeAttributeNs('xmlns', 'googleplay', null, 'http://www.google.com/schemas/play-podcasts/1.0');
    $writer->writeAttributeNs('xml', 'lang', null, $_config['feed_language']);
    $writer->writeAttribute('version', '2.0');
    $writer->writeAttributeNs('xmlns', 'atom', null, 'http://www.w3.org/2005/Atom');
    $writer->writeAttributeNs('xmlns', 'podcast', null, 'https://podcastindex.org/namespace/1.0');

    $writer->startElement('channel');

    $writer->writeElement('title', $feedTitle);

    $writer->writeElement('link', $_config['url']);

    $writer->startElementNs('atom', 'link', null);
    $writer->writeAttribute('href', $feedUrl);
    $writer->writeAttribute('rel', 'self');
    $writer->writeAttribute('type', 'application/rss+xml');
    $writer->endElement();

    if (!empty($_config['podcast_guid'])) {
        $writer->writeElementNs('podcast', 'guid', null, $_config['podcast_guid']);
    }

    $writer->writeElement('description', $_config['podcast_description']);

    $writer->writeElement('generator', 'Podcast Generator ' . PG_VERSION . ' - https://www.podcastgenerator.net/');
    $writer->writeElement('lastBuildDate', $genTime->format('r'));
    $writer->writeElement('language', $_config['feed_language']);

    $writer->writeElement('copyright', $_config['copyright']);
    $writer->writeElement('managingEditor', $_config['author_email']);
    $writer->writeElement('webMaster', $_config['webmaster']);

    $writer->startElementNs('itunes', 'image', null);
    $writer->writeAttribute('href', $podcastCoverUrl);
    $writer->endElement();

    $writer->startElement('image');
    $writer->writeElement('url', $podcastCoverUrl);
    $writer->writeElement('title', $_config['podcast_title']);
    $writer->writeElement('link', $_config['url']);
    $writer->endElement();

    $writer->writeElementNs('itunes', 'summary', null, $_config['podcast_description']);
    $writer->writeElementNs('itunes', 'subtitle', null, $_config['podcast_subtitle']);

    $writer->writeElementNs('itunes', 'author', null, $_config['author_name']);

    $writer->startElementNs('itunes', 'owner', null);
    $writer->writeElementNs('itunes', 'name', null, $_config['author_name']);
    $writer->writeElementNs('itunes', 'email', null, $_config['author_email']);
    $writer->endElement();

    $writer->writeElementNs('itunes', 'explicit', null, $_config['explicit_podcast']);

    write_itunes_category($writer, $_config['itunes_category[0]']);
    if ($_config['itunes_category[1]'] != '' || $_config['itunes_category[1]'] == 'null') {
        write_itunes_category($writer, $_config['itunes_category[1]']);
    }
    if ($_config['itunes_category[2]'] != '' || $_config['itunes_category[1]'] == 'null') {
        write_itunes_category($writer, $_config['itunes_category[2]']);
    }

    if ($_config['websub_server'] != '') {
        $writer->startElementNs('atom', 'link', null);
        $writer->writeAttribute('href', $_config['websub_server']);
        $writer->writeAttribute('rel', 'hub');
        $writer->endElement();
    }

    if ($_config['feed_locked'] != '') {
        $writer->startElementNs('podcast', 'locked', null);
        $writer->writeAttribute('owner', $_config['author_email']);
        $writer->text($_config['feed_locked']);
        $writer->fullEndElement();
    }

    $custom_tags = getCustomFeedTags($_config['absoluteurl']);
    if ($custom_tags != '') {
        $writer->writeRaw($custom_tags);
    }

    // Get ordered episodes
    $files = getEpisodeFiles($_config);

    if ($category != null) {
        $files = array_filter(
            $files,
            function ($ep) use ($category) {
                $categories = $ep['data']->episode->categoriesPG;
                return $categories->category1PG == $category
                    || $categories->category2PG == $category
                    || $categories->category3PG == $category;
            }
        );
    } elseif ($_config['liveitems_enabled'] == 'yes') {
        // we only include live items on the regular feed
        $liveItems = getLiveItemFiles($_config);

        $endedLiveItems = [];
        $liveLiveItems = [];
        $pendingLiveItems = [];

        foreach ($liveItems as $liveItem) {
            switch ($liveItem['data']->liveItem->status) {
                case LIVEITEM_STATUS_ENDED:
                    $endedLiveItems[] = $liveItem;
                    break;
                case LIVEITEM_STATUS_LIVE:
                    $liveLiveItems[] = $liveItem;
                    break;
                case LIVEITEM_STATUS_PENDING:
                    $pendingLiveItems[] = $liveItem;
                    break;
            }
        }

        // for ended live items, we need the items from the far end of the array
        // where the endTime >= now - liveitems_earliest_ended
        $maxEnded = (int) $_config['liveitems_max_ended'];
        $endTimeSeconds = (int) ($_config['liveitems_earliest_ended'] * 86400);
        $endTimeInterval = DateInterval::createFromDateString("$endTimeSeconds seconds");
        $endTime = $genTime->sub($endTimeInterval);
        $endedLiveItems = array_filter(
            array_slice($endedLiveItems, -$maxEnded, $maxEnded),
            function ($liveItem) use ($endTime) {
                $itemTime = new DateTime((string) $liveItem['data']->endTime);
                return $itemTime >= $endTime;
            }
        );

        // for pending live items, we need the items from the start of the array
        // where the startTime <= now + liveitems_latest_pending
        $maxPending = (int) $_config['liveitems_max_pending'];
        $pndTimeSeconds = (int) ($_config['liveitems_latest_pending'] * 86400);
        $pndTimeInterval = DateInterval::createFromDateString("$pndTimeSeconds seconds");
        $pndTime = $genTime->add($pndTimeInterval);
        $pendingLiveItems = array_filter(
            array_slice($pendingLiveItems, 0, $maxPending),
            function ($liveItem) use ($pndTime) {
                $itemTime = new DateTime((string) $liveItem['data']->startTime);
                return $itemTime <= $pndTime;
            }
        );

        $liveItems = array_merge($endedLiveItems, $liveLiveItems, $pendingLiveItems);
        foreach ($liveItems as $liveItem) {
            write_live_item($writer, $liveItem, $feedContext);
        }
    }

    // Set a maximum amount of episodes generated in the feed
    $maxEpisodes = count($files);
    if (strtolower($_config['recent_episode_in_feed']) != 'all') {
        $maxEpisodes = intval($_config['recent_episode_in_feed']);
    }

    // Items (Episodes) in XML
    for ($i = 0; $i < $maxEpisodes; $i++) {
        write_episode_item($writer, $files[$i], $feedContext);
    }

    // Close the tags
    $writer->endElement(); // channel
    $writer->endElement(); // rss
    $writer->endDocument();

    return $writer->outputMemory();
}

function generateRSS()
{
    // Make variables available in this scope
    global $config;

    $feedDir = $config['absoluteurl'] . $config['feed_dir'];

    // Create path if it doesn't exist
    if (!is_dir($feedDir)) {
        mkdir($feedDir);
    }

    $xml = generateRssFeed($config);
    return file_put_contents($feedDir . 'feed.xml', $xml);
}
