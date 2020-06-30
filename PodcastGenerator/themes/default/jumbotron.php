<div class="row">
    <?php
    if (getFreebox() == null) {
        echo '<div class="col-lg-12">';
    } else {
        echo '<div class="col-lg-8">';
    }
    ?>
    <div class="jumbotron">
        <h1 class="display-4"><?php echo htmlspecialchars($config["podcast_title"]); ?></h1>
        <p class="lead"><?php echo htmlspecialchars($config["podcast_description"]); ?></p>
        <?php
        foreach ($buttons as $item) {
            if (!isset($item->protocol)) {
                echo '<a class="' . htmlspecialchars($item->class) . '" href="external.php?name=' . htmlspecialchars($item->name) . '">' . htmlspecialchars($item->name) . '</a> ';
            } else {
                echo '<a class="' . htmlspecialchars($item->class) . '" href="external.php?name=' . htmlspecialchars($item->name) . '">' . htmlspecialchars($item->name) . '</a> ';
            }
        }
        ?>
    </div>
    <div class="search-container" style="margin-bottom: 2rem;">
        <form action="index.php" class="form-inline">
            <div class="form-group" style="width: 80%;">
                <input type="text" class="form-control" placeholder="<?php echo $search; ?>.." name="search" style="width: 98%;">
            </div>
            <div class="form-group" style="width: 20%;">
                <button type="submit" class="btn btn-info" style="width: 100%;"><div style="-webkit-transform: rotate(-45deg); -moz-transform: rotate(-45deg); -o-transform: rotate(-45deg); transform: rotate(-45deg);">&#9906;</div></button>
            </div>
        </form>
    </div>
</div>
<?php
if (getFreebox() != null) {
    echo '
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                ' . getFreebox() . '
            </div>
        </div>
    </div>';
}
?>
</div>
