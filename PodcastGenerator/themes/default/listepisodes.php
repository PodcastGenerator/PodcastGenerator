<?php
if (isset($no_episodes)) {
    ?><div class="col-lg-6"><p><?= $no_episodes ?></p></div><?php
    goto end;
}

$loggedin = isset($_SESSION["username"]);

// List episodes
for ($i = 0; $i < sizeof($episode_chunk); $i++) {
    $item = $episode_chunk;
    $mime = getmime($config["absoluteurl"] . $config["upload_dir"] . $item[$i]["episode"]["filename"]);
    if (!$mime) {
        continue;
    }
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

    if ($item[$i]["episode"]["imgPG"] != "") {
        $coverimage = $item[$i]["episode"]["imgPG"];
    } elseif (file_exists($config["absoluteurl"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.jpg')) {
        $coverimage = $config["url"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.jpg';
    } elseif (file_exists($config["absoluteurl"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.png')) {
        $coverimage = $config["url"] . $config["img_dir"] . $item[$i]["episode"]["fileid"] . '.png';
    } else {
        $coverimage = $config["url"] . $config["img_dir"] . "itunes_image.jpg";
    } ?>

    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mb-4">
        <div class="card h-100">
            <img class="card-img-top mb-1" style="max-width: 100%; max-height: 100%;" src="<?= $coverimage ?>">
            <div class="card-body">
                <h5><a href="<?= $config['indexfile'] . '?' . $link . '=' . $item[$i]["episode"]["filename"] ?>"><?= $item[$i]["episode"]["titlePG"] ?></a></h5>

                <p><i class="fa fa-calendar" aria-hidden="true"></i> <small class="text-muted"><?= $item[$i]["episode"]["moddate"] ?></small></p>
                <p class="card-text"><small><?= $item[$i]["episode"]["shortdescPG"] ?></small></p>

                <?php if ($loggedin) { ?>
                    <a class="btn btn-danger btn-sm" href="admin/episodes_edit.php?name=<?= $item[$i]["episode"]["filename"] ?>"><?= $editdelete ?></a>
                <?php } ?>

                <a class="btn btn-outline-primary btn-sm" href="<?= $config['indexfile'] . '?' . $link . '=' . $item[$i]["episode"]["filename"] ?>"><?= $more ?></a>
                <a class="btn btn-outline-success btn-sm" href="media/<?= $item[$i]["episode"]["filename"] ?>"><?= $download ?></a><br>

                <?php if ($type != 'invalid') { ?>
                    <small style="font-size:65%" class="text-muted">
                        <?= $filetype ?>: <?= strtoupper(pathinfo($config["upload_dir"] . $item[$i]["episode"]["filename"], PATHINFO_EXTENSION)) ?> -
                        <?= $size ?>: <?= $item[$i]["episode"]["fileInfoPG"]["size"] ?> MB -
                        <?= $duration ?>: <?= $item[$i]["episode"]["fileInfoPG"]["duration"] ?>m
                        <?= $metadata ?>
                    </small>
                <?php } ?>
            </div>

            <div style="background-color: #f1f3f4;" class="card-footer">
                <?php if (strtolower($config["enablestreaming"]) == "yes") { ?>
                    <?php if ($type == 'audio') { ?>
                        <audio controls>
                            <source src="<?= $config["upload_dir"] . $item[$i]["episode"]["filename"] ?>" type="<?= $mime ?>">
                        </audio>
                    <?php } elseif ($type == 'video') { ?>
                        <video controls width="250">
                            <source src="<?= $config["upload_dir"] . $item[$i]["episode"]["filename"] ?>" type="<?= $mime ?>">
                        </video>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php end: ?>
