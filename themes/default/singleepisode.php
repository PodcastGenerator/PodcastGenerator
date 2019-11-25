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
    echo "Episode not found";
    goto end;
}
echo '<div class="col-lg-12">';
echo '  <h1>' . htmlspecialchars($correctepisode["episode"]["titlePG"]) . '</h1>';                                                                              // Headline
echo '  <small>' . htmlspecialchars($correctepisode["episode"]["moddate"]) . '</small><br>';                                                                    // Pub Date
echo '  <small>' . htmlspecialchars($correctepisode["episode"]["shortdescPG"]) . '</small><br>';                                                                // Short description
if (isset($_SESSION["username"])) {
    echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '">Edit/Delete (Admin)</a>';
}
echo '  <a class="btn btn-outline-success btn-sm" href="media/' . htmlspecialchars($correctepisode["episode"]["filename"]) . '">Download</a><br>';              // Buttons
echo '  <small>Filetype: ' . htmlspecialchars(strtoupper(pathinfo($config["upload_dir"] . $correctepisode["episode"]["filename"], PATHINFO_EXTENSION))) . '
                - Size: ' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["size"]) . ' MB - Duration: ' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["duration"]) . ' m (' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["bitrate"]) . ' kbps ' . htmlspecialchars($correctepisode["episode"]["fileInfoPG"]["frequency"]) . ' Hz)</small><br>';
if (strtolower($config["enablestreaming"]) == "yes") {
    echo '  <audio controls>';
    echo '      <source src="' . htmlspecialchars($config["upload_dir"]) . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '" type="' . mime_content_type(htmlspecialchars($config["upload_dir"] . $episodes[$i]["episode"]["filename"])) . '">';
    echo '  </audio>';
}
echo '</div>';

end: echo "";
