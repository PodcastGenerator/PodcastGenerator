<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################
require 'checkLogin.php';
require '../core/include_admin.php';

if (count($_POST) > 0) {
    checkToken();
    // CHeck if all fields are set (except "category")
    $req_fields = [
        $_POST['title'],
        $_POST['shortdesc'],
        $_POST['date'],
        $_POST['time'],
        $_POST['explicit']
    ];
    // Check if fields are missing
    for ($i = 0; $i < count($req_fields); $i++) {
        if (empty($req_fields[$i])) {
            $error = _('Missing fields');
            goto error;
        }
    }

    // If no categories were selected, add the 'uncategorized'
    // category.  Otherwise, ensure that no more than three categories
    // were actually selected.
    if (empty($_POST['category'])) {
        $_POST['category'] = array();
        array_push($_POST['category'], 'uncategorized');
    } elseif (isset($_POST['category']) && count((array)$_POST['category']) > 3) {
        $error = _('Too many categories selected (max: 3)');
        goto error;
    }

    // Fill up empty categories (to avoid warnings)
    for ($i = 0; $i < 3; $i++) {
        if (!isset($_POST['category'][$i])) {
            $_POST['category'][$i] = '';
        }
    }

    // Check author e-mail
    if (!empty($_POST['authoremail'])) {
        if (!filter_var($_POST['authoremail'], FILTER_VALIDATE_EMAIL)) {
            $error = _('Invalid Author E-Mail provided');
            goto error;
        }
    }

    // Check episode and season numbers
    if (!empty($_POST['episodenum'])) {
        if (!is_numeric($_POST['episodenum'])) {
            $error = _('Invalid Episode Number provided');
            goto error;
        }
        $episodeNum = $_POST['episodenum'] + 0;
        if (!is_integer($episodeNum) || $episodeNum < 1) {
            $error = _('Invalid Episode Number provided');
            goto error;
        }
    }
    if (!empty($_POST['seasonnum'])) {
        if (!is_numeric($_POST['seasonnum'])) {
            $error = _('Invalid Season Number provided');
            goto error;
        }
        $seasonNum = $_POST['seasonnum'] + 0;
        if (!is_integer($seasonNum) || $seasonNum < 1) {
            $error = _('Invalid Season Number provided');
            goto error;
        }
    }

    if (strlen($_POST['shortdesc']) > 255) {
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

    // Skip files if they are not strictly named
    if ($config['strictfilenamepolicy'] == 'yes') {
        if (!preg_match('/^[\w.]+$/', basename($_FILES['file']['name']))) {
            $error = _('Invalid filename, only A-Z, a-z, underscores and dots are permitted');
            goto error;
        }
    }

    $link = str_replace('?', '', $config['link']);
    $link = str_replace('=', '', $link);
    $link = str_replace('$url', '', $link);

    $targetfile = '../' . $config['upload_dir'] . $_POST['date'] . '_' . basename($_FILES['file']['name']);
    $targetfile = str_replace(' ', '_', $targetfile);
    if (file_exists($targetfile)) {
        $appendix = 1;
        while (file_exists($targetfile)) {
            $targetfile = '../' . $config['upload_dir'] . $_POST['date'] . '_' . $appendix . '_' . basename($_FILES['file']['name']);
            $targetfile = str_replace(' ', '_', $targetfile);
            $appendix++;
        }
    }
    $targetfile = strtolower($targetfile);
    $targetfile_without_ext = strtolower('../' . $config['upload_dir'] . pathinfo($targetfile, PATHINFO_FILENAME));

    $validTypes = simplexml_load_file('../components/supported_media/supported_media.xml');
    $fileextension = pathinfo($targetfile, PATHINFO_EXTENSION);
    $validFileExt = false;
    foreach ($validTypes->mediaFile as $item) {
        if ($fileextension == $item->extension) {
            $validFileExt = true;
            break;
        }
    }
    if (!$validFileExt) {
        $error = _('Invalid file extension');
        goto error;
    }

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetfile)) {
        $error = _('The file upload was not successfully');
        goto error;
    }

    $mimetype = getmime($targetfile);

    if (!$mimetype) {
        $error = _('The uploaded file is not readable (permission error)');
        goto error;
    }

    $validMimeType = false;
    foreach ($validTypes->mediaFile as $item) {
        if ($mimetype == $item->mimetype) {
            $validMimeType = true;
            break;
        }
    }

    if (!$validMimeType) {
        $error = sprintf(
            _('Unsupported MIME content type "%s" detected for file with extension "%s"'),
            $mimetype,
            $fileextension
        );
        // Delete the file if the mime type is invalid
        unlink($targetfile);
        goto error;
    }

    // build categories list from post data
    $categories = array();
    for ($i = 0; $i < 3; $i++) {
        $categories[$i] = isset($_POST['category'][$i])
            ? $_POST['category'][$i]
            : ($i == 0 ? 'uncategorized' : '');
    }

    // Get datetime
    $datetime = strtotime($_POST['date'] . ' ' . $_POST['time']);
    // Set file date to this date
    touch($targetfile, $datetime);

    $fileinfo = getID3Info($targetfile);
    $duration = $fileinfo['playtime_string'];           // Get duration
    $bitrate = $fileinfo['audio']['bitrate'];           // Get bitrate
    $frequency = $fileinfo['audio']['sample_rate'];     // Frequency

    // Go and actually generate the episode
    // It easier to not dynamically generate the file
    $episodefeed = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
	<episode>
	    <guid>' . htmlspecialchars($config['url'] . "?" . $link . "=" . $targetfile) . '</guid>
	    <titlePG>' . htmlspecialchars($_POST['title'], ENT_NOQUOTES) . '</titlePG>
	    <episodeNumPG>' . $_POST['episodenum'] . '</episodeNumPG>
	    <seasonNumPG>' . $_POST['seasonnum'] . '</seasonNumPG>
	    <shortdescPG><![CDATA[' . $_POST['shortdesc'] . ']]></shortdescPG>
	    <longdescPG><![CDATA[' . $_POST['longdesc'] . ']]></longdescPG>
	    <imgPG></imgPG>
	    <categoriesPG>
	        <category1PG>' . htmlspecialchars($categories[0]) . '</category1PG>
	        <category2PG>' . htmlspecialchars($categories[1]) . '</category2PG>
	        <category3PG>' . htmlspecialchars($categories[2]) . '</category3PG>
	    </categoriesPG>
	    <keywordsPG>' . htmlspecialchars($_POST['itunesKeywords']) . '</keywordsPG>
	    <explicitPG>' . $_POST['explicit'] . '</explicitPG>
	    <authorPG>
	        <namePG>' . htmlspecialchars($_POST['authorname']) . '</namePG>
	        <emailPG>' . htmlspecialchars($_POST['authoremail']) . '</emailPG>
	    </authorPG>
	    <fileInfoPG>
	        <size>' . intval($_FILES['file']['size'] / 1000 / 1000) . '</size>
	        <duration>' . $duration . '</duration>
	        <bitrate>' . substr(strval($bitrate), 0, 3) . '</bitrate>
	        <frequency>' . $frequency . '</frequency>
	    </fileInfoPG>
	    <customTagsPG><![CDATA[' . $customTags . ']]></customTagsPG>
	</episode>
