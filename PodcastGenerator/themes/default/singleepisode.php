<?php
$correctepisode = array();
for ($i = 0; $i < sizeof($episodes); $i++) {
    if ($episodes[$i]["episode"]["filename"] == $_GET[$link]) {
        $correctepisode = $episodes[$i];
        break;
    }
}
// Check if episode was not found
if (sizeof($correctepisode) == 0) {
    echo _('Episode does not exist');
    goto end;
}

// Get mime
$mime = getmime($config["absoluteurl"] . $config["upload_dir"] . $correctepisode["episode"]["filename"]);
if (!$mime)
    $mime = null;
$type = '';
$metadata = '';
if (substr($mime, 0, 5) == 'video') {
    $type = 'video';
} elseif (substr($mime, 0, 5) == 'audio' || $mime == 'application/ogg') {
    $type = 'audio';
    $metadata = '(' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["bitrate"]) . ' kbps ' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["frequency"]) . ' Hz)';
} else {
    $type = 'invalid';
}

echo '<div class="col-lg-12">';
echo '  <h1>' . htmlspecialchars($correctepisode["episode"]["titlePG"]) . '</h1>';                                                                              // Headline
echo '  <small>' . htmlspecialchars($correctepisode["episode"]["moddate"]) . '</small><br>';                                                                    // Pub Date
echo '  <small>' . htmlspecialchars($correctepisode["episode"]["shortdescPG"]) . '</small><br>';                                                                // Short description
if (isset($_SESSION["username"])) {
    echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '">' . $editdelete . '</a>';
}
echo '  <a class="btn btn-outline-success btn-sm" href="media/' . htmlspecialchars($correctepisode["episode"]["filename"]) . '">' . $download . '</a><br>';              // Buttons
if ($type != 'invalid') {
    echo '  <small>' . $filetype . ': ' . htmlspecialchars(strtoupper(pathinfo($config["upload_dir"] . $correctepisode["episode"]["filename"], PATHINFO_EXTENSION))) . '
                - ' . $size . ': ' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["size"]) . ' MB - ' . $duration . ': ' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["duration"]) . 'm ' . $metadata . '</small><br>';
}
if (strtolower($config["enablestreaming"]) == "yes") {
    if ($type == 'audio') {
        echo '  <audio controls>';
        echo '      <source src="' . htmlspecialchars($config["upload_dir"]) . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '" type="' . $mime . '">';
        echo '  </audio>';
    } elseif ($type == 'video') {
        echo '  <video controls width="250">';
        echo '      <source src="' . htmlspecialchars($config["upload_dir"]) . htmlspecialchars($correctepisode["episode"]["filename"]) . '" type="' . $mime . '">';
        echo '  </video>';
    }
}
echo '</div>';

end: echo "";
