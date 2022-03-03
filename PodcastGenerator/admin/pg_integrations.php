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

if (isset($_GET['edit'])) {
    foreach ($_POST as $key => $value) {
        if ($key[0] != '_') {
            updateConfig('../config.php', $key, $value);
        } else if ($key == '_register') {
            $register = $value;
        }
    }

    if (isset($register)) {
        switch ($register) {
            case 'podcast-index':
                if ($config['pi_podcast_id']) {
                    $error = _('Already registered with Podcast Index');
                    goto error;
                }
                require_once('../vendor/autoload.php');
                try {
                    $client = new PodcastIndex\Client([
                        'app' => 'PodcastGenerator/' . $version,
                        'key' => $config['pi_api_key'],
                        'secret' => $config['pi_api_secret']
                    ]);
                    /** @var PodcastIndex\Response */
                    $response = $client->add->byFeedUrl($config['url'] . $config['feed_dir'] . 'feed.xml');
                    if ($response->code() >= 400) {
                        $error = _('API error') . ': ' . $response->reason();
                        goto error;
                    }
                    $result = $response->json();
                    if ($result->status && $result->status != 'false' && $result->feedId) {
                        updateConfig('../config.php', 'pi_podcast_id', $result->feedId);
                    }
                }
                catch (Exception $e) {
                    $error = $e->getMessage();
                    goto error;
                }
            break;

            default:
                $error = _('Unsupported service') . ': ' . $register;
                goto error;
            break;
        }
    }

    header('Location: pg_integrations.php');
    die();

    error: echo("");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']) ?> - <?= _('Podcast Generator Configuration'); ?></title>
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
        <h1><?= _('Manage integrations') ?></h1>
        <?php
        if (isset($error)) {
            echo '<p style="color: red;"><strong>' . $error . '</strong></p>';
        } ?>
        <form action="pg_integrations.php?edit=1" method="POST">
            <section>
                <h2><?= _('Podcast Index'); ?>:</h2>
                <?= _('API key') ?>:<br>
                <input type="text" name="pi_api_key" value="<?= htmlspecialchars($config['pi_api_key']) ?>"><br>
                <?= _('API secret') ?>:<br>
                <input type="password" name="pi_api_secret" value="<?= htmlspecialchars($config['pi_api_secret']) ?>"><br>
                <?= _('Podcast ID') ?>:<br>
                <small><?= _('Enter the ID number for your show in Podcast Index or click "Add Show" to add your show to the index') ?></small><br>
                <input type="text" name="pi_podcast_id" value="<?= $config['pi_podcast_id'] ?>">
                <?php if (!$config['pi_podcast_id']) { ?>
                    <button type="submit" name="_register" value="podcast-index" class="btn btn-sm btn-secondary"><?= _('Add Show') ?></button>
                <?php } ?><br>
                <hr>
            </section>
            <section>
                <h2><?= _('WebSub') ?>:</h2>
                <?= _('Server address') ?>:<br>
                <small><?= _('This is the full address of the WebSub hub to alert when the podcast is updated.') ?></small><br>
                <input type="text" name="websub_server" value="<?= htmlspecialchars($config['websub_server']) ?>"><br>
                <hr>
            </section>
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>