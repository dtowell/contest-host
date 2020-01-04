<?php

$auth_required = 'judge';
include "header.php";

if (empty($_GET['id']))
    redirect("view_problems.php");

$assignment = query_one("SELECT name,number FROM problems WHERE number=?",array($_GET['id']));
$assignment->input = file_get_contents($contest->path."/in$assignment->number.txt");
$assignment->output = file_get_contents($contest->path."/out$assignment->number.txt");

?>

<H1>Test Input <?=$assignment->number?>: <?=$assignment->name?> </h1>
<pre><?=$assignment->input?></pre>

<H1>Expected Output <?=$assignment->number?>: <?=$assignment->name?> </h1>
<pre><?=$assignment->output?></pre>

<?php include "footer.php" ?>
