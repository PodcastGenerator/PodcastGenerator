<!DOCTYPE html>
<html lang="<?php echo $config["feed_language"]; ?>">

<head>
    <title><?php echo $config["podcast_title"]; ?></title>
    <link rel="stylesheet" href="<?php echo $config["theme_path"]; ?>style/bootstrap.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php"><?php echo $config["podcast_title"]; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin/">Admin</a>
                </li>
            </ul>
        </div>
    </nav>
    <br>
    <div class="container">
        <div class="jumbotron">
            <h1 class="display-4"><?php echo $config["podcast_title"]; ?></h1>
            <p class="lead"><?php echo $config["podcast_description"]; ?></p>
            <small>TODO: Add buttons here</small>
        </div>
    </div>
</body>

</html>