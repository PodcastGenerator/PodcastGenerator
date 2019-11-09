<?php
$correctepisode = array();
for ($i = 0; $i < sizeof($episodes); $i++) {
    if ($episodes[$i]["episode"]["filename"] == $_GET["name"]) {
        $correctepisode = $episodes[$i];
        break;
    }
}
// Check if episode was not found
if (sizeof($correctepisode) == 0) {
    echo "Episode not found";
    goto end;
}
echo '<div class="col-lg-12">';
echo '  <h1>' . $correctepisode["episode"]["titlePG"] . '</h1>';                                                                              // Headline
echo '  <small>' . $correctepisode["episode"]["moddate"] . '</small><br>';                                                                    // Pub Date
echo '  <small>' . $correctepisode["episode"]["shortdescPG"] . '</small><br>';                                                                // Short description
if (isset($_SESSION["username"])) {
    echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . $episodes[$i]["episode"]["filename"] . '">Edit/Delete (Admin)</a>';
}
echo '  <a class="btn btn-outline-success btn-sm" href="media/' . $correctepisode["episode"]["filename"] . '">Download</a><br>';              // Buttons
echo '  <small>Filetype: ' . strtoupper(pathinfo($_config["upload_dir"] . $$correctepisode["episode"]["filename"], PATHINFO_EXTENSION)) . '
                - Size: ' . $correctepisode["episode"]["fileInfoPG"]["size"] . ' MB - Duration: ' . $correctepisode["episode"]["fileInfoPG"]["duration"] . ' m (' . $correctepisode["episode"]["fileInfoPG"]["bitrate"] . ' kbps ' . $correctepisode["episode"]["fileInfoPG"]["frequency"] . ' Hz)</small><br>';
echo '  <audio controls>';
echo '      <source src="' . $config["upload_dir"] . $correctepisode["episode"]["filename"] . '" type="' . mime_content_type($config["upload_dir"] . $correctepisode["episode"]["filename"]) . '">';
echo '  </audio>';
echo '</div>';

end:
echo "";