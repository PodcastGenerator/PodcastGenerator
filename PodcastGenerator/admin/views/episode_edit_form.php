<?php

// phpcs:disable
require_once(__DIR__ . '/../../vendor/autoload.php');
// phpcs:enable

use PodcastGenerator\Configuration;
use PodcastGenerator\Models\Admin\EpisodeFormModel;

function episode_edit_form(
    EpisodeFormModel $model,
    object $viewMeta,
    Configuration $config
) {
    ?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']) . ' - ' . $viewMeta->title ?></title>
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
    include __DIR__ . '/../js.php';
    include __DIR__ . '/../navbar.php';
    ?>
    <br>
    <div class="container">
        <h3><?= $viewMeta->title ?></h3>
        <?php if (isset($viewMeta->error) && !empty($viewMeta->error)) { ?>
            <p style="color: red;"><strong><?= $viewMeta->error ?></strong></p>
        <?php } elseif ($viewMeta->success) { ?>
            <p style="color: #2ecc71;">
                <strong><?= htmlspecialchars(sprintf(_('"%s" uploaded successfully'), $model->title)) ?></strong>
            </p>
        <?php } ?>
        <form action="<?= htmlspecialchars($viewMeta->action) ?>"
              method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h4><?= _('Main Information') ?></h4>
                    <hr>
                    <input type="hidden" name="guid" value="<?= htmlspecialchars($model->guid) ?>">
                    <?php if ($viewMeta->newItem) { ?>
                        <div class="form-group">
                            <label for="file" class="req"><?= _('File') ?>:</label><br>
                            <input type="file" id="file" name="file" required><br>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="title" class="req"><?= _('Title') ?>:</label><br>
                        <input type="text" id="title" name="title" class="form-control"
                               value="<?= htmlspecialchars($model->title) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="shortdesc" class="req"><?= _('Short Description') ?>:</label><br>
                        <input type="text" id="shortdesc" name="shortdesc" class="form-control"
                               value="<?= htmlspecialchars($model->shortdesc) ?>"
                               maxlength="255" oninput="shortDescCheck()" required>
                        <i id="shortdesc_counter"><?= sprintf(_('Characters remaining: %d'), 255) ?></i>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['categoriesenabled']) ?>">
                        <label for="categories"><?= _('Category') ?>:</label><br>
                        <small><?= _('You can select up to 3 categories') ?></small><br>
                        <?= htmlOptionSelectMulti(
                            'category',
                            $model->categories,
                            EpisodeFormModel::$categoryOptions,
                            'form-control ' . $model->cssClassFor('category')
                        ) ?>
                    </div>
                    <div class="form-group">
                        <?= _('Publication Date') ?>:<br>
                        <small><?= _('If you select a date in the future, it will be published then') ?></small><br>
                        <label for="date" class="req"><?= _('Date') ?>:</label><br>
                        <input name="date" id="date" type="date" value="<?= $model->date ?>" required>
                        <br>
                        <label for="time" class="req"><?= _('Time') ?>:</label><br>
                        <input name="time" id="time" type="time" value="<?= $model->time ?>" required>
                        <br>
                    </div>
                </div>
                <div class="col-6">
                    <h4><?= _('Extra Information') ?></h4>
                    <hr>
                    <div class="form-group">
                        <?= _('Current Cover'); ?>:<br>
                        <img src="<?= htmlspecialchars($model->getCoverImageUrl()) ?>"
                             style="max-height: 350px; max-width: 350px;">
                        <input type="hidden" name="coverart" value="<?= htmlspecialchars($model->coverart) ?>">
                        <hr>
                        <label for="cover"><?= _('Upload new cover') ?>:</label><br>
                        <input type="file" id="cover" name="cover"><br>
                    </div>
                    <div class="form-group">
                        <label for="longdesc"><?= _('Long Description') ?>:</label><br>
                        <textarea id="longdesc" name="longdesc"
                                class="form-control"><?= htmlspecialchars($model->longdesc) ?></textarea>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="episodenum"><?= _('Episode Number') ?>:</label><br>
                        <input type="text" id="episodenum" name="episodenum" pattern="[0-9]*" class="form-control"
                               value="<?= htmlspecialchars($model->episodenum) ?>">
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="seasonnum"><?= _('Season Number') ?>:</label><br>
                        <input type="text" id="seasonnum" name="seasonnum" pattern="[0-9]*" class="form-control"
                               value="<?= htmlspecialchars($model->seasonnum) ?>">
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="itunesKeywords"><?= _('iTunes Keywords') ?>:</label><br>
                        <input type="text" id="itunesKeywords" name="itunesKeywords"
                               value="<?= htmlspecialchars($model->itunesKeywords) ?>"
                               placeholder="Keyword1, Keyword2 (max 12)" class="form-control">
                        <br>
                    </div>
                    <div class="form-group">
                        <?= _('Explicit Content') ?>:<br>
                        <label>
                            <input type="radio" name="explicit" <?= checkedAttr($model->explicit, 'yes') ?>
                                   value="yes">
                            <?= _('Yes') ?>
                        </label>
                        <label>
                            <input type="radio" name="explicit" <?= checkedAttr($model->explicit, 'no') ?>
                                   value="no">
                            <?= _('No') ?>
                        </label>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="authorname"><?= _('Author') ?>:</label><br>
                        <input type="text" id="authorname" name="authorname" class="form-control"
                               placeholder="Author Name"
                               value="<?= htmlspecialchars($model->authorname) ?>">
                        <br>
                        <input type="email" id="authoremail" name="authoremail" class="form-control"
                               placeholder="Author E-Mail"
                               value="<?= htmlspecialchars($model->authoremail) ?>">
                        <br>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['customtagsenabled']) ?>">
                        <label for="customtags"><?= _('Custom Tags') ?>:</label><br>
                        <textarea id="customtags" name="customtags"
                                class="form-control"><?= htmlspecialchars($model->customtags) ?></textarea>
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
        <?php if (!$viewMeta->newItem) { ?>
            <hr>
            <h4><?= _('Delete Episode') ?></h4>
            <form action="episodes_edit.php?name=<?= htmlspecialchars($model->name()) ?>&delete=1" method="POST">
                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                <input type="submit" class="btn btn-danger" value="<?= _('Delete') ?>">
            </form>
        <?php } ?>
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
<?php
}
