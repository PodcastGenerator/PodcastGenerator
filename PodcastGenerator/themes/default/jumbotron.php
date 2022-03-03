<?php

$freebox = getFreebox();

?>
<div class="row">
    <div class="<?= $freebox == null ? "col-lg-12" : "col-lg-8" ?>">
        <div class="jumbotron">
            <h1 class="display-4"><?= htmlspecialchars($config["podcast_title"]) ?></h1>
            <p class="lead"><?= htmlspecialchars($config["podcast_description"]) ?></p>
            <?php foreach ($buttons as $item) { ?>
                <a class="<?= htmlspecialchars($item->class) ?>" href="external.php?name=<?= htmlspecialchars($item->name) ?>"><?= htmlspecialchars($item->name) ?></a>
            <?php } ?>
        </div>
        <div class="search-container" style="margin-bottom: 2rem;">
            <form action="index.php" class="form-inline">
                <div class="form-group" style="width: 80%;">
                    <input type="text" class="form-control" placeholder="<?= isset($search) ? $search : '' ?>..." name="search" style="width: 98%;">
                </div>
                <div class="form-group" style="width: 20%;">
                    <button type="submit" class="btn btn-info" style="width: 100%;">
                        <div style="-webkit-transform: rotate(-45deg); -moz-transform: rotate(-45deg); -o-transform: rotate(-45deg); transform: rotate(-45deg);">&#9906;</div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($freebox != null) { ?>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <?= getFreebox() ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
