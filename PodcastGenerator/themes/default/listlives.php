<?php

if (!isset($liveItems) || empty($liveItems)) {
    ?><div class="col-lg-6"><p><?= _('No live or upcoming streams at this time.') ?></p></div><?php
    goto end;
}

// List live items
foreach ($liveItems as $liveItem) {
    $coverImage = $liveItem['cover']['url'];
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
    $streamType = $liveItem['streamInfo']['type'];
    if (empty($streamUrl)) {
        $streamUrl = $config['liveitems_default_stream'];
        $streamType = $config['liveitems_default_mimetype'];
    } elseif (empty($streamType)) {
        $streamType = $config['liveitems_default_mimetype'];
    }

    $filename = $liveItem['filename'];
    if (str_starts_with($filename, '_live_')) {
        $filename = substr($filename, 6);
    }
    if (str_ends_with($filename, '.xml')) {
        $filename = substr($filename, 0, -4);
    }
    $moreUrl = $config['livefile'] . '?name=' . $filename;

    ?>
    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mb-4">
        <div class="card h-100">
            <img class="card-img-top mb-1" style="max-width: 100%; max-height: 100%;" src="<?= $coverImage ?>">
            <div class="card-body">
                <h5><a href="<?= $moreUrl ?>"><?= $liveItem['title'] ?></a></h5>

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

                <?php if ($loggedin) { ?>
                    <a class="btn btn-danger btn-sm" href="admin/live_edit.php?name=<?= $liveItem["filename"] ?>">
                        <?= $editdelete ?>
                    </a>
                <?php } ?>

                <a class="btn btn-outline-primary btn-sm" href="<?= $moreUrl ?>"><?= $more ?></a>
                <a class="btn btn-outline-success btn-sm" href="<?= $streamUrl ?>"><?= $directLink ?></a>
                <br>
            </div>
        </div>
    </div>
    <?php
}

end:
