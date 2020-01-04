<?php
if (!preg_match("/^[^?=]+\\.(html|jpg|png|gif)\$/",$_SERVER['REQUEST_URI']))
    return false;
header("Location: view_file.php?file=".substr($_SERVER['REQUEST_URI'],1));