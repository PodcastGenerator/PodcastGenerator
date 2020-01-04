<?php
function getmime($filename)
{
    // Check if file is even readable
    if(!is_readable($filename))
        return false;
    return mime_content_type($filename);
}