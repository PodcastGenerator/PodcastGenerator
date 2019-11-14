<?php
// List episodes
for ($i = 0; $i < sizeof($episodes); $i++) {
    echo '<div class="col-lg-6">';
    echo '  <h1><a href="?name=' . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '">' . htmlspecialchars($episodes[$i]["episode"]["titlePG"]) . '</a></h1>';             // Headline
    echo '  <small>' . htmlspecialchars($episodes[$i]["episode"]["moddate"]) . '</small><br>';                                                                    // Pub Date
    echo '  <small>' . htmlspecialchars($episodes[$i]["episode"]["shortdescPG"]) . '</small><br>';                                                                // Short description
    // Display edit button if admin is logged in
    if (isset($_SESSION["username"])) {
        echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '">Edit/Delete (Admin)</a>';
    }
    echo '  <a class="btn btn-outline-primary btn-sm" href="?name=' . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '">More</a>
                <a class="btn btn-outline-success btn-sm" href="media/' . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '">Download</a><br>';                      // Buttons
    echo '  <small>Filetype: ' . strtoupper(pathinfo($_config["upload_dir"] . $episodes[$i]["episode"]["filename"], PATHINFO_EXTENSION)) . '
                - Size: ' . htmlspecialchars($episodes[$i]["episode"]["fileInfoPG"]["size"]) . ' MB - Duration: ' . htmlspecialchars($episodes[$i]["episode"]["fileInfoPG"]["duration"]) . ' m (' . htmlspecialchars($episodes[$i]["episode"]["fileInfoPG"]["bitrate"]) . ' kbps ' . htmlspecialchars($episodes[$i]["episode"]["fileInfoPG"]["frequency"]) . ' Hz)</small>';
    echo '  <audio controls>';
    echo '      <source src="' . htmlspecialchars($config["upload_dir"]) . htmlspecialchars($episodes[$i]["episode"]["filename"]) . '" type="' . mime_content_type(htmlspecialchars($config["upload_dir"] . $episodes[$i]["episode"]["filename"])) . '">';
    echo '  </audio>';
    echo '</div>';
}
