<?php

$auth_required = 'judge';
$page_title = "Edit Problem";
include "header.php";

if (isset($_POST['submit'])) {
    if ($_GET['id']==="") {
		$_GET['id'] = query_insert("INSERT INTO problems (contest,number,name) VALUES (?,?,?)",[$_POST['contest'],$_POST['number'],$_POST['name']]);
    }
    else {
        query_or_die("UPDATE problems SET contest=?, name=? WHERE id=?",[$_POST['contest'],$_POST['name'],$_GET['id']]); 
    }
}
else if (isset($_POST['delete']) && !empty($_GET['id'])) {
    query_or_die("DELETE FROM problems WHERE id=?",array($_GET['id']));
    redirect("edit_problems.php");
}

if (!empty($_GET['id'])) {
    $problem = query_one("SELECT p.*,c.path FROM problems p LEFT JOIN contests c ON c.id=contest WHERE p.id=?",array($_GET['id']));
    foreach ($problem as $k=>$v)
        $_POST[$k]=$v;
}

$ids = query_many("SELECT p.id,c.name||' '||number AS name FROM problems p LEFT JOIN contests c ON contest=c.id ORDER BY contest,number");

$contests = query_many("SELECT id,name FROM contests ORDER BY id");

?>

<?php foreach ($ids as $i): ?>
    <a href=edit_problems.php?id=<?=$i->id?>><?=$i->name?></a>
<?php endforeach; ?>
<a href=edit_problems.php>new</a>
<br>

<form action=edit_problems.php?id=<?=@$_GET['id']?> method=post>
<table class=form>
    <tr><th>Contest:     </th><td><?=gen_select('contest',$contests,@$_POST['contest'])?></td></tr>
    <tr><th>Number:      </th><td><input type=text         name=number value='<?=@$_POST['number']?>' ></td></tr>
    <tr><th>Name:        </th><td><input type=text size=80 name=name   value='<?=@$_POST['name']?>'   ></td></tr>
    <tr><th>             </th><td><input type=submit name=submit value=Submit> <input type=submit name=delete value=Delete></td></tr>
    <tr><th>Description: </th><td><a href=view_file.php?file=<?=@$_POST['number']?>.html><?=@$_POST['path']?>/<?=@$_POST['number']?>.html</a></td></tr>
    <tr><th>Input:       </th><td><a href=view_data.php?id=<?=@$_POST['number']?>><?=@$_POST['path']?>/in<?=@$_POST['number']?>.txt </a></td></tr>
    <tr><th>Output:      </th><td><a href=view_data.php?id=<?=@$_POST['number']?>><?=@$_POST['path']?>/out<?=@$_POST['number']?>.txt </a></td></tr>
</table>
</form>

<?php include "footer.php" ?>
