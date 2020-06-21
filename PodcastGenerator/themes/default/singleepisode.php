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
    $metadata = '(' . $correctepisode["episode"]["fileInfoPG"]["bitrate"] . ' kbps ' . $correctepisode["episode"]["fileInfoPG"]["frequency"] . ' Hz)';
} else {
    $type = 'invalid';
}

echo '  <div class="col-lg-12">';
echo '  <div class="card mb-5">';
echo '  <div class="row no-gutters">';
// Check for image
// The imgPG value has the highest priority
if ($correctepisode["episode"]["imgPG"] != "") {
    echo '  <div class="col-md-4">';
    echo '  <img class="card-img" src="' . $correctepisode["episode"]["imgPG"] . '">';
    echo '  </div>';
} elseif (
    file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.jpg') ||
    file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png')
) {
    // TODO Really ugly code, needs to be done more beatiful
    $filename = file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png') ?
        $config["url"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png' :
        $config["url"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.jpg';
    echo '  <div class="col-md-4">';
    echo '  <img class="card-img" src="' . $filename . '">';
    echo '  </div>';
}
// If no cover art, we use itunes_image.jpg
else {
    echo '  <div class="col-md-4">';
    echo '  <img class="card-img" src="' . $config["url"] . $config["img_dir"] . "itunes_image.jpg" . '">';
    echo '  </div>';
}
echo '  <div class="col">';
echo '  <div class="card-body">';
echo '  <h4>' . $correctepisode["episode"]["titlePG"] . '</h4>';
echo '  <p><i class="fa fa-calendar" aria-hidden="true"></i> <small class="text-muted">' . $correctepisode["episode"]["moddate"] . '</small></p>';
echo '  <p class="card-text"><small>' . $correctepisode["episode"]["shortdescPG"] . '</small></p>';
if (isset($_SESSION["username"])) {
    echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . $episodes[$i]["episode"]["filename"] . '">' . $editdelete . '</a>';
}
echo '  <a class="btn btn-outline-success btn-sm" href="media/' . $correctepisode["episode"]["filename"] . '">' . $download . '</a><br>';
if ($type != 'invalid') {
    echo '  <small style="font-size:65%" class="text-muted">' . $filetype . ': ' . strtoupper(pathinfo($config["upload_dir"] . $correctepisode["episode"]["filename"], PATHINFO_EXTENSION)) . '
                - ' . $size . ': ' . $correctepisode["episode"]["fileInfoPG"]["size"] . ' MB - ' . $duration . ': ' . $correctepisode["episode"]["fileInfoPG"]["duration"] . 'm ' . $metadata . '</small>';
}
echo '</div>';
echo '</div>';
echo '<div style="background-color: #f1f3f4;" class="card-footer w-100">';
if (strtolower($config["enablestreaming"]) == "yes") {
    if ($type == 'audio') {
        echo '  <audio controls>';
        echo '      <source src="' . $config["upload_dir"] . $episodes[$i]["episode"]["filename"] . '" type="' . $mime . '">';
        echo '  </audio>';
    } elseif ($type == 'video') {
        echo '  <video controls width="250">';
        echo '      <source src="' . $config["upload_dir"] . $correctepisode["episode"]["filename"] . '" type="' . $mime . '">';
        echo '  </video>';
    }
}
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

end: echo "";
