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

if (isset($_GET['upload'])) {
    // CHeck if all fields are set (except "category")
    $req_fields = [
        $_POST['title'],
        $_POST['shortdesc'],
        $_POST['date'],
        $_POST['time'],
        $_POST['explicit'],
        $_POST['authorname'],
        $_POST['authoremail']
    ];
    // Check if fields are missing
    for ($i = 0; $i < sizeof($req_fields); $i++) {
        if (empty($req_fields[$i])) {
            $error = _('Missing fields');
            goto error;
        }
    }

    // Check if categories are even enabled and then do uncategorized
    if (strtolower($config['categoriesenabled']) != 'yes') {
        $_POST['category'] = array();
        array_push($_POST['category'], 'uncategorized');
    }

    // Check if the user selected too much episodes
    if (sizeof($_POST['category']) > 3) {
        $error = _('Too much categories selected');
        goto error;
    }

    // CHeck if the user has selected no categories
    if (sizeof($_POST['category']) <= 0) {
        $error = _('No category selected');
        goto error;
    }

    // Fill up empty categories (to avoid warnings)
    for ($i = 0; $i < 3; $i++) {
        if (!isset($_POST['category'][$i])) {
            $_POST['category'][$i] = '';
        }
    }

    // Check author e-mail
    if (isset($_POST['authoremail'])) {
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

    $targetfile = '../' . $config['upload_dir'] . $_POST['date'] . '-' . basename($_FILES['file']['name']);
    if (file_exists($targetfile)) {
        $appendix = 1;
        while (file_exists($targetfile)) {
            $targetfile = '../' . $config['upload_dir'] . $_POST['date'] . '-' . $appendix . '-' . basename($_FILES['file']['name']);
            $appendix++;
        }
    }
    $targetfile_without_ext = '../' . $config['upload_dir'] . pathinfo($targetfile, PATHINFO_FILENAME);
    if ($_FILES['file']['size'] > intval($config['max_upload_form_size'])) {
        $error = _('File is too big, maximum filesize is: ') . round(intval($config["max_upload_form_size"]) / 1000 / 1000, 0);
        goto error;
    }

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

    $validMimeType = false;
    foreach ($validTypes->mediaFile as $item) {
        if (mime_content_type($targetfile) == $item->mimetype) {
            $validMimeType = true;
            break;
        }
    }

    if (!$validMimeType) {
        $error = _('Invalid mime type, are you actually uploading a')  . ' ' . $fileextension;
        // Delete the file if the mime type is invalid
        unlink($targetfile);
        goto error;
    }

    // Get datetime
    $datetime = strtotime($_POST['date'] . ' ' . $_POST['time']);
    // Set file date to this date
    touch($targetfile, $datetime);

    // Get audio metadata (duration, bitrate etc)
    require '../components/getid3/getid3.php';
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
	    <titlePG><![CDATA[' . $_POST['title'] . ']]></titlePG>
	    <shortdescPG><![CDATA[' . $_POST['shortdesc'] . ']]></shortdescPG>
	    <longdescPG><![CDATA[' . $_POST['longdesc'] . ']]></longdescPG>
	    <imgPG></imgPG>
	    <categoriesPG>
	        <category1PG>' . $_POST['category'][0] . '</category1PG>
	        <category2PG>' . $_POST['category'][1] . '</category2PG>
	        <category3PG>' . $_POST['category'][2] . '</category3PG>
	    </categoriesPG>
	    <keywordsPG><![CDATA[' . $_POST['itunesKeywords'] . ']]></keywordsPG>
	    <explicitPG>' . $_POST['explicit'] . '</explicitPG>
	    <authorPG>
	        <namePG>' . $_POST['authorname'] . '</namePG>
	        <emailPG>' . $_POST['authoremail'] . '</emailPG>
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
        <form action="episodes_upload.php?upload=1" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h3><?php echo _('Main Informations'); ?></h3>
                    <hr>
                    <div class="form-group">
                        <?php echo _('File'); ?>*:<br>
                        <input type="file" name="file" required><br>
                        <small><?php echo sprintf('Your server configuration allows you to upload files up to around %s MB. If your file is bigger or you have other problems use the FTP feature', strval(htmlspecialchars(round(intval($config["max_upload_form_size"]) / 1000 / 1000, 0)))); ?></small><br>
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
                    <?php
                    if (strtolower($config['categoriesenabled']) == 'yes') {
                    ?>
                        <div class="form-group">
                            <?php echo _('Category'); ?>*:<br>
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
                    <?php
                    }
                    ?>
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
                        <input type="text" class="form-control" name="authorname" placeholder="<?php echo _('Author Name'); ?>" value="<?php echo htmlspecialchars($config["author_name"]); ?>"><br>
                        <input type="email" class="form-control" name="authoremail" placeholder="<?php echo _('Author E-Mail'); ?>" value="<?php echo htmlspecialchars($config["author_email"]); ?>"><br>
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