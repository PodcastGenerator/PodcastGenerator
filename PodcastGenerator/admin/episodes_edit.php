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

if (!isset($_GET['name'])) {
    die(_('No name given'));
}
if (!file_exists('../' . $config['upload_dir'] . $_GET['name'])) {
    die(_('Episode does not exist'));
}

// Delete episode
if (isset($_GET['delete'])) {
    // Delete the audio file
    unlink('../' . $config['upload_dir'] . $_GET['name']);
    // Delete the XML file
    unlink('../' . $config['upload_dir'] . pathinfo('../' . $config['upload_dir'] . $_GET['name'], PATHINFO_FILENAME) . '.xml');
    // Delete the image file if it exists
    if(file_exists('../' . $config['img_dir'] . pathinfo('../' . $config['upload_dir'] . $_GET['name'], PATHINFO_FILENAME) . '.jpg') ||
    file_exists('../' . $config['img_dir'] . pathinfo('../' . $config['upload_dir'] . $_GET['name'], PATHINFO_FILENAME) . '.png'))
    {
        unlink('../' . $config['img_dir'] . pathinfo('../' . $config['upload_dir'] . $_GET['name'], PATHINFO_FILENAME) . '.jpg');
        unlink('../' . $config['img_dir'] . pathinfo('../' . $config['upload_dir'] . $_GET['name'], PATHINFO_FILENAME) . '.png');
    }
    generateRSS();
    header('Location: '.$config['url'].$config['indexfile']);
    die();
}

