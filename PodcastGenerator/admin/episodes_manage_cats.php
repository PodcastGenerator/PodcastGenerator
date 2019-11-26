<?php
require "checkLogin.php";
require "../core/include_admin.php";

// If episode is deleted
if(isset($_GET["del"])) {
    $cats_xml = simplexml_load_file("../categories.xml");
    // Get index of item
    foreach($cats_xml as $item) {
        if($item->id == $_GET["del"]) {
            // Delete the actual node
            $dom = dom_import_simplexml($item);
            $dom->parentNode->removeChild($dom);
        }
    }
    // Write to file
    $cats_xml->asXML("../categories.xml");
    header("Location: episodes_manage_cats.php");
}
// If episode is added
if(isset($_GET["add"])) {
    $cats_xml = simplexml_load_file("../categories.xml");
    $description = $_POST["categoryname"];
    $id = strtolower(str_replace(" " , "_" , $description));
    // Check if this episode already exists
    foreach($cats_xml as $item) {
        if($item->id == $id) {
            $error = "Category already exists";
        }
    }
    if(!isset($error)) {
        $cats_xml->addChild("category");
        // Check for an empty item (which is the last)
        foreach($cats_xml as $item) {
            if(!isset($item->id) && !isset($item->description)) {
                $item->addChild("id", $id);
                $item->addChild("description", $description);
                break;
            }
        }
    }
    $cats_xml->asXML("../categories.xml");
    header("Location: episodes_manage_cats.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($config["podcast_title"]); ?> - Admin</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>

<body>
    <?php
    include "js.php";
    include "navbar.php";
    ?>
    <br>
    <div class="container">
        <h1>Manage categories</h1>
        <h3>Add category</h3>
        <form action="episodes_manage_cats.php?add=1" method="POST">
            Category Name:<br>
            <input type="text" name="categoryname" placeholder="Category Name"><br><br>
            <input type="submit" value="Add" class="btn btn-success"><br><br>
        </form>
        <h3>Current Categories</h3>
        <ul>
            <?php
            $cats_xml = simplexml_load_file("../categories.xml");
            foreach ($cats_xml as $item) {
                echo "<li><a href=\"" . htmlspecialchars($config["url"]) . "index.php?cat=" . htmlspecialchars($item->id) . "\">" . htmlspecialchars($item->description) . "</a> <a class=\"btn btn-sm btn-danger\" href=\"episodes_manage_cats.php?del=" . htmlspecialchars($item->id) . "\">Delete</a></li>";
            }
            ?>
        </ul>
    </div>
</body>

</html>