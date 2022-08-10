<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

require 'checkLogin.php';
require '../core/include_admin.php';

require_once(__DIR__ . '/../vendor/autoload.php');

if (!isset($_GET['name'])) {
    die(_('No name given'));
}

checkPath($_GET['name']);

$uploadDir = $config['absoluteurl'] . $config['upload_dir'];
$imagesDir = $config['absoluteurl'] . $config['img_dir'];

$targetfile = $uploadDir . $_GET['name'];
$targetfile_without_ext = $uploadDir . pathinfo($targetfile, PATHINFO_FILENAME);

if (!file_exists($targetfile)) {
    die(_('Episode does not exist'));
}

// Delete episode
if (isset($_GET['delete'])) {
    checkToken();

    deleteLiveItem($targetfile, $config);
    generateRSS();
    pingServices();

    header('Location: ' . $config['url'] . $config['indexfile']);
    die();
}

$liveItem = loadLiveItem($targetfile, $config);

$coverart = $liveItem['image']['url'];
if (empty($coverart)) {
    // check for default live item image
    if (!empty($config['liveitem_default_cover'])) {
        $coverart = $config['liveitem_default_cover'];
    } elseif (!empty($config['podcast_cover'])) {
        $coverart = $config['podcast_cover'];
    } else {
        $coverart = 'itunes_image.jpg';
    }
    $coverart = $config['url'] . $config['img_dir'] . $coverart;
}

$mimetypes = getSupportedMimeTypes($config, ['audio', 'video']);
$mimeTypeOptions = [
    [ 'value' => '', 'label' => sprintf(_('Default (%s)'), $config['liveitems_default_mimetype']) ]
];
foreach ($mimetypes as $mimetype) {
    $mimeTypeOptions[] = [ 'value' => $mimetype, 'label' => $mimetype ];
}

$statusOptions = [
    [ 'value' => LIVEITEM_STATUS_PENDING, 'label' => _('Pending') ],
    [ 'value' => LIVEITEM_STATUS_LIVE, 'label' => _('Live') ],
    [ 'value' => LIVEITEM_STATUS_ENDED, 'label' => _('Ended') ]
];

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Live Items') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $config['url'] ?>favicon.ico">
    <style>
        label.req::after { content: "*"; color: red; }
    </style>
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?= _('Edit Live Item') ?></h1>

        <?php if (isset($error)) { ?>
            <p style="color: red;"><?= $error ?></p>
        <?php } ?>

        <form action="live_edit.php?name=<?= htmlspecialchars($_GET["name"]) ?>"
              method="POST"
              enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h4><?= _('Main Information') ?></h4>
                    <hr>
                    <input type="hidden" name="guid" value="<?= htmlspecialchars($liveItem['guid']) ?>">
                    <div class="form-group">
                        <label for="title" class="req"><?= _('Title') ?>:</label><br>
                        <input type="text" id="title" name="title" class="form-control"
                               value="<?= htmlspecialchars($liveItem['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="shortDesc" class="req"><?= _('Short Description') ?>:</label><br>
                        <input type="text" id="shortDesc" name="shortDesc" class="form-control"
                               value="<?= htmlspecialchars($liveItem['shortDesc']) ?>"
                               maxlength="255" oninput="shortDescCheck()" required>
                        <i id="shortDesc_counter"><?= sprintf(_('Characters remaining: %d'), 255) ?></i>
                    </div>
                    <div class="form-group row">
                        <div class="col-12"><?= _('Start Time') ?>:</div>
                        <div class="col-6">
                            <label for="startDate" class="req"><?= _('Date') ?>:</label><br>
                            <input name="startDate" id="startDate" type="date" required
                                   value="<?= $liveItem['startTime']->format('Y-m-d') ?>">
                        </div>
                        <div class="col-6">
                            <label for="startTime" class="req"><?= _('Time') ?>:</label><br>
                            <input name="startTime" id="startTime" type="time" required
                                   value="<?= $liveItem['startTime']->format('H:i') ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12"><?= _('End Time') ?>:</div>
                        <div class="col-6">
                            <label for="endDate" class="req"><?= _('Date') ?>:</label><br>
                            <input name="endDate" id="endDate" type="date" required
                                   value="<?= $liveItem['endTime']->format('Y-m-d') ?>">
                        </div>
                        <div class="col-6">
                            <label for="endTime" class="req"><?= _('Time') ?>:</label><br>
                            <input name="endTime" id="endTime" type="time" required
                                   value="<?= $liveItem['endTime']->format('H:i') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <?= _('Status') ?>:<br>
                        <?php htmlOptionRadios('status', $liveItem['status'], $statusOptions); ?>
                    </div>
                    <div class="form-group">
                        <?= _('Stream Information') ?>:<br>
                        <label for="streamUrl"><?= _('URL') ?>:</label><br>
                        <input name="streamUrl" id="streamUrl" type="url" class="form-control"
                               value="<?= $liveItem['streamInfo']['url'] ?>"
                               placeholder="<?= $config['liveitems_default_stream'] ?>">
                        <br>
                        <label for="streamType"><?= _('MIME Type') ?>:</label><br>
                        <?php htmlOptionSelect(
                            'streamType',
                            $liveItem['streamInfo']['mimeType'],
                            $mimeTypeOptions,
                            'form-control'
                        ); ?>
                    </div>
                </div>

                <div class="col-6">
                    <h4><?= _('Extra Information') ?></h4>
                    <hr>
                    <div class="form-group">
                        <?= _('Current Cover'); ?>:<br>
                        <img src="<?= htmlspecialchars($coverart) ?>"
                             style="max-height: 350px; max-width: 350px;">
                        <hr>
                        <label for="cover"><?= _('Upload new cover') ?>:</label><br>
                        <input type="file" id="cover" name="cover"><br>
                    </div>
                    <div class="form-group">
                        <label for="longDesc"><?= _('Long Description') ?>:</label><br>
                        <textarea id="longDesc" name="longDesc"
                                  class="form-control"><?= htmlspecialchars($liveItem['longDesc']) ?></textarea>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="authorName"><?= _('Author') ?>:</label><br>
                        <input type="text" id="authorName" name="authorName" class="form-control"
                               placeholder="Author Name"
                               value="<?= htmlspecialchars($liveItem['author']['name']) ?>">
                        <br>
                        <input type="email" id="authorEmail" name="authorEmail" class="form-control"
                               placeholder="Author E-Mail"
                               value="<?= htmlspecialchars($liveItem['author']['email']) ?>">
                        <br>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['customtagsenabled']) ?>">
                        <label for="customtags"><?= _('Custom Tags') ?>:</label><br>
                        <textarea id="customtags" name="customtags"
                                class="form-control"><?= htmlspecialchars($liveItem['customTags']) ?></textarea>
                        <br>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                    <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success">
                </div>
            </div>
        </form>
        <hr>
        <h4><?= _('Delete Episode') ?></h4>
        <form action="episodes_edit.php?name=<?= htmlspecialchars($_GET['name']) ?>&delete=1" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" class="btn btn-danger" value="<?= _('Delete') ?>">
        </form>
    </div>
    <script type="text/javascript">
        function shortDescCheck() {
            let shortdesc = document.getElementById("shortDesc").value;
            let maxlength = 255;
            let remaining = maxlength - shortdesc.length;
            let counter
                = document.getElementById("shortDesc_counter").innerText
                = "<?= _('Characters remaining: %d') ?>".replace('%d', remaining);
        }
        shortDescCheck();
    </script>
</body>

</html>
