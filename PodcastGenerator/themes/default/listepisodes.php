<?php
// List episodes
for ($i = 0; $i < sizeof($episode_chunk); $i++) {
    $item = $episode_chunk;
    $mime = mime_content_type($config["absoluteurl"] . $config["upload_dir"] . $item[$i]["episode"]["filename"]);
    $type = '';
    $metadata =  '';
    if (substr($mime, 0, 5) == 'video') {
        $type = 'video';
    } elseif (substr($mime, 0, 5) == 'audio' || $mime == 'application/ogg') {
        $type = 'audio';
        $metadata = '(' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["bitrate"]) . ' kbps ' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["frequency"]) . ' Hz)';
    } else {
        $type = 'invalid';
    }

    echo '<div class="col-lg-6">';
    echo '  <h1><a href="index.php?' . $link . '=' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">' . htmlspecialchars($item[$i]["episode"]["titlePG"]) . '</a></h1>';             // Headline
    echo '  <small>' . htmlspecialchars($item[$i]["episode"]["moddate"]) . '</small><br>';                                                                    // Pub Date
    echo '  <small>' . htmlspecialchars($item[$i]["episode"]["shortdescPG"]) . '</small><br>';                                                                // Short description
    // Display edit button if admin is logged in
    if (isset($_SESSION["username"])) {
        echo '  <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">' . $editdelete . '</a>';
    }
    echo '  <a class="btn btn-outline-primary btn-sm" href="index.php?' . $link . '=' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">' . $more . '</a>
                <a class="btn btn-outline-success btn-sm" href="media/' . htmlspecialchars($item[$i]["episode"]["filename"]) . '">' . $download . '</a><br>';                      // Buttons
    if ($type != 'invalid') {
        echo '  <small>Filetype: ' . strtoupper(pathinfo($config["upload_dir"] . $item[$i]["episode"]["filename"], PATHINFO_EXTENSION)) . '
                - Size: ' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["size"]) . ' MB - Duration: ' . htmlspecialchars($item[$i]["episode"]["fileInfoPG"]["duration"]) . 'm ' . $metadata . '</small><br>';
    }
    if (strtolower($config["enablestreaming"]) == "yes") {
        if ($type == 'audio') {
            echo '  <audio controls>';
            echo '      <source src="' . htmlspecialchars($config["upload_dir"]) . htmlspecialchars($item[$i]["episode"]["filename"]) . '" type="' . $mime . '">';
            echo '  </audio>';
        } elseif ($type == 'video') {
            echo '  <video controls width="250">';
            echo '      <source src="' . htmlspecialchars($config["upload_dir"]) . htmlspecialchars($item[$i]["episode"]["filename"]) . '" type="' . $mime . '">';
            echo '  </video>';
        }
    }
    echo '</div>';
}
