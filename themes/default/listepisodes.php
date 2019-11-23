<?php
// List episodes
for ($i = 0; $i < sizeof($episode_chunk); $i++) {
    $item = $episode_chunk;
    echo '<div class="col-lg-6">';
    echo '  <h1><a href="index.php?' . $link . '=' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">' . htmlspecialchars($item[$i]["episode"]["titlePG"]) . '</a></h1>';             // Headline
    echo '  <small>' . htmlspecialchars($item[$i]["episode"]["moddate"]) . '</small><br>';                                                                    // Pub Date
    echo '  <small>' . htmlspecialchars($item[$i]["episode"]["shortdescPG"]) . '</small><br>';                                                                // Short description
    // Display edit button if admin is logged in
    if (isset($_SESSION["username"])) {
        echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">Edit/Delete (Admin)</a>';
    }
    echo '  <a class="btn btn-outline-primary btn-sm" href="index.php?' . $link . '=' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">More</a>
                <a class="btn btn-outline-success btn-sm" href="media/' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">Download</a><br>';                      // Buttons
    echo '  <small>Filetype: ' . strtoupper(pathinfo($config["upload_dir"] . $item[$i]["episode"]["filename"], PATHINFO_EXTENSION)) . '
                - Size: ' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["size"]) . ' MB - Duration: ' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["duration"]) . ' m (' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["bitrate"]) . ' kbps ' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["frequency"]) . ' Hz)</small>';
    if (strtolower($config["enablestreaming"]) == "yes") {
        echo '  <audio controls>';
        echo '      <source src="' . htmlspecialchars($config["upload_dir"]) . htmlspecialchars($item[$i]["episode"]["filename"]) . '" type="' . mime_content_type(htmlspecialchars($config["upload_dir"] . $item[$i]["episode"]["filename"])) . '">';
        echo '  </audio>';
    }
    echo '</div>';
}
