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
        input.form-control.half-width { width: 50%; display: inline-block; }
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
            <div class="alert alert-danger" role="alert"><?= $viewMeta->error ?></div>
        <?php } elseif ($viewMeta->success) { ?>
            <div class="alert alert-success" role="alert">
                <?php if ($viewMeta->newItem) { ?>
                    <?= htmlspecialchars(sprintf(_('"%s" uploaded successfully'), $model->title)) ?>
                <?php } else { ?>
                    <?= _('Successfully updated episode!') ?>
                <?php } ?>
            </div>
        <?php } ?>
        <form action="<?= htmlspecialchars($viewMeta->action) ?>"
                method="POST"
                enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h4><?= _('Main Information') ?></h4>
                    <hr>
                    <input type="hidden" name="guid" value="<?= htmlspecialchars($model->guid) ?>">
                    <?php if ($viewMeta->newItem) { ?>
                        <div class="form-group">
                            <label for="file" class="req"><?= _('File') ?>:</label><br>
                            <input type="file" id="file" name="file" required
                                    class="form-control <?= $model->cssClassFor('file') ?>">
                            <span class="invalid-feedback"><?= $model->validationFor('file') ?></span>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="title" class="req"><?= _('Title') ?>:</label><br>
                        <input type="text" id="title" name="title"
                                class="form-control <?= $model->cssClassFor('title') ?>"
                               value="<?= htmlspecialchars($model->title) ?>" required>
                        <span class="invalid-feedback"><?= $model->validationFor('title') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="shortdesc" class="req"><?= _('Short Description') ?>:</label><br>
                        <input type="text" id="shortdesc" name="shortdesc"
                                class="form-control <?= $model->cssClassFor('shortdesc') ?>"
                               value="<?= htmlspecialchars($model->shortdesc) ?>"
                               maxlength="255" oninput="shortDescCheck()" required>
                        <span class="invalid-feedback"><?= $model->validationFor('shortdesc') ?></span>
                        <i id="shortdesc_counter"><?= sprintf(_('Characters remaining: %d'), 255) ?></i>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['categoriesenabled']) ?>">
                        <label for="category"><?= _('Category') ?>:</label><br>
                        <small class="form-text text-muted">
                            <?= _('You can select up to 3 categories') ?>
                        </small><br>
                        <?= htmlOptionSelectMulti(
                            'category',
                            $model->categories,
                            EpisodeFormModel::$categoryOptions,
                            'form-control ' . $model->cssClassFor('category')
                        ) ?>
                        <span class="invalid-feedback"><?= $model->validationFor('category') ?></span>
                    </div>
                    <div class="form-group">
                        <label class="req" style="width:100%"><?= _('Publication Date') ?>:</label>
                        <small class="form-text text-muted" style="width:100%">
                            <?= _('If you select a date in the future, it will be published then') ?>
                        </small>

                        <label class="sr-only" for="date"><?= _('Date') ?>:</label>
                        <input name="date" id="date" type="date" required
                                pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
                                class="form-content half-width <?= $model->cssClassFor('date') ?>"
                                value="<?= $model->date ?>">
                        <label class="sr-only" for="time"><?= _('Time') ?>:</label>
                        <input name="time" id="time" type="time" required
                                pattern="[0-9]{2}:[0-9]{2}"
                                class="form-content half-width <?= $model->cssClassFor('date') ?>"
                                value="<?= $model->time ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('date') ?></span>
                    </div>
                </div>
                <div class="col-6">
                    <h4><?= _('Extra Information') ?></h4>
                    <hr>
                    <div class="form-group">
                        <div><span><?= _('Current Cover') ?>:</span></div>
                        <img src="<?= htmlspecialchars($model->getCoverImageUrl()) ?>"
                             style="max-height: 350px; max-width: 350px;">
                        <hr>
                        <label for="cover"><?= _('Upload new cover') ?>:</label><br>
                        <input type="file" id="cover" name="cover"
                                class="form-control <?= $model->cssClassFor('cover') ?>">
                        <input type="hidden" name="coverart" value="<?= htmlspecialchars($model->coverart) ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('cover') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="longdesc"><?= _('Long Description') ?>:</label><br>
                        <textarea class="form-control <?= $model->cssClassFor('longdesc') ?>"
                                id="longdesc" name="longdesc"><?= htmlspecialchars($model->longdesc) ?></textarea>
                        <span class="invalid-feedback"><?= $model->validationFor('longdesc') ?></span>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="episodenum"><?= _('Episode Number') ?>:</label><br>
                        <input type="text" id="episodenum" name="episodenum" pattern="[0-9]*"
                                class="form-control <?= $model->cssClassFor('episodenum') ?>"
                               value="<?= htmlspecialchars($model->episodenum) ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('episodenum') ?></span>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="seasonnum"><?= _('Season Number') ?>:</label><br>
                        <input type="text" id="seasonnum" name="seasonnum" pattern="[0-9]*"
                                class="form-control <?= $model->cssClassFor('seasonnum') ?>"
                               value="<?= htmlspecialchars($model->seasonnum) ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('seasonnum') ?></span>
                        <br>
                    </div>
                    <div class="form-group">
                        <label for="itunesKeywords"><?= _('iTunes Keywords') ?>:</label><br>
                        <input type="text" id="itunesKeywords" name="itunesKeywords"
                               value="<?= htmlspecialchars($model->itunesKeywords) ?>"
                                placeholder="<?= _('Keyword1, Keyword2 (max 12)') ?>"
                                class="form-control <?= $model->cssClassFor('itunesKeywords') ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('itunesKeywords') ?></span>
                        <br>
                    </div>
                    <div class="form-row form-group">
                        <label class="req" style="width:100%"><?= _('Explicit Content') ?>:</label>
                        <?php htmlOptionRadios('explicit', $model->explicit, EpisodeFormModel::$yesNoOptions); ?>
                        <span class="invalid-feedback"><?= $model->validationFor('explicit') ?></span>
                        <br>
                    </div>
                    <div class="form-row form-group">
                        <label class="req" style="width:100%"><?= _('Episode Type') ?>:</label>
                        <?php htmlOptionRadios('episodetype', $model->episodeType, EpisodeFormModel::$epTypeOptions); ?>
                        <span class="invalid-feedback"><?= $model->validationFor('episodetype') ?></span>
                        <br>
                    </div>
                    <div class="form-row">
                        <div class="col-12"><label><?= _('Author') ?>:</label></div>
                        <div class="col-12 form-group">
                            <label class="sr-only" for="authorname"><?= _('Name') ?>:</label><br>
                            <input type="text" id="authorname" name="authorname"
                                    class="form-control <?= $model->cssClassFor('authorname') ?>"
                                    placeholder="<?= _('Author Name') ?>"
                               value="<?= htmlspecialchars($model->authorname) ?>">
                            <span class="invalid-feedback"><?= $model->validationFor('authorname') ?></span>
                        </div>
                        <div class="col-12 form-group">
                            <label class="sr-only" for="authoremail"><?= _('E-mail address') ?>:</label>
                            <input type="email" id="authoremail" name="authoremail"
                                    class="form-control <?= $model->cssClassFor('authoremail') ?>"
                                    placeholder="<?= _('Author E-Mail') ?>"
                               value="<?= htmlspecialchars($model->authoremail) ?>">
                            <span class="invalid-feedback"><?= $model->validationFor('authoremail') ?></span>
                        </div>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['customtagsenabled']) ?>">
                        <label for="customtags"><?= _('Custom Tags') ?>:</label><br>
                        <textarea class="form-control <?= $model->cssClassFor('customtags') ?>"
                                id="customtags" name="customtags"><?= htmlspecialchars($model->customtags) ?></textarea>
                        <span class="invalid-feedback"><?= $model->validationFor('customtags') ?></span>
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
