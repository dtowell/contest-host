<?php

$auth_required = 'judge';
$auto_refresh = true;
$page_title = "Print Queue";
include "header.php";

$attempts = query_many("SELECT attempts.id,contestant,problem,number,language,result,
			strftime('%s',time) AS time,problems.name,problems.id as problem
        FROM attempts
        LEFT JOIN problems ON problem=problems.id
        WHERE language='txt'
        ORDER BY time DESC");
?>

<table class=form>
<tr class=line><th>Time</th><th>Minutes</th><th>Num</th><th>Name</th><th>Contestant</th><th>Action</th></tr>
<?php foreach ($attempts as $a) : ?>
    <tr>
    <td><?=format_time($a->time)?></td>
    <td><?=format_minutes($a->time)?></td>
    <td><?=$a->problem?></td>
    <td><a href='view_file.php?file=<?=$a->number?>.html'><?=$a->name?></a></td>
    <td><?=$a->contestant?></td>
    <td><a href='view_attempt.php?id=<?=$a->id?>'><?=$a->language?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include "footer.php" ?>
