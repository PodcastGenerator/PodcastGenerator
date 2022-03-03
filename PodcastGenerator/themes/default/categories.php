<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config["podcast_title"]) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/bootstrap.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/custom.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/font-awesome.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($config["theme_path"]) ?>style/dark.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <?php include "navbar.php"; ?>
    <br>
    <div class="container">
        <?php
            include "js.php";
            include "jumbotron.php";
        ?>
        <div class="row">
            <?php if (!isset($_GET["cat"])) { ?>
                <div class="col">
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action" href="categories.php?cat=all"><?= _('All Episodes') ?></a>
                        <?php foreach ($categories_arr as $key => $value) { ?>
                            <a class='list-group-item list-group-item-action' href="categories.php?cat=<?= $key ?>"><?= $value ?></a>
                        <?php } ?>
                    </div>
                </div>
            <?php } else {
                include 'listepisodes.php';
            } ?>
        </div>
    </div>
</body>

</html>
