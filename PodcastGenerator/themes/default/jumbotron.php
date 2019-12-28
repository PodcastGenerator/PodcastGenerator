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