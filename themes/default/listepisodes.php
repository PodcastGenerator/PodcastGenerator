<?php
// List episodes
for ($i = 0; $i < sizeof($episodes); $i++) {
    echo '<div class="col-lg-6">';
    echo '  <h1><a href="?name=' . $episodes[$i]["episode"]["filename"] . '">' . $episodes[$i]["episode"]["titlePG"] . '</a></h1>';             // Headline
    echo '  <small>' . $episodes[$i]["episode"]["moddate"] . '</small><br>';                                                                    // Pub Date
    echo '  <small>' . $episodes[$i]["episode"]["shortdescPG"] . '</small><br>';                                                                // Short description
    // Display edit button if admin is logged in
    if (isset($_SESSION["username"])) {
        echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . $episodes[$i]["episode"]["filename"] . '">Edit/Delete (Admin)</a>';
    }
    echo '  <a class="btn btn-outline-primary btn-sm" href="?name=' . $episodes[$i]["episode"]["filename"] . '">More</a>
                <a class="btn btn-outline-success btn-sm" href="media/' . $episodes[$i]["episode"]["filename"] . '">Download</a><br>';                      // Buttons
    echo '  <small>Filetype: ' . strtoupper(pathinfo($_config["upload_dir"] . $episodes[$i]["episode"]["filename"], PATHINFO_EXTENSION)) . '
                - Size: ' . $episodes[$i]["episode"]["fileInfoPG"]["size"] . ' MB - Duration: ' . $episodes[$i]["episode"]["fileInfoPG"]["duration"] . ' m (' . $episodes[$i]["episode"]["fileInfoPG"]["bitrate"] . ' kbps ' . $episodes[$i]["episode"]["fileInfoPG"]["frequency"] . ' Hz)</small>';
    echo '  <audio controls>';
    echo '      <source src="' . $config["upload_dir"] . $episodes[$i]["episode"]["filename"] . '" type="' . mime_content_type($config["upload_dir"] . $episodes[$i]["episode"]["filename"]) . '">';
    echo '  </audio>';
    echo '</div>';
}
