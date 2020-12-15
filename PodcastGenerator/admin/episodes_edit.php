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

checkPath($_GET['name']);

$targetfile = $config['absoluteurl'] . $config['upload_dir'] . $_GET['name'];
$targetfile_without_ext = $config['absoluteurl'] . $config['upload_dir'] . pathinfo($targetfile, PATHINFO_FILENAME);

if (!file_exists($targetfile)) {
    die(_('Episode does not exist'));
}

// Delete episode
if (isset($_GET['delete'])) {
    checkToken();
    // Delete the audio file
    unlink($targetfile);
    // Delete the XML file
    unlink($targetfile_without_ext . '.xml');
    // Delete the image file if it exists
    if (
        file_exists($config['absoluteurl'] . $config['img_dir'] . pathinfo($targetfile, PATHINFO_FILENAME) . '.jpg')
        || file_exists($config['absoluteurl'] . $config['img_dir'] . pathinfo($targetfile, PATHINFO_FILENAME) . '.png')
    ) {
        unlink($config['absoluteurl'] . $config['img_dir'] . pathinfo($targetfile, PATHINFO_FILENAME) . '.jpg');
        unlink($config['absoluteurl'] . $config['img_dir'] . pathinfo($targetfile, PATHINFO_FILENAME) . '.png');
    }
    generateRSS();
    pingServices();
    header('Location: ' . $config['url'] . $config['indexfile']);
    die();
}

// Edit episode
if (count($_POST) > 0) {
    checkToken();
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
    } elseif (count($_POST['category']) > 3) {
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

    $link = str_replace('?', '', $config['link']);
    $link = str_replace('=', '', $link);
    $link = str_replace('$url', '', $link);

    // build categories list from post data
    $categories = array();
    for ($i = 0; $i < 3; $i++) {
        $categories[$i] = isset($_POST['category'][$i])
            ? $_POST['category'][$i]
            : ($i == 0 ? 'uncategorized' : '');
    }

    // Get datetime
    $datetime = strtotime($_POST["date"] . ' ' . $_POST['time']);
    // Set file date to this date
    touch($targetfile, $datetime);

    // Get audio metadata (duration, bitrate etc)
    $fileinfo = getID3Info($targetfile);
    $duration = $fileinfo["playtime_string"];           // Get duration
    $bitrate = $fileinfo["audio"]["bitrate"];           // Get bitrate
    $frequency = $fileinfo["audio"]["sample_rate"];     // Frequency

    // Automatically fill an empty long description with the contents
    // of the short description.
    $long_desc = empty($_POST['longdesc']) ? $_POST['shortdesc'] : $_POST['longdesc'];

    // Regenerate GUID if it is missing from POST data
    $guid = empty($_POST['guid']) ? $config['url'] . "?" . $link . "=" . $_GET['name'] : $_POST['guid'];

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

    // Go and actually generate the episode
    // It easier to not dynamically generate the file
    $episodefeed = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
	<episode>
	    <guid>' . htmlspecialchars($guid) . '</guid>
	    <titlePG>' . htmlspecialchars($_POST['title'], ENT_NOQUOTES) . '</titlePG>
	    <episodeNumPG>' . $_POST['episodenum'] . '</episodeNumPG>
	    <seasonNumPG>' . $_POST['seasonnum'] . '</seasonNumPG>
	    <shortdescPG><![CDATA[' . $_POST['shortdesc'] . ']]></shortdescPG>
	    <longdescPG><![CDATA[' . $long_desc . ']]></longdescPG>
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
	        <size>' . intval(filesize($targetfile) / 1000 / 1000) . '</size>
	        <duration>' . $duration . '</duration>
	        <bitrate>' . substr(strval($bitrate), 0, 3) . '</bitrate>
	        <frequency>' . $frequency . '</frequency>
	    </fileInfoPG>
	    <customTagsPG><![CDATA[' . $customTags . ']]></customTagsPG>
	</episode>
</PodcastGenerator>';
    file_put_contents($config['absoluteurl'] . $config['upload_dir'] . pathinfo($targetfile, PATHINFO_FILENAME) . '.xml', $episodefeed);
    generateRSS();
    pingServices();
    // Redirect if success
    header('Location: ' . $config['url'] . $config['indexfile'] . $config['link'] . $_GET['name'] . '');
    die();

    error:
}
// Get episode data
$episode = simplexml_load_file($targetfile_without_ext . '.xml');
// Fill in selected categories
$categories = simplexml_load_file("../categories.xml");
$selected_cats = array(
    strval($episode->episode->categoriesPG->category1PG),
    strval($episode->episode->categoriesPG->category2PG),
    strval($episode->episode->categoriesPG->category3PG)
);
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']) . ' - ' . _('Edit Episode') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $config['url'] ?>favicon.ico">
</head>

