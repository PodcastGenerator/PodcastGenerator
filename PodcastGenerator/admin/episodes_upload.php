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

if (sizeof($_POST) > 0) {
    // CHeck if all fields are set (except "category")
    $req_fields = [
        $_POST['title'],
        $_POST['shortdesc'],
        $_POST['date'],
        $_POST['time'],
        $_POST['explicit']
    ];
    // Check if fields are missing
    for ($i = 0; $i < sizeof($req_fields); $i++) {
        if (empty($req_fields[$i])) {
            $error = _('Missing fields');
            goto error;
        }
    }

    // If no categories were selected, add the 'uncategorized'
    // category.  Otherwise, ensure that no more than three categories
    // were actually selected.
    if (sizeof((array)$_POST['category']) == 0) {
        $_POST['category'] = array();
        array_push($_POST['category'], 'uncategorized');
    } else if (sizeof((array)$_POST['category']) > 3) {
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

    if (strlen($_POST['shortdesc']) > 255) {
        $error = _("Size of the 'Short Description' exceeded");
        goto error;
    }

    // Skip files if they are not strictly named
    if ($config['strictfilenamepolicy'] == 'yes') {
        if (!preg_match('/^[\w.]+$/', basename($_FILES['file']['name']))) {
            $error = _('Invalid filename, only A-Z, a-z, underscores and dots are permitted');
            goto error;
        }
    }

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
        $error = sprintf(_('Unsupported mime type detected for file with extension "%s"'), $fileextension);
        // Delete the file if the mime type is invalid
        unlink($targetfile);
        goto error;
    }

    // Get datetime
    $datetime = strtotime($_POST['date'] . ' ' . $_POST['time']);
    // Set file date to this date
    touch($targetfile, $datetime);

    // Get audio metadata (duration, bitrate etc)
    require_once '../components/getid3/getid3.php';
    $getID3 = new getID3;
    $fileinfo = $getID3->analyze($targetfile);
    $duration = $fileinfo['playtime_string'];           // Get duration
    $bitrate = $fileinfo['audio']['bitrate'];           // Get bitrate
    $frequency = $fileinfo['audio']['sample_rate'];     // Frequency

    // Go and actually generate the episode
    // It easier to not dynamically generate the file
    $episodefeed = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
	<episode>
	    <titlePG><![CDATA[' . htmlspecialchars($_POST['title'], ENT_NOQUOTES) . ']]></titlePG>
	    <shortdescPG><![CDATA[' . htmlspecialchars($_POST['shortdesc']) . ']]></shortdescPG>
	    <longdescPG><![CDATA[' . htmlspecialchars($_POST['longdesc']) . ']]></longdescPG>
	    <imgPG></imgPG>
	    <categoriesPG>
	        <category1PG>' . htmlspecialchars($_POST['category'][0]) . '</category1PG>
	        <category2PG>' . htmlspecialchars($_POST['category'][1]) . '</category2PG>
	        <category3PG>' . htmlspecialchars($_POST['category'][2]) . '</category3PG>
	    </categoriesPG>
	    <keywordsPG><![CDATA[' . htmlspecialchars($_POST['itunesKeywords']) . ']]></keywordsPG>
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
    $success = true;

    error: echo ('');
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Upload Episode'); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?php _('Upload Episode'); ?></h1>
        <?php
        if (isset($success)) {
            echo '<strong><p style="color: #2ecc71;">' . htmlspecialchars($_POST['title']) . ' ' . _('uploaded successfully') . '</p></strong>';
        }
        if (isset($error)) {
            echo '<strong><p style="color: #e74c3c;">' . $error . '</p></strong>';
        }
        ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h3><?php echo _('Main Informations'); ?></h3>
                    <hr>
                    <div class="form-group">
                        <?php echo _('File'); ?>*:<br>
                        <input type="file" name="file" required><br>
                    </div>
                    <div class="form-group">
                        <?php echo _('Title'); ?>*:<br>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <?php echo _('Short Description'); ?>*:<br>
                        <input type="text" id="shortdesc" name="shortdesc" class="form-control" maxlength="255" oninput="shortDescCheck()" required>
                        <i id="shortdesc_counter">255 <?php echo _('characters remaining'); ?></i>
                    </div>
                    <div class="form-group" style="display: <?php echo ($config['categoriesenabled'] != 'yes') ? 'none' : 'block'; ?>">
                        <?php echo _('Category'); ?>:<br>
                        <small><?php echo _('You can select up to 3 categories'); ?></small><br>
                        <select name="category[ ]" multiple>
                            <?php
                            $categories = simplexml_load_file('../categories.xml');
                            foreach ($categories as $item) {
                                echo '<option value="' . htmlspecialchars($item->id) . '">' . htmlspecialchars($item->description) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?php echo _('Publication Date'); ?>:<br>
                        <small><?php echo _('If you select a date in the future, it will be published then'); ?></small><br>
                        <?php echo _('Date'); ?>*:<br>
                        <input name="date" type="date" value="<?php echo date("Y-m-d"); ?>" required><br>
                        <?php echo _('Time'); ?>*:<br>
                        <input name="time" type="time" value="<?php echo date("H:i"); ?>" required><br>
                    </div>
                </div>
                <div class="col-6">
                    <h3><?php echo _('Extra Informations'); ?></h3>
                    <hr>
                    <div class="form-group">
                        <?php echo _('Long Description'); ?>:<br>
                        <textarea name="longdesc"></textarea><br>
                    </div>
                    <div class="form-group">
                        <?php echo _('iTunes Keywords'); ?>:<br>
                        <input type="text" name="itunesKeywords" placeholder="Keyword1, Keyword2 (max 12)" class="form-control"><br>
                    </div>
                    <div class="form-group">
                        <?php echo _('Explicit content'); ?>:<br>
                        <input type="radio" value="yes" name="explicit"> <?php echo _('Yes'); ?> <input type="radio" value="no" name="explicit" checked> <?php echo _('No'); ?><br>
                    </div>
                    <div class="form-group">
                        <?php echo _('Author'); ?>*:<br>
                        <input type="text" class="form-control" name="authorname" placeholder="<?php echo htmlspecialchars($config["author_name"]); ?>"><br>
                        <input type="email" class="form-control" name="authoremail" placeholder="<?php echo htmlspecialchars($config["author_email"]); ?>"><br>
                    </div>
                    <input type="submit" class="btn btn-success btn-lg" value="<?php echo _('Upload episode'); ?>">
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        function shortDescCheck() {
            let shortdesc = document.getElementById("shortdesc").value;
            let maxlength = 255;
            let counter = document.getElementById("shortdesc_counter").innerText = (maxlength - shortdesc.length) + " " + <?php echo '"' . _('characters remaining') . '"' ?>;
        }
    </script>
</body>

</html>
