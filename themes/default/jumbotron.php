<div class="row">
    <?php
    if(getFreebox() == null) {
        echo '<div class="col-lg-12">';
    }
    else {
        echo '<div class="col-lg-8">';
    }
    ?>
        <div class="jumbotron">
            <h1 class="display-4"><?php echo $config["podcast_title"]; ?></h1>
            <p class="lead"><?php echo $config["podcast_description"]; ?></p>
            <small>TODO: Add buttons here</small>
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