<body>
    <?php
    include "js.php";
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <h3><?= _('Edit Episode') ?></h3>
        <?php if (isset($error)) { ?>
            <p style="color: red;"><strong><?= $error ?></strong></p>
        <?php } ?>
        <form action="episodes_edit.php?name=<?= htmlspecialchars($_GET["name"]) ?>" method="POST">
            <div class="row">
                <div class="col-6">
                    <h3><?= _('Main Information') ?></h3>
                    <hr>
                    <input type="hidden" name="guid" value="<?= htmlspecialchars($episode->episode->guid) ?>">
                    <div class="form-group">
                        <?= _('Title') ?>*:<br>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($episode->episode->titlePG) ?>" required>
                    </div>
                    <div class="form-group">
                        <?= _('Short Description') ?>*:<br>
                        <input type="text" id="shortdesc" name="shortdesc" class="form-control" value="<?= htmlspecialchars($episode->episode->shortdescPG) ?>" maxlength="255" oninput="shortDescCheck()" required>
                        <i id="shortdesc_counter"><?= sprintf(_('%d characters remaining'), 255) ?></i>
                    </div>
                    <div class="form-group" style="display: <?= ($config['categoriesenabled'] != 'yes') ? 'none' : 'block' ?>">
                        <?= _('Category') ?>:<br>
                        <small><?= _('You can select up to 3 categories') ?></small><br>
                        <select name="category[ ]" multiple>
                            <?php foreach ($categories as $item) { ?>
                                <?php if (in_array($item->id, $selected_cats)) { ?>
                                    <option value="<?= htmlspecialchars($item->id) ?>" selected><?= htmlspecialchars($item->description) ?></option>
                                <?php } else { ?>
                                    <option value="<?= htmlspecialchars($item->id) ?>"><?= htmlspecialchars($item->description) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?= _('Publication Date') ?>:<br>
                        <small><?= _('If you select a date in the future, it will be published then') ?></small><br>
                        <?= _('Date') ?>*:<br>
                        <input name="date" type="date" value="<?= date('Y-m-d', filemtime($targetfile)) ?>" required><br>
                        <?= _('Time') ?>*:<br>
                        <input name="time" type="time" value="<?= date('H:i', filemtime($targetfile)) ?>" required><br>
                    </div>
                </div>
                <div class="col-6">
                    <h3><?= _('Extra Information') ?></h3>
                    <hr>
                    <div class="form-group">
                        <?= _('Long Description') ?>:<br>
                        <textarea name="longdesc"><?= htmlspecialchars($episode->episode->longdescPG) ?></textarea><br>
                    </div>
                    <div class="form-group">
                        <?= _('Episode Number') ?>:<br>
                        <input type="text" name="episodenum" pattern="[0-9]*" class="form-control" value="<?= htmlspecialchars($episode->episode->episodeNumPG) ?>"><br>
                    </div>
                    <div class="form-group">
                        <?= _('Season Number') ?>:<br>
                        <input type="text" name="seasonnum" pattern="[0-9]*" class="form-control" value="<?= htmlspecialchars($episode->episode->seasonNumPG) ?>"><br>
                    </div>
                    <div class="form-group">
                        <?= _('iTunes Keywords') ?>:<br>
                        <input type="text" name="itunesKeywords" value="<?= htmlspecialchars($episode->episode->keywordsPG) ?>" placeholder="Keyword1, Keyword2 (max 12)" class="form-control"><br>
                    </div>
                    <div class="form-group">
                        <?= _('Explicit Content') ?>:<br>
                        <label><input type="radio" value="yes" name="explicit" <?= $episode->episode->explicitPG == 'yes' ? 'checked' : '' ?>> <?= _('Yes') ?></label>
                        <label><input type="radio" value="no" name="explicit" <?= $episode->episode->explicitPG == 'no' ? 'checked' : '' ?>> <?= _('No') ?></label><br>
                    </div>
                    <div class="form-group">
                        <?= _('Author') ?>*:<br>
                        <input type="text" class="form-control" name="authorname" placeholder="Author Name" value="<?= htmlspecialchars($episode->episode->authorPG->namePG) ?>"><br>
                        <input type="email" class="form-control" name="authoremail" placeholder="Author E-Mail" value="<?= htmlspecialchars($episode->episode->authorPG->emailPG) ?>"><br>
                    </div>
                    <div class="form-group" style="display: <?= ($config['customtagsenabled'] != 'yes') ? 'none' : 'block' ?>">
                        <?= _('Custom Tags') ?><br>
                        <textarea name="customtags"><?= htmlspecialchars($episode->episode->customTagsPG) ?></textarea><br>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 offset-6">
                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-success btn-lg" value="<?= _('Save Changes') ?>">
                </div>
            </div>
        </form>
        <hr>
        <h3><?= _('Delete Episode') ?></h3>
        <form action="episodes_edit.php?name=<?= htmlspecialchars($_GET['name']) ?>&delete=1" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" class="btn btn-danger" value="<?= _('Delete') ?>">
        </form>
    </div>
    <script type="text/javascript">
        function shortDescCheck() {
            let shortdesc = document.getElementById("shortdesc").value;
            let maxlength = 255;
            let counter = document.getElementById("shortdesc_counter").innerText = (maxlength - shortdesc.length) + <?= _('" characters remaining"') ?>;
        }
        shortDescCheck();
    </script>
</body>

</html>
