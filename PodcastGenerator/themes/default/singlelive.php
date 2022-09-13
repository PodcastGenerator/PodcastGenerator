<?php

if (!isset($liveItem) || $liveItem == null) {
    echo _('The named live stream does not exist.');
    goto end;
}

$coverImage = $liveItem['image']['url'];
if (empty($coverImage)) {
    $imageDir = $config['url'] . $config['img_dir'];

    $coverImage = $config['liveitem_default_cover'];
    if (empty($coverImage)) {
        $coverImage = $config['podcast_cover'];
    } if (empty($coverImage)) {
        $coverImage = 'itunes_image.jpg';
    }

    $coverImage = $imageDir . $coverImage;
}

$streamUrl = $liveItem['streamInfo']['url'];
$streamType = $liveItem['streamInfo']['mimeType'];
if (empty($streamUrl)) {
    $streamUrl = $config['liveitems_default_stream'];
    $streamType = $config['liveitems_default_mimetype'];
} elseif (empty($streamType)) {
    $streamType = $config['liveitems_default_mimetype'];
}
$type = explode('/', $streamType)[0];

$showStreamWidget = strtolower($config['enablestreaming']) == 'yes' && $liveItem['status'] == LIVEITEM_STATUS_LIVE;

?>
<div class="col-lg-12">
    <div class="card mb-5">
        <div class="row no-gutters">
            <div class="col-md-4">
                <img class="card-img" src="<?= $coverImage ?>">
            </div>

            <div class="col">
                <div class="card-body">
                    <h4><?= $liveItem['title'] ?></h4>
                    <p>
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <small class="text-muted">
                            <time class="start" datetime="<?= $liveItem['startTime']->format(DateTime::ISO8601) ?>">
                                <?= $liveItem['startTime']->format($timeFmt) ?>
                            </time>
                            <?php echo '&ndash;'; ?>
                            <time class="end" datetime="<?= $liveItem['endTime']->format(DateTime::ISO8601) ?>">
                                <?= $liveItem['endTime']->format($timeFmt) ?>
                            </time>
                        </small>
                    </p>
                    <p class="card-text"><small><?= $liveItem['shortDesc'] ?></small></p>

                    <?php if (isset($liveItem['longDesc'])) { ?>
                        <p class="card-text"><?= $liveItem['longDesc'] ?></small></p>
                    <?php } ?>
                    <?php if ($loggedIn) { ?>
                        <a class="btn btn-dark btn-sm" href="admin/live_edit.php?name=<?= $liveItem['filename'] ?>">
                            <?= $editdelete ?>
                        </a>
                    <?php } ?>
                    <a class="btn btn-outline-success btn-sm" href="<?= $streamUrl ?>"><?= $directLink ?></a>
                    <br>
                </div>
            </div>

            <?php if ($showStreamWidget) { ?>
                <div style="background-color: #f1f3f4;" class="card-footer w-100">
                    <?php if ($type == 'audio') { ?>
                        <audio controls preload="none">
                            <source src="<?= $streamUrl ?>" type="<?= $streamType ?>">
                        </audio>
                    <?php } elseif ($type == 'video') { ?>
                        <video controls width="250" preload="none">';
                            <source src="<?= $streamUrl ?>" type="<?= $streamType ?>">
                        </video>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php end: ?>