</PodcastGenerator>';
    file_put_contents($targetfile_without_ext . '.xml', $episodefeed);
    // Write image if set
    if (isset($fileinfo["comments"]["picture"])) {
        $imgext = ($fileinfo["comments"]["picture"][0]["image_mime"] == "image/png") ? 'png' : 'jpg';
        $img_filename = $config["absoluteurl"] . $config["img_dir"] . pathinfo($targetfile, PATHINFO_FILENAME) . '.' . $imgext;
        file_put_contents($img_filename, $fileinfo["comments"]["picture"][0]["data"]);
    }
    generateRSS();
    pingServices();
    $success = true;

    error:
}

$categories = simplexml_load_file('../categories.xml');

if (!isset($customTags)) {
    $customTags = '';
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Upload Episode') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $config['url'] ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?php _('Upload Episode'); ?></h1>
        <?php if (isset($success)) { ?>
            <strong><p style="color: #2ecc71;"><?= htmlspecialchars($_POST['title']) . ' ' . _('uploaded successfully') ?></p></strong>
        <?php } ?>
        <?php if (isset($error)) { ?>
            <strong><p style="color: #e74c3c;"><?= $error ?></p></strong>
        <?php } ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h3><?= _('Main Informations') ?></h3>
                    <hr>
                    <div class="form-group">
                        <?= _('File') ?>*:<br>
                        <input type="file" name="file" required><br>
                    </div>
                    <div class="form-group">
                        <?= _('Title') ?>*:<br>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <?= _('Short Description') ?>*:<br>
                        <input type="text" id="shortdesc" name="shortdesc" class="form-control" maxlength="255" oninput="shortDescCheck()" required>
                        <i id="shortdesc_counter">255 <?= _('characters remaining') ?></i>
                    </div>
                    <div class="form-group" style="display: <?= ($config['categoriesenabled'] != 'yes') ? 'none' : 'block' ?>">
                        <?= _('Category') ?>:<br>
                        <small><?= _('You can select up to 3 categories') ?></small><br>
                        <select name="category[ ]" multiple>
                            <?php foreach ($categories as $item) { ?>
                                <option value="<?= htmlspecialchars($item->id) ?>"><?= htmlspecialchars($item->description) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?= _('Publication Date') ?>:<br>
                        <small><?= _('If you select a date in the future, it will be published then') ?></small><br>
                        <?= _('Date') ?>*:<br>
                        <input name="date" type="date" value="<?= date("Y-m-d") ?>" required><br>
                        <?= _('Time') ?>*:<br>
                        <input name="time" type="time" value="<?= date("H:i") ?>" required><br>
                    </div>
                </div>
                <div class="col-6">
                    <h3><?= _('Extra Informations') ?></h3>
                    <hr>
                    <div class="form-group">
                        <?= _('Long Description') ?>:<br>
                        <textarea name="longdesc"></textarea><br>
                    </div>
                    <div class="form-group">
                        <?= _('Episode Number') ?>:<br>
                        <input type="text" name="episodenum" pattern="[0-9]*" class="form-control"><br>
                    </div>
                    <div class="form-group">
                        <?= _('Season Number') ?>:<br>
                        <input type="text" name="seasonnum" pattern="[0-9]*" class="form-control"><br>
                    </div>
                    <div class="form-group">
                        <?= _('iTunes Keywords') ?>:<br>
                        <input type="text" name="itunesKeywords" placeholder="Keyword1, Keyword2 (max 12)" class="form-control"><br>
                    </div>
                    <div class="form-group">
                        <?= _('Explicit content') ?>:<br>
                        <label><input type="radio" value="yes" name="explicit"> <?= _('Yes') ?></label>
                        <label><input type="radio" value="no" name="explicit" checked> <?= _('No') ?></label><br>
                    </div>
                    <div class="form-group">
                        <?= _('Author') ?>*:<br>
                        <input type="text" class="form-control" name="authorname" placeholder="<?= htmlspecialchars($config["author_name"]) ?>"><br>
                        <input type="email" class="form-control" name="authoremail" placeholder="<?= htmlspecialchars($config["author_email"]) ?>"><br>
                    </div>
                    <div class="form-group" style="display: <?= ($config['customtagsenabled'] != 'yes') ? 'none' : 'block' ?>">
                        <?= _('Custom Tags') ?><br>
                        <textarea name="customtags"><?= htmlspecialchars($customTags) ?></textarea><br>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 offset-6">
                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-success btn-lg" value="<?= _('Upload episode') ?>">
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        function shortDescCheck() {
            let shortdesc = document.getElementById("shortdesc").value;
            let maxlength = 255;
            let remaining = maxlength - shortdesc.length;
            let counter
                = document.getElementById("shortdesc_counter").innerText
                = "<?= _('%d characters remaining') ?>".replace('%d', remaining);
        }
    </script>
</body>

</html>
