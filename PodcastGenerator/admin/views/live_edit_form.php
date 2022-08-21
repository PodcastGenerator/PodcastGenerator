<?php

// phpcs:disable
require_once(__DIR__ . '/../../vendor/autoload.php');
// phpcs:enable

use PodcastGenerator\Configuration;
use PodcastGenerator\Models\Admin\LiveItemFormModel;

function live_edit_form(
    LiveItemFormModel $model,
    object $viewMeta,
    Configuration $config
) {
    ?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Live Items') ?></title>
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
    include __DIR__ . '/../navbar.php'; ?>
    <br>
    <div class="container">
        <h1><?= $viewMeta->title ?></h1>

        <?php if (isset($viewMeta->error) && !empty($viewMeta->error)) { ?>
            <div class="alert alert-danger" role="alert"><?= $viewMeta->error ?></div>
        <?php } elseif ($viewMeta->success) { ?>
            <div class="alert alert-success" role="alert"><?= _('Successfully updated live item!') ?></div>
        <?php } ?>

        <form action="<?= $viewMeta->action ?>"
                method="POST"
                enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <h4><?= _('Main Information') ?></h4>
                    <hr>
                    <input type="hidden" name="guid" value="<?= htmlspecialchars($model->guid) ?>">
                    <div class="form-group">
                        <label for="title" class="req"><?= _('Title') ?>:</label>
                        <input type="text" id="title" name="title"
                                class="form-control <?= $model->cssClassFor('title') ?>"
                                value="<?= htmlspecialchars($model->title) ?>" required>
                        <span class="invalid-feedback"><?= $model->validationFor('title') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="shortDesc" class="req"><?= _('Short Description') ?>:</label>
                        <input type="text" id="shortDesc" name="shortDesc"
                                class="form-control <?= $model->cssClassFor('shortDesc') ?>"
                                value="<?= htmlspecialchars($model->shortDesc) ?>"
                                maxlength="255" oninput="shortDescCheck()" required>
                        <span class="invalid-feedback"><?= $model->validationFor('shortDesc') ?></span>
                        <i id="shortDesc_counter"><?= sprintf(_('Characters remaining: %d'), 255) ?></i>
                    </div>
                    <div class="form-row">
                        <label class="req" style="width:100%"><?= _('Start Time') ?>:</label>

                        <label class="sr-only" for="startDate"><?= _('Date') ?>:</label>
                        <input name="startDate" id="startDate" type="date" required
                                pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
                                class="form-control half-width <?= $model->cssClassFor('startTime') ?>"
                                value="<?= $model->startTimeDate ?>">
                        <label class="sr-only" for="startTime"><?= _('Time') ?>:</label>
                        <input name="startTime" id="startTime" type="time" required
                                pattern="[0-9]{2}-[0-9]{2}"
                                class="form-control half-width <?= $model->cssClassFor('startTime') ?>"
                                value="<?= $model->startTimeTime ?>">
                        <span class="invalid-feedback">
                            <?= $model->validationFor('startTime') ?>
                        </span>
                    </div>
                    <div class="form-row form-group">
                        <label class="req" style="width:100%"><?= _('End Time') ?>:</label>

                        <label class="sr-only" for="endDate"><?= _('Date') ?>:</label>
                        <input name="endDate" id="endDate" type="date" required
                                pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
                                class="form-control half-width <?= $model->cssClassFor('endTime') ?>"
                                value="<?= $model->endTimeDate ?>">
                        <label class="sr-only" for="endTime"><?= _('Time') ?>:</label>
                        <input name="endTime" id="endTime" type="time" required
                                pattern="[0-9]{2}-[0-9]{2}"
                                class="form-control half-width <?= $model->cssClassFor('endTime') ?>"
                                value="<?= $model->endTimeTime ?>">
                        <span class="invalid-feedback">
                            <?= $model->validationFor('endTime') ?>
                        </span>
                    </div>
                    <div class="form-row form-group">
                        <label class="req" style="width:100%"><?= _('Status') ?>:</label>
                        <?php htmlOptionRadios('status', $model->status, LiveItemFormModel::$statusOptions); ?>
                        <span class="invalid-feedback"><?= $model->validationFor('status') ?></span>
                    </div>
                    <div class="form-row">
                        <div class="col-12"><span><?= _('Stream Information') ?>:</span></div>
                        <div class="col-12 form-group">
                            <label for="streamUrl"><?= _('URL') ?>:</label><br>
                            <input name="streamUrl" id="streamUrl" type="url"
                                    class="form-control <?= $model->cssClassFor('streamUrl') ?>"
                                    value="<?= $model->streamUrl ?>"
                                    placeholder="<?= $config['liveitems_default_stream'] ?>">
                            <span class="invalid-feedback"><?= $model->validationFor('streamUrl') ?></span>
                        </div>
                        <div class="col-12 form-group">
                            <label for="streamType"><?= _('MIME Type') ?>:</label><br>
                            <?php htmlOptionSelect(
                                'streamType',
                                $model->streamType,
                                LiveItemFormModel::$mimeTypeOptions,
                                'form-control ' . $model->cssClassFor('streamType')
                            ); ?>
                            <span class="invalid-feedback"><?= $model->validationFor('streamType') ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <h4><?= _('Extra Information') ?></h4>
                    <hr>
                    <div class="form-group">
                        <div><span><?= _('Current Cover'); ?>:</span></div>
                        <img src="<?= htmlspecialchars($model->getCoverImageUrl()) ?>"
                                style="max-height: 350px; max-width: 350px;">
                        <hr>
                        <label for="cover"><?= _('Upload new cover') ?>:</label>
                        <input type="file" id="cover" name="cover"
                                class="form-control <?= $model->cssClassFor('coverImageUrl') ?>">
                        <input type="hidden" id="coverImageUrl" name="coverImageUrl"
                                value="<?= htmlspecialchars($model->coverImageUrl) ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('cover') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="longDesc"><?= _('Long Description') ?>:</label>
                        <textarea class="form-control <?= $model->cssClassFor('coverImageUrl') ?>"
                                id="longDesc" name="longDesc"><?= htmlspecialchars($model->longDesc) ?></textarea>
                        <br>
                        <span class="invalid-feedback"><?= $model->validationFor('longDesc') ?></span>
                    </div>
                    <div class="form-row">
                        <div class="col-12"><label><?= _('Author') ?>:</label></div>
                        <div class="col-12 form-group">
                            <label class="sr-only" for="authorName"><?= _('Name') ?>:</label>
                            <input type="text" id="authorName" name="authorName"
                                    class="form-control <?= $model->cssClassFor('authorName') ?>"
                                    placeholder="Author Name"
                                    value="<?= htmlspecialchars($model->authorName) ?>">
                            <span class="invalid-feedback"><?= $model->validationFor('authorName') ?></span>
                        </div>
                        <div class="col-12 form-group">
                            <label class="sr-only" for="authorEmail"><?= _('E-mail address') ?>:</label>
                            <input type="email" id="authorEmail" name="authorEmail"
                                    class="form-control <?= $model->cssClassFor('authorEmail') ?>"
                                    placeholder="Author E-Mail"
                                    value="<?= htmlspecialchars($model->authorEmail) ?>">
                            <span class="invalid-feedback"><?= $model->validationFor('authorEmail') ?></span>
                        </div>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['customtagsenabled']) ?>">
                        <label for="customtags"><?= _('Custom Tags') ?>:</label>
                        <textarea class="form-control <?= $model->cssClassFor('customtags') ?>"
                                id="customtags" name="customtags"><?= htmlspecialchars($model->customTags) ?></textarea>
                        <br>
                        <span class="invalid-feedback"><?= $model->validationFor('customTags') ?></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                    <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success">
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
            let shortdesc = document.getElementById("shortDesc").value;
            let maxlength = 255;
            let remaining = maxlength - shortdesc.length;
            let counter
                = document.getElementById("shortDesc_counter").innerText
                = "<?= _('Characters remaining: %d') ?>".replace('%d', remaining);
        }
        shortDescCheck();
    </script>
</body>

</html>
<?php
}
