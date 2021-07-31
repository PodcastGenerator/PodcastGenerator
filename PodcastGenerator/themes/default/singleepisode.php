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
    print(_('Episode does not exist'));
    goto end;
}

// Get mime
$mime = getmime($config["absoluteurl"] . $config["upload_dir"] . $correctepisode["episode"]["filename"]);
if (!$mime) {
    $mime = null;
}
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

if ($correctepisode["episode"]["imgPG"] != "") {
    $coverimage = $correctepisode["episode"]["imgPG"];
} elseif (file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.jpg')) {
    $coverimage = $config["url"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.jpg';
} elseif (file_exists($config["absoluteurl"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png')) {
    $coverimage = $config["url"] . $config["img_dir"] . $correctepisode["episode"]["fileid"] . '.png';
} else {
    $coverimage = $config["url"] . $config["img_dir"] . "itunes_image.jpg";
}
?>

<div class="col-lg-12">
    <div class="card mb-5">
        <div class="row no-gutters">
            <div class="col-md-4">
                <img class="card-img" src="<?= $coverimage ?>">
            </div>

            <div class="col">
                <div class="card-body">
                    <h4><?= $correctepisode["episode"]["titlePG"] ?></h4>
                    <p><i class="fa fa-calendar" aria-hidden="true"></i> <small class="text-muted"><?= $correctepisode["episode"]["moddate"] ?></small></p>
                    <p class="card-text"><small><?= $correctepisode["episode"]["shortdescPG"] ?></small></p>

                    <?php if (isset($correctepisode["episode"]["longdescPG"])) { ?>
                        <p class="card-text"><?= $correctepisode["episode"]["longdescPG"] ?></small></p>
                    <?php } ?>
                    <?php if (isset($_SESSION["username"])) { ?>
                        <a class="btn btn-dark btn-sm" href="admin/episodes_edit.php?name=<?= $episodes[$i]["episode"]["filename"] ?>"><?= $editdelete ?></a>
                    <?php } ?>
                    <a class="btn btn-outline-success btn-sm" href="media/<?= $correctepisode["episode"]["filename"] ?>"><?= $download ?></a><br>

                    <?php if ($type != 'invalid') { ?>
                        <small style="font-size:65%" class="text-muted">
                            <?= $filetype ?>: <?= strtoupper(pathinfo($config["upload_dir"] . $correctepisode["episode"]["filename"], PATHINFO_EXTENSION)) ?> -
                            <?= $size ?>: <?= $correctepisode["episode"]["fileInfoPG"]["size"] ?> MB -
                            <?= $duration ?>: <?= $correctepisode["episode"]["fileInfoPG"]["duration"] ?>m
                            <?= $metadata ?>
                        </small>
                    <?php } ?>
                </div>
            </div>

            <div style="background-color: #f1f3f4;" class="card-footer w-100">
                <?php if (strtolower($config["enablestreaming"]) == "yes") { ?>
                    <?php if ($type == 'audio') { ?>
                        <audio controls>
                            <source src="<?= $config["upload_dir"] . $episodes[$i]["episode"]["filename"] ?>" type="<?= $mime ?>">
                        </audio>
                    <?php } elseif ($type == 'video') { ?>
                        <video controls width="250">';
                            <source src="<?= $config["upload_dir"] . $correctepisode["episode"]["filename"] ?>" type="<?= $mime ?>">
                        </video>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php end: ?>