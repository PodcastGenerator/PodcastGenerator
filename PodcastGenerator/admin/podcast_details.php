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

$languages = simplexml_load_file('../components/supported_languages/podcast_languages.xml');

if (isset($_GET['edit'])) {
    checkToken();

    if (isset($_POST['podcast_guid']) && !empty($_POST['podcast_guid'])) {
        $guid = $_POST['podcast_guid'];
        if (!preg_match('/^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$/', $guid)) {
            $error = _('Podcast GUID is malformed');
            goto error;
        }
    }

    if (isset($_POST['custom_tags']) && !empty($_POST['custom_tags'])) {
        // need to handle custom_tags specially
        $custom_tags = $_POST['custom_tags'];
        if (isWellFormedXml($custom_tags)) {
            // only set the value if it's well-formed XML
            saveCustomFeedTags($custom_tags);
        } elseif ($config['customtagsenabled'] == 'yes') {
            // only error if custom tags feature is enabled
            $error = _('Custom tags are not well-formed');
            goto error;
        }
    }

    $configChanged = false;
    foreach ($_POST as $key => $value) {
        if ($key == 'custom_tags') {
            continue;
        } else {
            $config[$key] = $value;
            $configChanged = true;
        }
    }

    if ($configChanged) {
        if (!$config->save()) {
            $error = _('Could not save configuration changes');
            goto error;
        }
    }

    if (!isset($error)) {
        // need to reload config before generating feed
        $config->reload();
        generateRSS();
        pingServices();

        header('Location: podcast_details.php');
        die();
    }
}

error:

$custom_tags = getCustomFeedTags();

$lockFeedOptions = array(
    [ 'value' => 'yes', 'label' => _('Locked') ],
    [ 'value' => 'no', 'label' => _('Unlocked') ],
    [ 'value' => '', 'label' => _('Off') ]
);

function selectedLangAttr($lang)
{
    global $config;
    return $config['feed_language'] == $lang ? ' selected' : '';
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Podcast Details') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        .txt {
            width: 100%;
        }
    </style>
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
        <h1><?= _('Change Podcast Details') ?></h1>
        <form action="podcast_details.php?edit=1" method="POST">
            <label for="podcast_title"><?= _('Podcast Title') ?>:</label><br>
            <input type="text" id="podcast_title" name="podcast_title"
                   value="<?= htmlspecialchars($config['podcast_title']) ?>" class="txt">
            <br>

            <label for="podcast_subtitle"><?= _('Podcast Subtitle or Slogan') ?>:</label><br>
            <input type="text" id="podcast_subtitle" name="podcast_subtitle"
                   value="<?= htmlspecialchars($config['podcast_subtitle']) ?>" class="txt">
            <br>

            <label for="podcast_description"><?= _('Podcast Description') ?>:</label><br>
            <input type="text" id="podcast_description" name="podcast_description"
                   value="<?= htmlspecialchars($config['podcast_description']) ?>" class="txt">
            <br>

            <label for="copyright"><?= _('Copyright Notice') ?>:</label><br>
            <input type="text" id="copyright" name="copyright"
                   value="<?= htmlspecialchars($config['copyright']) ?>" class="txt">
            <br>

            <label for="author_name"><?= _('Author Name') ?>:</label><br>
            <input type="text" id="author_name" name="author_name"
                   value="<?= htmlspecialchars($config['author_name']) ?>" class="txt">
            <br>

            <label for="author_email"><?= _('Author E-Mail Address') ?>:</label><br>
            <input type="text" id="author_email" name="author_email"
                   value="<?= htmlspecialchars($config['author_email']) ?>" class="txt">
            <br>

            <label for="podcast_guid"><?= _('Podcast GUID') ?>:</label>
            (<?= _('Unique ID code for your podcast across the internet') ?>)<br>
            <input type="text" id="podcast_guid" name="podcast_guid" class="txt"
                   value="<?= htmlspecialchars($config['podcast_guid']) ?>"
                   pattern="^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$"
                   placeholder="3636e0e4-8dff-4662-90b3-6068af079b97">
            <br>

            <label for="feed_language"><?= _('Feed Language'); ?>:</label>
            (<?= _('Main language of your podcast') ?>)<br>
            <select id="feed_language" name="feed_language">
                <?php foreach ($languages as $lang) { ?>
                    <option value="<?= htmlspecialchars($lang->code) ?>"<?= selectedLangAttr($lang->code) ?>>
                        <?= htmlspecialchars($lang->name) ?>
                    </option>
                <?php } ?>
            </select>
            <br>

            <?= _('Explicit Podcast') ?>:<br>
            <?php htmlOptionRadios('explicit_podcast', $config['explicit_podcast'], $yesNoOptions); ?>
            <br>

            <?= _('Lock Podcast Feed') ?>: (<?= _('Prevent other platforms from importing your feed') ?>)<br>
            <?php htmlOptionRadios('feed_locked', $config['feed_locked'], $lockFeedOptions); ?>
            <br>

<?php if ($config['customtagsenabled'] == 'yes') { ?>
            <label for="custom_tags"><?= _('Custom Feed Tags') ?>:</label><br>
            <textarea id="custom_tags" name="custom_tags" class="txt"><?= htmlspecialchars($custom_tags) ?></textarea>
            <br>
<?php } ?>

            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>
