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
        } elseif ($key == '_register') {
            $register = $value;
        }
    }

    if (isset($register)) {
        switch ($register) {
            case 'podcast-index':
                if ($config['pi_podcast_id']) {
                    //# 'Podcast Index' is a proper name.
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
    <?php //# 'Podcast Generator' is a proper name. ?>
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
                <?php //# 'Podcast Index' is a proper name. ?>
                <h2><?= _('Podcast Index'); ?>:</h2>
                <label for="pi_api_key"><?= _('API key') ?>:</label><br>
                <input type="text" id="pi_api_key" name="pi_api_key"
                       value="<?= htmlspecialchars($config['pi_api_key']) ?>">
                <br>

                <label for="pi_api_secret"><?= _('API secret') ?>:</label><br>
                <input type="password" id="pi_api_secret" name="pi_api_secret"
                       value="<?= htmlspecialchars($config['pi_api_secret']) ?>">
                <br>

                <label for="pi_podcast_id"><?= _('Podcast ID') ?>:</label><br>
                <small>
                    <?php //# 'Podcast Index' is a proper name. ?>
                    <?= _('Enter the ID number for your show in Podcast Index or click "Add Show" to add your show to the index') ?>
                </small>
                <br>
                <input type="text" id="pi_podcast_id" name="pi_podcast_id" value="<?= $config['pi_podcast_id'] ?>">

                <?php if (!$config['pi_podcast_id']) { ?>
                    <button type="submit" name="_register" value="podcast-index" class="btn btn-sm btn-secondary">
                        <?= _('Add Show') ?>
                    </button>
                <?php } ?><br>
                <hr>
            </section>

            <section>
                <?php //# 'WebSub' is a proper name. ?>
                <h2><?= _('WebSub') ?>:</h2>

                <label for="websub_server"><?= _('Server address') ?>:</label><br>
                <small>
                    <?php //# 'WebSub' is a proper name. ?>
                    <?= _('This is the full address of the WebSub hub to alert when the podcast is updated.') ?>
                </small>
                <br>
                <input type="text" id="websub_server" name="websub_server"
                       value="<?= htmlspecialchars($config['websub_server']) ?>">
                <br>
                <hr>
            </section>

            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success"><br>
        </form>
    </div>
</body>

</html>