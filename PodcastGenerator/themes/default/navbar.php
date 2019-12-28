<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php"><?php echo $config["podcast_title"]; ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <?php
            if (strtolower($config["categoriesenabled"]) == "yes") {
            ?>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php"><?php echo $categories; ?></a>
                </li>
            <?php
            }
            ?>
            <li class="nav-item">
                <a class="nav-link" href="admin/">Admin</a>
            </li>
        </ul>
    </div>
</nav>