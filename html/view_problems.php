<?php

$auth_required = 'contestant';
$auto_refresh = true;
$page_title = "Problems";
include "header.php";

$problems = query_many("SELECT p.id,number,p.name,
        (SELECT count(*) FROM attempts WHERE problem=p.id AND contestant=? AND NOT language='txt') AS attempts,
        (SELECT result FROM attempts WHERE problem=p.id AND contestant=? AND NOT language='txt' ORDER BY time DESC LIMIT 1) AS status
        FROM problems p
        LEFT JOIN contests c ON c.id=contest
        WHERE live 
        ORDER BY number",[$_SESSION['user_id'],$_SESSION['user_id']]);

$notes = query_many("SELECT problems.id,number,problems.name,note 
        FROM notes 
        LEFT JOIN problems ON problem=problems.id 
        LEFT JOIN contests ON notes.contest=contests.id 
        WHERE live",array());
?>

<table class=form>
<tr><th>Contestant:</th><td><?=$_SESSION['username']?></td></tr>
<tr><th>Current time:</th><td><?=format_time($current_time)?></td></tr>
<tr><th>Minutes:</th><td><?=format_minutes($current_time)?></td></tr>
</table>

<br><br>
<table class=form>
<tr class=line><th>Num</th><th>Name</th><th>Attempts</th><th>Status</th><th>Action</th></tr>
<?php foreach ($problems as $p): ?>
    <tr><td><?=$p->number?></td>
    <td><a href='<?=$p->number?>.html'><?=$p->name?></a></td>
    <td style='text-align:right;'><?=$p->attempts?></td>
    <td><?=$p->status?></td>
    <td><a href=submit_attempt.php?id=<?=$p->id?>>submit</span></td>
<?php endforeach; ?>
</table>

<br><br>
<p><b>Announcements:</b></p>
<?php foreach ($notes as $n): ?>
    <?php if ($n->id!=""): ?>
    	<a href='<?=$n->number?>.html'><b>Problem <?=$n->number?>: <?=$n->name?></b></a>
    <?php endif; ?>
    <?=$n->note?><br>
<?php endforeach; ?>

<?php include "footer.php" ?>
