<?php
require "checkLogin.php";
require "../core/include_admin.php";

function generateRSS() {
    // Make variables available in this scope
    global $config, $version;
    // Set the feed header with relevant podcast informations
    $feedhead = "<?xml version=\"1.0\" encoding=\"".$config["feed_encoding"]."\"?>
    <!-- generator=\"Podcast Generator ".$version."\"
    <channel>
        <title>".$config["podcast_title"]."</title>
        <link>".$config["url"]."</link>
        <atom:link href=\"".$config["url"]."feed.xml\" rel=\"self\" type=\"application/rss+xml\" />
        <description>".$config["podcast_description"]."</description>
        <generator>Podcast Generator ".$version." - http://www.podcastgenerator.net</generator>
        <lastBuildDate>".date("r")."</lastBuildDate>
        <language>".substr($config["feed_language"], 0, 2)."</language>
        <copyright>".$config["copyright"]."</copyright>
        <itunes:image href=\"".$config["url"].$config["img_dir"]."itunes_image.jpg\" />
        <image>
            <url>".$config["url"].$config["img_dir"]."itunes_image.jpg</url>
            <title>".$config["podcast_title"]."</title>
            <link>".$config["url"]."</link>
        </image>
        <itunes:summary>".$config["podcast_description"]."</itunes:summary>
        <itunes:subtitle>".$config["podcast_subtitle"]."</itunes:subtitle>
        <itunes:author>".$config["author_name"]."</itunes:author>
        <itunes:owner>
            <itunes:name>".$config["author_name"]."</itunes:name>
            <itunes:email>".$config["author_email"]."</itunes:email>
        </itunes:owner>
        <itunes:explicit>".$config["explicit_podcast"]."</itunes:explicit>
        
        <itunes:category text=\"Arts\"></itunes:category>
        ";
        // Get supported file extensions
        $supported_extensions = array();
        $supported_extensions_xml = simplexml_load_file("../components/supported_media/supported_media.xml");
        foreach ($supported_extensions_xml->mediaFile->extension as $item) {
            array_push($supported_extensions, $item);
        }
        // Get episodes ordered by pub date
        $files = array();
        if($handle = opendir("../" . $config["upload_dir"])) {
            while(false !== ($entry = readdir($handle))) {
                // Sort out all files with invalid file extensions
                if(in_array(pathinfo("../" . $config["upload_dir"] . $entry, PATHINFO_EXTENSION), $supported_extensions)) {
                    array_push($files, [
                        "filename" => $entry,
                        "lastModified" => filemtime("../" . $config["upload_dir"] . $entry)
                    ]);
                }
            }
        }
        // Bubble sort files
        for($i = 0; $i < sizeof($files) - 1; $i++) {
            for($j = 0; $j < sizeof($files)-$i-1; $j++) {
                if($files[$j]["lastModified"] > $files[$j + 1]["lastModified"]) {
                    $old = $files[$j + 1]["lastModified"];
                    $files[$j + 1]["lastModified"] = $files[$j]["lastModified"];
                    $files[$j]["lastModified"] = $old;
                }
            }
        }
        var_dump($files);

}

generateRSS();