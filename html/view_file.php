<?php

$auth_required = 'anyone';
$no_head = true;
include "header.php";

if (empty($_GET['file']) || (!$_SESSION['judge'] && !preg_match('/\.(html|png|gif|jpg)$/',$_GET['file'])))
    redirect("view_problems.php");

if (isset($_GET['pre'])) echo "<pre>";
echo file_get_contents("$contest->path/$_GET[file]");
if (isset($_GET['pre'])) echo "</pre>";