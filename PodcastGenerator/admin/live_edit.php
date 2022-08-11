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

if (isset($_GET['delete'])) {
    // Delete live item
    checkToken();

    deleteLiveItem($targetfile, $config);
    generateRSS();
    pingServices();

    header('Location: ' . $config['url'] . $config['indexfile']);
    die();
} elseif (count($_POST) > 0) {
    // Edit live item
    checkToken();

    // Check for required fields
    $req_fields = [
        $_POST['title'],
        $_POST['status'],
        $_POST['shortDesc'],
        $_POST['startDate'],
        $_POST['startTime'],
        $_POST['endDate'],
        $_POST['endTime']
    ];
    foreach ($req_fields as $req_field) {
        if (empty($req_field)) {
            $error = _('Missing fields');
            goto error;
        }
    }

    // Validate status
    if (
        $_POST['status'] != LIVEITEM_STATUS_LIVE
        && $_POST['status'] != LIVEITEM_STATUS_ENDED
        && $_POST['status'] != LIVEITEM_STATUS_PENDING
    ) {
        $error = _('Invalid status');
        goto error;
    }

    // Validate author email field
    if (!empty($_POST['authorEmail'])) {
        if (!filter_var($_POST['authorEmail'], FILTER_VALIDATE_EMAIL)) {
            $error = _('Invalid Author E-Mail provided');
            goto error;
        }
    }

    // Validate start and end times
    $startTime = new DateTime($_POST['startDate'] . ' ' . $_POST['startTime']);
    $endTime = new DateTime($_POST['endDate'] . ' ' . $_POST['endTime']);
    if ($startTime > $endTime) {
        $error = _('Start date/time must be earlier than end date/time');
        goto error;
    }

    // Validate short description length
    if (strlen($_POST['shortDesc']) > 255) {
        $error = _("Size of the 'Short Description' exceeded");
        goto error;
    }

    // If we have custom tags, ensure that they're valid XML
    $customTags = $_POST['customtags'];
    if (!isWellFormedXml($customTags)) {
        if ($config['customtagsenabled'] == 'yes') {
            $error = _('Custom tags are not well-formed');
            goto error;
        } else {
            // if we have custom tags disabled and the POST value is misformed,
            // just clear it out.
            $customTags = '';
        }
    }

    // Load the live item
    $liveItem = loadLiveItem($targetfile, $config);

    // Determine current cover art file
    $currentCoverFile = $liveItem['image']['path'];
    $currentCoverUrl = $liveItem['image']['url'];
    
    // Process the cover image, if one was provided
    $coverImage = '';
    if (!empty($_FILES['cover']['name'])) {
        $coverImage = basename($_FILES['cover']['name']);
        $coverImageExt = pathinfo($coverImage, PATHINFO_EXTENSION);

        $validExtensions = getSupportedFileExtensions($config, ['image']);
        $validCoverFileExt = in_array($coverImageExt, $validExtensions);
        if (!$validCoverFileExt) {
            $error = sprintf(_('%s has invalid file extension'), $coverImage);
            goto error;
        }

        $coverImageFile = makeUniqueFilename($imagesDir . $coverImage);
        if (!move_uploaded_file($_FILES['cover']['tmp_name'], $coverImageFile)) {
            $error = sprintf(_('%s was not uploaded successfully'), $coverImage);
            goto error;
        }

        $coverMimeType = getmime($coverImageFile);
        if (!$coverMimeType) {
            $error = _('The uploaded cover art file is not readable (permission error)');
            goto error;
        }

        $validMimeTypes = getSupportedMimeTypes($config, ['image']);
        $validCoverMimeType = in_array($coverMimeType, $validMimeTypes);
        if (!$validCoverMimeType) {
            $error = sprintf(_('%s has unsupported MIME content type %s'), $coverImage, $coverMimeType);
            // Delete the file if the mime type is invalid
            unlink($coverImageFile);
            goto error;
        }

        // Newer cover art files go on top of the list
        if (!empty($coverImageFile) && $coverImageFile != $currentCoverFile) {
            array_unshift($liveItem['previousImages'], $currentCoverFile);
            $currentCoverFile = $coverImageFile;
            $currentCoverUrl = $config['url'] . $config['img_dir'] . basename($coverImageFile);
        }

        $liveItem['image']['path'] = $coverImageFile;
        $liveItem['image']['url'] = $currentCoverUrl;
    }

    // update remaining values and save
    $liveItem['title'] = $_POST['title'];
    $liveItem['status'] = $_POST['status'];
    $liveItem['startTime'] = $startTime;
    $liveItem['endTime'] = $endTime;
    $liveItem['shortDesc'] = $_POST['shortDesc'];
    $liveItem['streamInfo']['url'] = $_POST['streamUrl'];
    $liveItem['streamInfo']['mimeType'] = $_POST['streamType'];
    $liveItem['author']['name'] = $_POST['authorName'];
    $liveItem['author']['email'] = $_POST['authorEmail'];
    $liveItem['customTags'] = $_POST['customtags'];

    saveLiveItem($liveItem, $targetfile);
    generateRSS();
    pingServices();

    $success = true;

    error:
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
        <?php } elseif ($success) { ?>
            <p style="color: green;"><?= _('Successfully updated live item!') ?></p>
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
