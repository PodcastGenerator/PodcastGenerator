<?php
require "checkLogin.php";
require "../core/include_admin.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $config["podcast_title"]; ?> - Upload Episode</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
</head>

<body>
    <?php
    include "js.php";
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <h1>Upload Episode</h1>
        <form>
            <div class="row">
                <div class="col-6">
                    <h3>Main Informations</h3>
                    <hr>
                    <div class="form-group">
                        File*:<br>
                        <input type="file" name="file"><br>
                        <small>Your server configuration allows you to upload files up to <?php echo $config["max_upload_form_size"]; ?>. If your file is bigger or you have otehr problems use the FTP feature</small><br>
                    </div>
                    <div class="form-group">
                        Title*:<br>
                        <input type="text" name="title" class="form-control">
                    </div>
                    <div class="form-group">
                        Short Description*:<br>
                        <input type="text" id="shortdesc" name="shortdesc" class="form-control" maxlength="255" oninput="shortDescCheck()">
                        <i id="shortdesc_counter">255 characters remaining</i>
                    </div>
                    <div class="form-group">
                        Category*:<br>
                        <small>You can select up to 3 categories</small><br>
                        <select name="category" multiple>
                            <?php
                            $categories = simplexml_load_file("../categories.xml");
                            foreach ($categories as $item) {
                                echo "<option value=\"" . $item->id . "\">" . $item->description . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        Publication Date:<br>
                        <small>If you select a date in the future, it will be published then</small><br>
                        Date*:<br>
                        <input name="date" type="date" value="<?php echo date("Y-m-d"); ?>"><br>
                        Time*:<br>
                        <input name="time" type="time" value="<?php echo date("H:i"); ?>"><br>
                    </div>
                </div>
                <div class="col-6">
                    <h3>Extra Informations</h3>
                    <hr>
                    <div class="form-group">
                        Long Description:<br>
                        <textarea name="losgdesc"></textarea><br>
                    </div>
                    <div class="form-group">
                        iTunes Keywords:<br>
                        <input type="text" name="itunesKeywords" placeholder="Keyword1, Keyword2 (max 12)" class="form-control"><br>
                    </div>
                    <div class="form-group">
                        Explicit content:<br>
                        <input type="radio" value="yes" name="explicit"> Yes <input type="radio" value="no" name="explicit" checked> No<br>
                    </div>
                    <div class="form-group">
                        Author*:<br>
                        <input type="text" class="form-control" name="authorname" placeholder="Author Name" value="<?php echo $config["author_name"]; ?>"><br>
                        <input type="email" class="form-control" name="authoremail" placeholder="Author E-Mail" value="<?php echo $config["author_email"]; ?>"><br>
                    </div>
                    <input type="submit" class="btn btn-success btn-lg" value="Upload episode">
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        function shortDescCheck() {
            let shortdesc = document.getElementById("shortdesc").value;
            let maxlength = 255;
            let counter = document.getElementById("shortdesc_counter").innerText = (maxlength - shortdesc.length) + " characters remaining";
        }
    </script>
</body>

</html>