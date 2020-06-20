<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        <form action="search.php" class="form-inline">
            <div class="form-group" style="width: 80%;">
                <input type="text" class="form-control" placeholder="Search.." name="search" style="width: 98%;">
            </div>
            <div class="form-group" style="width: 20%;">
                <button type="submit" class="btn btn-info" style="width: 100%;"><i class="fa fa-search"></i></button>
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