// Edit episode
if (sizeof($_POST) > 0) {
    // CHeck if all fields are set
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

    // If no categories were selected, add the 'uncategorized'
    // category.  Otherwise, ensure that no more than three categories
    // were actually selected.
    if (sizeof($_POST['category']) == 0) {
        $_POST['category'] = array();
        array_push($_POST['category'], 'uncategorized');
    } else if (sizeof($_POST['category']) > 3) {
        $error = _('Too many categories selected (max: 3)');
        goto error;
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

    $targetfile = '../' . $config['upload_dir'] . $_GET['name'];

    // Get datetime
    $datetime = strtotime($_POST["date"] . ' ' . $_POST['time']);
    // Set file date to this date
    touch($targetfile, $datetime);

    // Get audio metadata (duration, bitrate etc)
    require_once '../components/getid3/getid3.php';
    $getID3 = new getID3;
    $fileinfo = $getID3->analyze($targetfile);
    $duration = $fileinfo["playtime_string"];           // Get duration
    $bitrate = $fileinfo["audio"]["bitrate"];           // Get bitrate
    $frequency = $fileinfo["audio"]["sample_rate"];     // Frequency

    // Automatically fill an empty long description with the contents
    // of the short description.
    $long_desc = empty($_POST['longdesc']) ? $_POST['shortdesc'] : $_POST['longdesc'];
        
    // Go and actually generate the episode
    // It easier to not dynamically generate the file
    $episodefeed = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
	<episode>
	    <titlePG><![CDATA[' . htmlspecialchars($_POST['title'], ENT_NOQUOTES) . ']]></titlePG>
	    <shortdescPG><![CDATA[' . htmlspecialchars($_POST['shortdesc']) . ']]></shortdescPG>
	    <longdescPG><![CDATA[' . htmlspecialchars($long_desc) . ']]></longdescPG>
	    <imgPG></imgPG>
	    <categoriesPG>
	        <category1PG>' . htmlspecialchars($_POST['category'][0]) . '</category1PG>
	        <category2PG>' . htmlspecialchars($_POST['category'][1]) . '</category2PG>
	        <category3PG>' . htmlspecialchars($_POST['category'][2]) . '</category3PG>
	    </categoriesPG>
	    <keywordsPG><![CDATA[' . htmlspecialchars($_POST['keywords']) . ']]></keywordsPG>
	    <explicitPG>' . $_POST['explicit'] . '</explicitPG>
	    <authorPG>
	        <namePG>' . htmlspecialchars($_POST['authorname']) . '</namePG>
	        <emailPG>' . htmlspecialchars($_POST['authoremail']) . '</emailPG>
	    </authorPG>
	    <fileInfoPG>
	        <size>' . intval(filesize($targetfile) / 1000 / 1000) . '</size>
	        <duration>' . $duration . '</duration>
	        <bitrate>' . substr(strval($bitrate), 0, 3) . '</bitrate>
	        <frequency>' . $frequency . '</frequency>
	    </fileInfoPG>
	</episode>
</PodcastGenerator>';
    file_put_contents('../' . $config['upload_dir'] . pathinfo($targetfile, PATHINFO_FILENAME) . '.xml', $episodefeed);
    generateRSS();
    // Redirect if success
    header('Location: ' . $config['url'] . $config['indexfile'] . $config['link'] . $_GET['name'] . '');
    die();

    error: echo ("");
}
// Get episode data
$episode = simplexml_load_file('../' . $config['upload_dir'] . pathinfo('../' . $config['upload_dir'] . $_GET['name'], PATHINFO_FILENAME) . '.xml');
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']) . ' - ' . _('Edit Episode'); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>

<body>
    <?php
    include "js.php";
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <h3><?php echo _('Edit Episode'); ?></h3>
        <?php
        if (isset($error)) {
            echo '<p style="color: red;"><strong>' . $error . '</strong></p>';
        } ?>
        <form action="episodes_edit.php?name=<?php echo htmlspecialchars($_GET["name"]); ?>" method="POST">
            <div class="row">
                <div class="col-6">
                    <h3><?php echo _('Main Information'); ?></h3>
                    <hr>
                    <div class="form-group">
                        <?php echo _('Title'); ?>*:<br>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($episode->episode->titlePG); ?>" required>
                    </div>
                    <div class="form-group">
                        <?php echo _('Short Description'); ?>*:<br>
                        <input type="text" id="shortdesc" name="shortdesc" class="form-control" value="<?php echo htmlspecialchars($episode->episode->shortdescPG); ?>" maxlength="255" oninput="shortDescCheck()" required>
                        <i id="shortdesc_counter">255<?php echo _(' characters remaining'); ?></i>
                    </div>
                    <div class="form-group" style="display: <?php echo ($config['categoriesenabled'] != 'yes') ? 'none' : 'block'; ?>">
                        <?php echo _('Category'); ?>:<br>
                        <small><?php echo _('You can select up to 3 categories'); ?></small><br>
                        <select name="category[ ]" multiple>
                            <?php
                            $categories = simplexml_load_file("../categories.xml");
                            // Fill in selected categories
                            $selected_cats = array(strval($episode->episode->categoriesPG->category1PG), strval($episode->episode->categoriesPG->category2PG), strval($episode->episode->categoriesPG->category3PG));
                            foreach ($categories as $item) {
                                if (in_array($item->id, $selected_cats)) {
                                    echo "<option value=\"" . htmlspecialchars($item->id) . "\" selected>" . htmlspecialchars($item->description) . "</option>";
                                } else {
                                    echo "<option value=\"" . htmlspecialchars($item->id) . "\">" . htmlspecialchars($item->description) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?php echo _('Publication Date'); ?>:<br>
                        <small><?php echo _('If you select a date in the future, it will be published then'); ?></small><br>
                        <?php echo _('Date'); ?>*:<br>
                        <input name="date" type="date" value="<?php echo date('Y-m-d', filemtime('../' . $config['upload_dir'] . $_GET['name'])); ?>" required><br>
                        <?php echo _('Time'); ?>*:<br>
                        <input name="time" type="time" value="<?php echo date('H:i', filemtime('../' . $config['upload_dir'] . $_GET['name'])); ?>" required><br>
                    </div>
                </div>
                <div class="col-6">
                    <h3><?php echo _('Extra Information'); ?></h3>
                    <hr>
                    <div class="form-group">
                        <?php echo _('Long Description'); ?>:<br>
                        <textarea name="longdesc"><?php echo htmlspecialchars($episode->episode->longdescPG); ?></textarea><br>
                    </div>
                    <div class="form-group">
                        <?php echo _('iTunes Keywords'); ?>:<br>
                        <input type="text" name="itunesKeywords" value="<?php echo htmlspecialchars($episode->episode->keywordsPG); ?>" placeholder="Keyword1, Keyword2 (max 12)" class="form-control"><br>
                    </div>
                    <div class="form-group">
                        <?php echo _('Explicit Content'); ?>:<br>
                        <input type="radio" value="yes" name="explicit" <?php if($episode->episode->explicitPG == 'yes') { echo 'checked'; } ?>> Yes <input type="radio" value="no" name="explicit" <?php if($episode->episode->explicitPG == 'no') { echo 'checked'; } ?>> No<br>
                    </div>
                    <div class="form-group">
                        <?php echo _('Author'); ?>*:<br>
                        <input type="text" class="form-control" name="authorname" placeholder="Author Name" value="<?php echo htmlspecialchars($episode->episode->authorPG->namePG); ?>"><br>
                        <input type="email" class="form-control" name="authoremail" placeholder="Author E-Mail" value="<?php echo htmlspecialchars($episode->episode->authorPG->emailPG); ?>"><br>
                    </div>
                    <input type="submit" class="btn btn-success btn-lg" value="<?php echo _('Save Changes'); ?>">
                </div>
            </div>
        </form>
        <hr>
        <h3><?php echo _('Delete Episode'); ?></h3>
        <a href="episodes_edit.php?name=<?php echo htmlspecialchars($_GET['name']); ?>&delete=1" class="btn btn-danger">Delete</a>
    </div>
    <script type="text/javascript">
        function shortDescCheck() {
            let shortdesc = document.getElementById("shortdesc").value;
            let maxlength = 255;
            let counter = document.getElementById("shortdesc_counter").innerText = (maxlength - shortdesc.length) + <?php echo _('" characters remaining"'); ?>;
        }
        shortDescCheck();
    </script>
</body>

</html>
