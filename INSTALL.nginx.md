# INSTALL

## Install from scratch - Basic

1. Download the latest version of Podcast Generator;
2. Unzip the zip package containing the script;
3. Upload the resulting files and folders to your web server;
4. Point your web browser to the URL corresponding to the location where
Podcast Generator files were uploaded (e.g. http://mypodcastsite.com/podcastgen).
You will be redirected automatically to the 3-step setup wizard;
5. Log-in into Podcast Generator administration area and start publishing your
podcast.

## Upgrade from 2.7

Caution: MAKE A BACKUP OF YOUR ENTIRE PODCAST GENERATOR FOLDER BEFORE UPGRADING!!!!!

1. Download the latest version
2. Upload it to your web server, allow to overwrite files.
3. Point your web browser to the URL corresponding to the location where
Podcast Generator files were uploaded (e.g. http://mypodcastsite.com/podcastgen).
You will be redirected to a password converter;
4. Enjoy!

## Install from scratch - Detailed

*******
Install Podcast Generator on Ubuntu 22.04

Please read this through entirely before using.
[Issues 272](https://github.com/PodcastGenerator/PodcastGenerator/issues/272)
*******
This guide assumes there is a fresh install of Ubuntu 22.04.0 and that the user
initially logs into the server as the root user.

Note: These instructions are for illustrative purposes, you situation may require
further enhancements and security considerations.

### Setup Your Server

1. Open your local machine or login via ssh:

2. Create a user:

    ```bash
    adduser ${replace_with_your_username}
    ```

3. Add the new user to the sudo group:

    ```bash
    usermod -aG sudo ${replace_with_your_username}
    ```

4. Login with user:

    ```bash
    su ${replace_with_your_username}
    ```

5. Add port 22 and 80 to the firewall rules and enable:

    ```bash
    sudo ufw allow 80
    sudo ufw allow 443
    sudo ufw allow 22
    sudo ufw enable
    sudo ufw status
    ```

    All active rules should then be shown.

6. Update system:

    ```bash
    sudo apt update && sudo apt upgrade
    ```

7. Install PHP, unzip and nginx:

    ```bash
    sudo apt install php-cli php-fpm php-json php-zip php-gd php-mbstring \
      php-curl php-xml php-pear php-bcmath unzip nginx wget
    ```

8. Confirm PHP version and configure PHP for file uploads:

    ```bash
    php --version
    ```

    Output should look like:

    ```bash
    PHP 8.1.2 (cli) (built: Apr  7 2022 17:46:26) (NTS)
    Copyright (c) The PHP Group
    Zend Engine v4.1.2, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.2, Copyright (c), by Zend Technologies
    ```

    Configure your PHP install so file uploads are allowed and working

    ```bash
    sudo nano /etc/php/(version)/fpm/php.ini
    ```

    Change the according lines to reflect
    * `memory_limit = 514M`
    * `post_max_size = 513M`
    * `upload_max_filesize = 512M`

    Restart PHP FPM

    ```bash
    sudo systemctl restart php8.1-fpm.service
    ```

### Install Podcast Generator

The next steps assume that you will be using the default html folder provided
by the apache install and no other virtual hosts are present.

1. Set VERSION for PodcastGenerator and change directory (cd) to the folder
where PodcastGenerator will be installed:

    ```bash
    export VERSION='3.2.6'
    cd /var/www/
    ```

2. Remove index.html:

    ```bash
    sudo rm -rf html/index*.html
    ```

3. Download the latest release: (replace url with the current release)

    ```bash
    sudo wget \
    https://github.com/PodcastGenerator/PodcastGenerator/releases/download/v${VERSION}/PodcastGenerator-v${VERSION}.zip
    ```

4. Unzip PodcastGenerator: Replace with current release version

    ```bash
    sudo unzip PodcastGenerator-v${VERSION}.zip -d PodcastGenerator-v${VERSION}
    ```

5. Move PodcastGenerator from the unzipped directory PodcastGenerator-${VERSION}
to var/www/html:

    ```bash
    sudo mv /var/www/PodcastGenerator-v${VERSION}/PodcastGenerator/* /var/www/html/
    ```

6. Copy the nginx configuration file to the configuration directory and enable it:

    ```bash
    sudo mv /var/www/PodcastGenerator-v${VERSION}/podcastgenerator-nginx.conf \
      /etc/nginx/sites-available/podcastgenerator-nginx.conf
    ```

    You will need to edit the configuration file to fit your environment.
    The file contains comments to help you through the process.

    ```bash
    sudo nano /etc/nginx/sites-available/podcastgenerator-nginx.conf
    ```

    Create the file link to enable the webserver configuration

    ```bash
    sudo ln -s /etc/nginx/sites-available/podcastgenerator-nginx.conf \
    /etc/nginx/sites-enabled/podcastgenerator-nginx.conf
    ```

    Verify NGINX conf is working with

    ```bash
    sudo nginx -t
    ```

    If the conf check looks goot, restart nginx to apply your changes

    ```bash
    sudo systemctl restart nginx
    ```

7. Cleanup by removing unneccessary files:

    ```bash
    sudo rm -rf PodcastGenerator-v${VERSION}/ PodcastGenerator-v${VERSION}.zip
    ```

8. Change ownership of the installation files: (NOTE, this is for Ubuntu.
If you are using BSD,RHEL,CENTOS,etc. Ownership may be different on different
opperating systems.)

    Update ownership

    ```bash
    sudo chown -R www-data:www-data /var/www/html
    ```

    Update file permissions

    ```bash
    sudo chmod -R 755 html/images
    sudo chmod -R 755 html/media

    ```

9. Optional: Install certbot and obtain a Let's Encrypt certificate:

    ```bash
    sudo snap install certbot --classic
    sudo certbot --nginx -d domain.of.your.podcastgenerator
    ```

10. Navigate to the IP, domain or local host address of the machine in a
web browser:

    `ip.address.in.browser` or `domain.of.your.podcastgenerator`

11. Select "Begin Installation"

## ALL DONE

Enjoy your installation of Podcast Generator!

Consider spreading the word about the software or
[contribute back to the project](https://github.com/PodcastGenerator/PodcastGenerator).

If your self-hosted instance gets to be too much to handle or if the
installation/maintainance/security seems too intimidating, please consider using our
[hosting partner](https://rss.com/blog/how-to-create-an-rss-feed-for-a-podcast/).
Free accounts are availble for students and Non-profits and other users start
with a free trial and have the option to upgrade to a full featured hosted plan
starting at $12.99/month (USD).
