<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <a class="navbar-brand" href="index.php">Admin</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="<?php echo _('Toggle navigation'); ?>">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo _('Episodes'); ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="episodes_upload.php"><?php echo _('Upload New Episodes'); ?></a>
                    <a class="dropdown-item" href="../index.php"><?php echo _('Edit / Delete Episode'); ?></a>
                    <a class="dropdown-item" href="episodes_manage_cats.php"><?php echo _('Manage categories'); ?></a>
                    <a class="dropdown-item" href="episodes_ftp_feature.php"><?php echo _('FTP Feature (Auto Indexing)'); ?></a>
                    <a class="dropdown-item" href="episodes_regenerate.php"><?php echo _('Manually regenerate RSS feed'); ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo _('Themes and aspect'); ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="theme_change.php"><?php echo _('Change Theme'); ?></a>
                    <a class="dropdown-item" href="theme_freebox.php"><?php echo _('Customize your Freebox'); ?></a>
                    <a class="dropdown-item" href="theme_buttons.php"><?php echo _('Change Buttons'); ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo _('Podcast Platform Settings'); ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="store_cover.php"><?php echo _('Change Cover Art'); ?></a>
                    <a class="dropdown-item" href="store_cat.php"><?php echo _('Change Podcast Category'); ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo _('Podcast Details'); ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="podcast_details.php"><?php echo _('Change Podcast details'); ?></a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo _('Podcast Generator'); ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="pg_config.php"><?php echo _('Change Podcast Generator Config'); ?></a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout - <?php echo $_SESSION["username"]; ?></a>
            </li>
        </ul>
    </div>
</nav>