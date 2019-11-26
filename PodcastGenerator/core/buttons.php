<?php
function getButtons($path = "../") {
    return simplexml_load_file($path . "buttons.xml");
}