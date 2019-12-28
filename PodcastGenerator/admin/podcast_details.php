<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################
require 'checkLogin.php';
require '../core/include_admin.php';

if (isset($_GET['edit'])) {
    foreach ($_POST as $key => $value) {
        updateConfig('../config.php', $key, $value);
    }
    header('Location: podcast_details.php');
    die();
} else {
    generateRSS();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config['podcast_title']); ?> - <?php echo _('Podcast Details'); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        .txt {
            width: 100%;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $config['url']; ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?php echo _('Change Podcast Details'); ?></h1>
        <form action="podcast_details.php?edit=1" method="POST">
            <?php echo _('Podcast Title'); ?>:<br>
            <input type="text" name="podcast_title" value="<?php echo htmlspecialchars($config['podcast_title']); ?>" class="txt"><br>
            <?php echo _('Podcast Subtitle or Slogan'); ?>:<br>
            <input type="text" name="podcast_subtitle" value="<?php echo htmlspecialchars($config['podcast_subtitle']); ?>" class="txt"><br>
            <?php echo _('Podcast Description'); ?>:<br>
            <input type="text" name="podcast_description" value="<?php echo htmlspecialchars($config['podcast_description']); ?>" class="txt"><br>
            <?php echo _('Copyright Notice'); ?>:<br>
            <input type="text" name="copyright" value="<?php echo htmlspecialchars($config['copyright']); ?>" class="txt"><br>
            <?php echo _('Author Name'); ?>:<br>
            <input type="text" name="author_name" value="<?php echo htmlspecialchars($config['author_name']); ?>" class="txt"><br>
            <?php echo _('Author E-Mail Address'); ?>:<br>
            <input type="text" name="author_email" value="<?php echo htmlspecialchars($config['author_email']); ?>" class="txt"><br>
            <?php echo _('Feed Language'); ?>: (<?php echo _('Main language of your podcast'); ?>)<br>
            <select name="feed_language">
                <option value="af">Afrikanns</option>
                <option value="sq">Albanian</option>
                <option value="ar">Arabic</option>
                <option value="hy">Armenian</option>
                <option value="eu">Basque</option>
                <option value="bn">Bengali</option>
                <option value="bg">Bulgarian</option>
                <option value="ca">Catalan</option>
                <option value="km">Cambodian</option>
                <option value="zh">Chinese (Mandarin)</option>
                <option value="hr">Croation</option>
                <option value="cs">Czech</option>
                <option value="da">Danish</option>
                <option value="nl">Dutch</option>
                <option value="en" selected>English</option>
                <option value="eo">Esperanto</option>
                <option value="et">Estonian</option>
                <option value="fj">Fiji</option>
                <option value="fi">Finnish</option>
                <option value="fr">French</option>
                <option value="ka">Georgian</option>
                <option value="de">German</option>
                <option value="el">Greek</option>
                <option value="gu">Gujarati</option>
                <option value="he">Hebrew</option>
                <option value="hi">Hindi</option>
                <option value="hu">Hungarian</option>
                <option value="is">Icelandic</option>
                <option value="id">Indonesian</option>
                <option value="ga">Irish</option>
                <option value="it">Italian</option>
                <option value="ja">Japanese</option>
                <option value="jw">Javanese</option>
                <option value="ko">Korean</option>
                <option value="la">Latin</option>
                <option value="lv">Latvian</option>
                <option value="lt">Lithuanian</option>
                <option value="mk">Macedonian</option>
                <option value="ms">Malay</option>
                <option value="ml">Malayalam</option>
                <option value="mt">Maltese</option>
                <option value="mi">Maori</option>
                <option value="mr">Marathi</option>
                <option value="mn">Mongolian</option>
                <option value="ne">Nepali</option>
                <option value="no">Norwegian</option>
                <option value="fa">Persian</option>
                <option value="pl">Polish</option>
                <option value="pt">Portuguese</option>
                <option value="pa">Punjabi</option>
                <option value="qu">Quechua</option>
                <option value="ro">Romanian</option>
                <option value="ru">Russian</option>
                <option value="sm">Samoan</option>
                <option value="sr">Serbian</option>
                <option value="sk">Slovak</option>
                <option value="sl">Slovenian</option>
                <option value="es">Spanish</option>
                <option value="sw">Swahili</option>
                <option value="sv">Swedish </option>
                <option value="ta">Tamil</option>
                <option value="tt">Tatar</option>
                <option value="te">Telugu</option>
                <option value="th">Thai</option>
                <option value="bo">Tibetan</option>
                <option value="to">Tonga</option>
                <option value="tr">Turkish</option>
                <option value="uk">Ukranian</option>
                <option value="ur">Urdu</option>
                <option value="uz">Uzbek</option>
                <option value="vi">Vietnamese</option>
                <option value="cy">Welsh</option>
                <option value="xh">Xhosa</option>
            </select><br>
            <?php echo _('Explicit Podcast'); ?>:<br>
            <input type="radio" name="explicit" value="yes"> <?php echo _('Yes'); ?> <input type="radio" name="explicit" value="no" checked> <?php echo _('No'); ?><br>
            <br>
            <input type="submit" value="<?php echo _("Submit") ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>