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

    deleteEpisode($targetfile, $config);
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
    } elseif (count($_POST['category']) > 3) {
        $error = _('Too many categories selected (max: 3)');
        goto error;
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

    // Get episode data
    $episode = simplexml_load_file($targetfile_without_ext . '.xml');

    // Determine current cover art file
    $currentCoverFile = $episode->episode->imgPG->attributes()['path'] ?? '';
    $currentCoverUrl = (string) $episode->episode->imgPG;
    if (empty($currentCoverFile)) {
        // Look for old-style cover image
        $currentCoverFile = pathinfo($targetfile, PATHINFO_FILENAME) . '.jpg';
        if (!file_exists($imagesDir . $currentCoverFile)) {
            $currentCoverFile = pathinfo($targetfile, PATHINFO_FILENAME) . '.png';
            if (!file_exists($imagesDir . $currentCoverFile)) {
                $currentCoverFile = '';
            }
        }
    }

    // Build array of previous cover art files
    $previousCoverFiles = array();
    if (isset($coverImg->episode->previousImgsPG)) {
        foreach ($coverImg->episode->previousImgsPG->children() as $prevImg) {
            $previousCoverFiles[] = $imagesDir . $prevImg;
        }
    }

    $coverfile = '';
    if (!empty($_FILES['episodecover']['name'])) {
        $coverfile = basename($_FILES['episodecover']['name']);
        $episodecoverfile = makeEpisodeFilename($imagesDir, $_POST['date'], $coverfile);

        $validTypes = simplexml_load_file('../components/supported_media/supported_media.xml');

        $coverfileextension = pathinfo($episodecoverfile, PATHINFO_EXTENSION);
        $validCoverFileExt = false;
        foreach ($validTypes->mediaFile as $item) {
            if ($coverfileextension == $item->extension) {
                $validCoverFileExt = true;
                break;
            }
        }
        if (!$validCoverFileExt) {
            $error = sprintf(_('%s has invalid file extension'), $coverfile);
            goto error;
        }

        if (!move_uploaded_file($_FILES['episodecover']['tmp_name'], $episodecoverfile)) {
            $error = sprintf(_('%s was not uploaded successfully'), $coverfile);
            goto error;
        }

        $covermimetype = getmime($episodecoverfile);
        if (!$covermimetype) {
            $error = _('The uploaded cover art file is not readable (permission error)');
            goto error;
        }
        $validCoverMimeType = false;
        foreach ($validTypes->mediaFile as $item) {
            if (strpos($item->mimetype, 'image/') !== 0) {
                continue; // skip non-image MIME types
            }
            if ($covermimetype == $item->mimetype) {
                $validCoverMimeType = true;
                break;
            }
        }

        if (!$validCoverMimeType) {
            $error = sprintf(_('%s has unsupported MIME content type %s'), $coverfile, $mimetype);
            // Delete the file if the mime type is invalid
            unlink($episodecoverfile);
            goto error;
        }
    }

    // Newer cover art files go on top of the list
    if (!empty($episodecoverfile) && $episodecoverfile != $currentCoverFile) {
        array_unshift($previousCoverFiles, $currentCoverFile);
        $currentCoverFile = $episodecoverfile;
        $currentCoverUrl = $config['url'] . $config['img_dir'] . basename($episodecoverfile);
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
	    <imgPG path="' . htmlspecialchars($currentCoverFile) . '">' . htmlspecialchars($currentCoverUrl) . '</imgPG>
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
	    <customTagsPG><![CDATA[' . $customTags . ']]></customTagsPG>' . "\n";

    if (!empty($previousCoverFiles)) {
        $episodefeed .= "\t\t" . '<previousImgsPG>' . "\n";
        foreach ($previousCoverFiles as $img) {
            $episodefeed .= "\t\t\t" . '<imgPG>' . $img . '</imgPG>' . "\n";
        }
        $episodefeed .= "\t\t" . '</previousImgsPG>' . "\n";
    }

    $episodefeed .= '	</episode>' . "\n" . '</PodcastGenerator>';
    file_put_contents($uploadDir . pathinfo($targetfile, PATHINFO_FILENAME) . '.xml', $episodefeed);

    generateRSS();
    pingServices();

    // Redirect if success
    header('Location: ' . $config['url'] . $config['indexfile'] . $config['link'] . $_GET['name'] . '');
    die();

    error:
}

// Get episode data
$episode = simplexml_load_file($targetfile_without_ext . '.xml');
$filemtime = filemtime($targetfile);

$coverart = (string) $episode->episode->imgPG;
if (empty($coverart)) {
    // check for old style cover art files
    if (file_exists($imagesDir . pathinfo($targetfile, PATHINFO_FILENAME) . '.jpg')) {
        $coverart = pathinfo($targetfile, PATHINFO_FILENAME) . '.jpg';
    } elseif (file_exists($imagesDir . pathinfo($targetfile, PATHINFO_FILENAME) . '.png')) {
        $coverart = pathinfo($targetfile, PATHINFO_FILENAME) . '.png';
    } else {
        // default to the podcast cover art, if no episode art exists
        $coverart = $config['podcast_cover'] ?? 'itunes_image.jpg';
    }
    $coverart = $config['url'] . $config['img_dir'] . $coverart;
}

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
    <style>
        label.req::after { content: "*"; color: red; }
    </style>
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
        <form action="episodes_edit.php?name=<?= htmlspecialchars($_GET["name"]) ?>"
              method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h4><?= _('Main Information') ?></h4>
                    <hr>
                    <input type="hidden" name="guid" value="<?= htmlspecialchars($episode->episode->guid) ?>">
                    <div class="form-group">
                        <label for="title" class="req"><?= _('Title') ?>:</label><br>
                        <input type="text" id="title" name="title" class="form-control"
                               value="<?= htmlspecialchars($episode->episode->titlePG) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="shortdesc" class="req"><?= _('Short Description') ?>:</label><br>
                        <input type="text" id="shortdesc" name="shortdesc" class="form-control"
                               value="<?= htmlspecialchars($episode->episode->shortdescPG) ?>"
                               maxlength="255" oninput="shortDescCheck()" required>
                        <i id="shortdesc_counter"><?= sprintf(_('Characters remaining: %d'), 255) ?></i>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['categoriesenabled']) ?>">
                        <label for="categories"><?= _('Category') ?>:</label><br>
                        <small><?= _('You can select up to 3 categories') ?></small><br>
                        <select name="category[ ]" id="categories" multiple>
                            <?php foreach ($categories as $item) { ?>
                                <?php if (in_array($item->id, $selected_cats)) { ?>
                                    <option value="<?= htmlspecialchars($item->id) ?>" selected>
                                        <?= htmlspecialchars($item->description) ?>
                                    </option>
                                <?php } else { ?>
                                    <option value="<?= htmlspecialchars($item->id) ?>">
                                        <?= htmlspecialchars($item->description) ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?= _('Publication Date') ?>:<br>
                        <small><?= _('If you select a date in the future, it will be published then') ?></small><br>
                        <label for="date" class="req"><?= _('Date') ?>:</label><br>
                        <input name="date" id="date" type="date" value="<?= date('Y-m-d', $filemtime) ?>" required>
                        <br>
                        <label for="time" class="req"><?= _('Time') ?>:</label><br>
                        <input name="time" id="time" type="time" value="<?= date('H:i', $filemtime) ?>" required>
                        <br>
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
                        <label for="episodecover"><?= _('Upload new cover') ?>:</label><br>
                        <input type="file" id="episodecover" name="episodecover"><br>
                    </div>
                    <div class="form-group">
                        <label for="longdesc"><?= _('Long Description') ?>:</label><br>
                        <textarea id="longdesc" name="longdesc"
                                class="form-control"><?= htmlspecialchars($episode->episode->longdescPG) ?></textarea>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="episodenum"><?= _('Episode Number') ?>:</label><br>
                        <input type="text" id="episodenum" name="episodenum" pattern="[0-9]*" class="form-control"
                               value="<?= htmlspecialchars($episode->episode->episodeNumPG) ?>">
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="seasonnum"><?= _('Season Number') ?>:</label><br>
                        <input type="text" id="seasonnum" name="seasonnum" pattern="[0-9]*" class="form-control"
                               value="<?= htmlspecialchars($episode->episode->seasonNumPG) ?>">
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="itunesKeywords"><?= _('iTunes Keywords') ?>:</label><br>
                        <input type="text" id="itunesKeywords" name="itunesKeywords"
                               value="<?= htmlspecialchars($episode->episode->keywordsPG) ?>"
                               placeholder="Keyword1, Keyword2 (max 12)" class="form-control">
                        <br>
                    </div>
                    <div class="form-group">
                        <?= _('Explicit Content') ?>:<br>
                        <label>
                            <input type="radio" name="explicit" <?= checkedAttr($episode->episode->explicitPG, 'yes') ?>
                                   value="yes">
                            <?= _('Yes') ?>
                        </label>
                        <label>
                            <input type="radio" name="explicit" <?= checkedAttr($episode->episode->explicitPG, 'no') ?>
                                   value="no">
                            <?= _('No') ?>
                        </label>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="authorname"><?= _('Author') ?>:</label><br>
                        <input type="text" id="authorname" name="authorname" class="form-control"
                               placeholder="Author Name"
                               value="<?= htmlspecialchars($episode->episode->authorPG->namePG) ?>">
                        <br>
                        <input type="email" id="authoremail" name="authoremail" class="form-control"
                               placeholder="Author E-Mail"
                               value="<?= htmlspecialchars($episode->episode->authorPG->emailPG) ?>">
                        <br>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['customtagsenabled']) ?>">
                        <label for="customtags"><?= _('Custom Tags') ?>:</label><br>
                        <textarea id="customtags" name="customtags"
                                class="form-control"><?= htmlspecialchars($episode->episode->customTagsPG) ?></textarea>
                        <br>
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
        <h4><?= _('Delete Episode') ?></h4>
        <form action="episodes_edit.php?name=<?= htmlspecialchars($_GET['name']) ?>&delete=1" method="POST">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" class="btn btn-danger" value="<?= _('Delete') ?>">
        </form>
    </div>
    <script type="text/javascript">
        function shortDescCheck() {
            let shortdesc = document.getElementById("shortdesc").value;
            let maxlength = 255;
            let remaining = maxlength - shortdesc.length;
            let counter
                = document.getElementById("shortdesc_counter").innerText
                = "<?= _('Characters remaining: %d') ?>".replace('%d', remaining);
        }
        shortDescCheck();
    </script>
</body>

</html>
