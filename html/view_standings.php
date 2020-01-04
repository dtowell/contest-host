<?php

$auth_required = 'anyone';
$auto_refresh = true;
$page_title = "Standings";
include "header.php";

$limit = "";
if (!$_SESSION['judge'])
    $limit = " AND time < (SELECT min(freeze_time) FROM contests WHERE live)";

$standings = query_many("SELECT
            p.id,p.number,p.name AS problem,c.name AS contestant,
            (SELECT count(*) FROM attempts WHERE problem=p.id AND contestant=c.id AND NOT result='submitted' AND NOT language IN ('txt','q') $limit) AS attempts,
            (SELECT min(strftime('%s',time)) FROM attempts WHERE problem=p.id AND contestant=c.id AND result='accepted' $limit) AS time
        FROM problems p,contestants c,contests
        WHERE NOT judge AND live AND contest=contests.id
        ORDER BY number,c.name",[]);

foreach ($standings as $s) {
    $contestants[$s->contestant] = $s->contestant;
    $problems[$s->problem]->number = $s->problem;
    $problems[$s->problem]->url = "/view_file.php?file=$s->number.html";
    $attempts[$s->contestant][$s->problem] = $s->attempts;
    @$attempts[$s->contestant]['Total'] += $s->attempts;
    if (empty($s->time))
        $x = "-";
    else {
        $x = (int)(($s->time-$contest->start_time)/60) + ($s->attempts-1)*20;
        @$right[$s->contestant]++;
    }
    $score[$s->contestant][$s->problem] = $x;
    @$score[$s->contestant]['Total'] += $x;
}
$problems['Total']->number = "Totals";
$problems['Total']->url = "/view_standings.php";

function cmp($a,$b) {
    global $right,$score;
    if (@$right[$a] == @$right[$b])
        return (int)$score[$a]['Total'] - (int)$score[$b]['Total'];
    return @$right[$b] - @$right[$a];
}

@uasort($contestants,"cmp");
?>

<table class=form>
<tr><th>Current time:</th><td><?=format_time($current_time)?></td></tr>
<tr><th>Minutes:</th><td><?=($current_time<$contest->freeze_time)?format_minutes($current_time):'scoreboard frozen'?></td></tr>
</table>

<br><br>
<table class=form>
<tr><th></th>
    <?php foreach ($problems as $p=>$n): ?>
        <th colspan=2 style="text-align:center;"><a href='<?=$n->url?>' title='<?=$p?>'><?=$n->number?></a></th>
    <?php endforeach; ?>
    <th></th>
</tr>
<tr class=line><th>Team</th>
    <?php foreach ($problems as $p=>$n): ?>
        <th><small>tries</small></th><th><small>mins</small></th>
    <?php endforeach; ?>
    <th>Accepted</th>
</tr>
<?php foreach ($contestants as $c=>$x): ?>
    <tr>
    <td><nobr><?=$c?></nobr></td>
    <?php foreach ($problems as $p=>$x): ?>
        <td><?=@$attempts[$c][$p]?></td><td><?=@$score[$c][$p]?></td>
    <?php endforeach; ?>
    <td><?=@$right[$c]?></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include "footer.php" ?>

