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
            <p style="color: red;"><?= $viewMeta->error ?></p>
        <?php } elseif ($viewMeta->success) { ?>
            <p style="color: green;"><?= _('Successfully updated live item!') ?></p>
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
                        <label for="title" class="req"><?= _('Title') ?>:</label><br>
                        <input type="text" id="title" name="title" class="form-control"
                               value="<?= htmlspecialchars($model->title) ?>" required>
                        <span class="invalid-feedback"><?= $model->validationFor('title') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="shortDesc" class="req"><?= _('Short Description') ?>:</label><br>
                        <input type="text" id="shortDesc" name="shortDesc" class="form-control"
                               value="<?= htmlspecialchars($model->shortDesc) ?>"
                               maxlength="255" oninput="shortDescCheck()" required>
                        <span class="invalid-feedback"><?= $model->validationFor('shortDesc') ?></span>
                        <i id="shortDesc_counter"><?= sprintf(_('Characters remaining: %d'), 255) ?></i>
                    </div>
                    <div class="form-group row">
                        <div class="col-12"><?= _('Start Time') ?>:</div>
                        <div class="col-6">
                            <label for="startDate" class="req"><?= _('Date') ?>:</label><br>
                            <input name="startDate" id="startDate" type="date" required
                                   value="<?= $model->startTimeDate ?>">
                        </div>
                        <div class="col-6">
                            <label for="startTime" class="req"><?= _('Time') ?>:</label><br>
                            <input name="startTime" id="startTime" type="time" required
                                   value="<?= $model->startTimeTime ?>">
                        </div>
                        <div class="col-12">
                            <span class="invalid-feedback"><?= $model->validationFor('startTime') ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12"><?= _('End Time') ?>:</div>
                        <div class="col-6">
                            <label for="endDate" class="req"><?= _('Date') ?>:</label><br>
                            <input name="endDate" id="endDate" type="date" required
                                   value="<?= $model->endTimeDate ?>">
                        </div>
                        <div class="col-6">
                            <label for="endTime" class="req"><?= _('Time') ?>:</label><br>
                            <input name="endTime" id="endTime" type="time" required
                                   value="<?= $model->endTimeTime ?>">
                        </div>
                        <div class="col-12">
                            <span class="invalid-feedback"><?= $model->validationFor('endTime') ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= _('Status') ?>:<br>
                        <?php htmlOptionRadios('status', $model->status, LiveItemFormModel::$statusOptions); ?>
                        <span class="invalid-feedback"><?= $model->validationFor('status') ?></span>
                    </div>
                    <div class="form-group">
                        <?= _('Stream Information') ?>:<br>
                        <label for="streamUrl"><?= _('URL') ?>:</label><br>
                        <input name="streamUrl" id="streamUrl" type="url" class="form-control"
                               value="<?= $model->streamUrl ?>"
                               placeholder="<?= $config['liveitems_default_stream'] ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('streamUrl') ?></span>
                        <br>
                        <label for="streamType"><?= _('MIME Type') ?>:</label><br>
                        <?php htmlOptionSelect(
                            'streamType',
                            $model->streamType,
                            LiveItemFormModel::$mimeTypeOptions,
                            'form-control'
                        ); ?>
                        <span class="invalid-feedback"><?= $model->validationFor('streamType') ?></span>
                    </div>
                </div>

                <div class="col-6">
                    <h4><?= _('Extra Information') ?></h4>
                    <hr>
                    <div class="form-group">
                        <?= _('Current Cover'); ?>:<br>
                        <img src="<?= htmlspecialchars($model->getCoverImageUrl()) ?>"
                             style="max-height: 350px; max-width: 350px;">
                        <hr>
                        <label for="cover"><?= _('Upload new cover') ?>:</label><br>
                        <input type="file" id="cover" name="cover"><br>
                        <input type="hidden" id="coverImageUrl" name="coverImageUrl"
                               value="<?= htmlspecialchars($model->coverImageUrl) ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('cover') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="longDesc"><?= _('Long Description') ?>:</label><br>
                        <textarea id="longDesc" name="longDesc"
                                  class="form-control"><?= htmlspecialchars($model->longDesc) ?></textarea>
                        <br>
                        <span class="invalid-feedback"><?= $model->validationFor('longDesc') ?></span>
                    </div>
                    <div class="form-group">
                        <label for="authorName"><?= _('Author') ?>:</label><br>
                        <input type="text" id="authorName" name="authorName" class="form-control"
                               placeholder="Author Name"
                               value="<?= htmlspecialchars($model->authorName) ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('authorName') ?></span>
                        <br>
                        <input type="email" id="authorEmail" name="authorEmail" class="form-control"
                               placeholder="Author E-Mail"
                               value="<?= htmlspecialchars($model->authorEmail) ?>">
                        <span class="invalid-feedback"><?= $model->validationFor('authorEmail') ?></span>
                        <br>
                    </div>
                    <div class="form-group" style="<?= displayBlockCss($config['customtagsenabled']) ?>">
                        <label for="customtags"><?= _('Custom Tags') ?>:</label><br>
                        <textarea id="customtags" name="customtags"
                                class="form-control"><?= htmlspecialchars($model->customTags) ?></textarea>
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
