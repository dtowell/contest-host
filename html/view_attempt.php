<?php

$auth_required = 'judge';
include "header.php";

if (!isset($_GET['id']))
    redirect("view_problems.php");
    
$attempt = query_one("SELECT contestant,code FROM attempts WHERE id=?",array($_GET['id']));

?>

<h1><?=$attempt->contestant?></h1>
<pre><?=htmlentities($attempt->code)?></pre>

<?php include "footer.php" ?>
