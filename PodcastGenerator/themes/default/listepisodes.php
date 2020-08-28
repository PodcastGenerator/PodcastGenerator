<?php
// List episodes
if (isset($no_episodes)) {
    echo '<div class="col-lg-6"><p>' . $no_episodes . '</p></div>';
} else {
    for ($i = 0; $i < sizeof($episode_chunk); $i++) {
        $item = $episode_chunk;
        $mime = getmime($config["absoluteurl"] . $config["upload_dir"] . $item[$i]["episode"]["filename"]);
        if (!$mime)
            continue;
        $type = '';
        $metadata =  '';
        if (substr($mime, 0, 5) == 'video') {
            $type = 'video';
        } elseif (substr($mime, 0, 5) == 'audio' || $mime == 'application/ogg') {
            $type = 'audio';
            $metadata = '(' . $item[$i]["episode"]["fileInfoPG"]["bitrate"] . ' kbps ' . $item[$i]["episode"]["fileInfoPG"]["frequency"] . ' Hz)';
        } else {
            $type = 'invalid';
        }

        echo '  <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mb-4">';
        echo '  <div class="card h-100">';
        // Check for image
        // The imgPG value has the highest priority
        if ($item[$i]["episode"]["imgPG"] != "") {
            echo '  <img class="card-img-top mb-1" style="max-width: 100%; max-height: 100%;" src="' . $item[$i]["episode"]["imgPG"] . '">';
        } elseif (
            file_exists($config["absoluteurl"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.jpg') ||
            file_exists($config["absoluteurl"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.png')
        ) {
            // TODO Really ugly code, needs to be done more beatiful
            $filename = file_exists($config["absoluteurl"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.png') ?
                $config["url"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.png' :
                $config["url"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.jpg';
            echo '  <img class="card-img-top mb-1" style="max-width: 100%; max-height: 100%;" src="' . $filename . '">';
        }
        // If no cover art, we use itunes_image.jpg
        else {
            echo '  <img class="card-img-top mb-1" style="max-width: 100%; max-height: 100%;" src="' . $config["url"] . $config["img_dir"] . "itunes_image.jpg" . '">';
        }
        echo '  <div class="card-body">';
        echo '  <h5><a href="' . $config['indexfile'] . '?' . $link . '=' . $item[$i]["episode"]["filename"] . '">' . $item[$i]["episode"]["titlePG"] . '</a></h5>';
        echo '  <p><i class="fa fa-calendar" aria-hidden="true"></i> <small class="text-muted">' . $item[$i]["episode"]["moddate"] . '</small></p> ';
        echo '  <p class="card-text"><small>' . $item[$i]["episode"]["shortdescPG"] . '</small></p>';
        // Display edit button if admin is logged in
        if (isset($_SESSION["username"])) {
            echo '  <a class="btn btn-danger btn-sm" href="admin/episodes_edit.php?name=' . $item[$i]["episode"]["filename"] . '">' . $editdelete . '</a>';
        }
        echo '  <a class="btn btn-outline-primary btn-sm" href="' . $config['indexfile'] . '?' . $link . '=' . $item[$i]["episode"]["filename"] . '">' . $more . '</a>
                <a class="btn btn-outline-success btn-sm" href="media/' . $item[$i]["episode"]["filename"] . '">' . $download . '</a><br>';
        if ($type != 'invalid') {
            echo '  <small style="font-size:65%" class="text-muted">' . $filetype . ': ' . strtoupper(pathinfo($config["upload_dir"] . $item[$i]["episode"]["filename"], PATHINFO_EXTENSION)) . '
                - ' . $size . ': ' . $item[$i]["episode"]["fileInfoPG"]["size"] . ' MB - ' . $duration . ': ' . $item[$i]["episode"]["fileInfoPG"]["duration"] . 'm ' . $metadata . '</small>';
        }
        echo '</div>';
        echo '<div style="background-color: #f1f3f4;" class="card-footer">';
        if (strtolower($config["enablestreaming"]) == "yes") {
            if ($type == 'audio') {
                echo '  <audio controls>';
                echo '      <source src="' . $config["upload_dir"] . $item[$i]["episode"]["filename"] . '" type="' . $mime . '">';
                echo '  </audio>';
            } elseif ($type == 'video') {
                echo '  <video controls width="250">';
                echo '      <source src="' . $config["upload_dir"] . $item[$i]["episode"]["filename"] . '" type="' . $mime . '">';
                echo '  </video>';
            }
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
