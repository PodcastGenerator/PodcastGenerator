<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <a class="navbar-brand" href="index.php">Admin</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="<?= _('Toggle navigation') ?>">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= _('Episodes') ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="episodes_upload.php"><?= _('Upload New Episodes') ?></a>
                    <a class="dropdown-item" href="episodes_list.php"><?= _('View All Episodes') ?></a>
                    <a class="dropdown-item" href="episodes_manage_cats.php"><?= _('Manage categories') ?></a>
                    <a class="dropdown-item" href="episodes_ftp_feature.php"><?= _('FTP Feature (Auto Indexing)') ?></a>
                    <a class="dropdown-item" href="episodes_bulk.php"><?= _('Bulk download episodes') ?></a>
                    <a class="dropdown-item" href="episodes_regenerate.php"><?= _('Manually regenerate RSS feed') ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= _('Themes and aspect') ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="theme_change.php"><?= _('Change Theme') ?></a>
                    <a class="dropdown-item" href="theme_freebox.php"><?= _('Customize your Freebox') ?></a>
                    <a class="dropdown-item" href="theme_buttons.php"><?= _('Change Buttons') ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= _('Podcast Platform Settings') ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="store_cover.php"><?= _('Change Cover Art') ?></a>
                    <a class="dropdown-item" href="store_cat.php"><?= _('Change Podcast Category') ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= _('Podcast Details') ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="podcast_details.php"><?= _('Change Podcast details') ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php //# 'Podcast Generator' is a proper name. ?>
                    <?= _('Podcast Generator') ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php //# 'Podcast Generator' is a proper name. ?>
                    <a class="dropdown-item" href="pg_config.php"><?= _('Change Podcast Generator Config') ?></a>
                    <a class="dropdown-item" href="pg_users.php"><?= _('Manage users') ?></a>
                    <a class="dropdown-item" href="pg_integrations.php"><?= _('Manage integrations') ?></a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= $config['url']; ?>" target="_blank"><?= _('View Podcast') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout - <?= $_SESSION["username"] ?></a>
            </li>
        </ul>
    </div>
</nav>