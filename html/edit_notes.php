<?php

$auth_required = 'judge';
$page_title = "Edit Notes";
include "header.php";

if (isset($_POST['submit'])) {
    if ($_GET['id']==="") {
        $_GET['id'] = query_insert("INSERT INTO notes (contest,problem,note) VALUES (?,(SELECT id FROM problems WHERE number=? AND contest=?),?)",
            [$_POST['contest'],(empty($_POST['number'])?null:$_POST['number']),$_POST['contest'],$_POST['note']]);
    }
    else {
        query_or_die("UPDATE notes SET contest=?, problem=(SELECT id FROM problems WHERE number=? AND contest=?), note=? WHERE id=?",
            [$_POST['contest'],(empty($_POST['number'])?null:$_POST['number']),$_POST['contest'],$_POST['note'],$_POST['id']]);
    }
}
else if (isset($_POST['delete']) && !empty($_GET['id'])) {
    query_or_die("DELETE FROM notes WHERE id=?",array($_GET['id']));
    redirect("edit_notes.php");
}

if (!empty($_GET['id'])) {
    $note = query_one("SELECT *,(SELECT number FROM problems WHERE problem=problems.id) AS number FROM notes WHERE id=?",array($_GET['id']));
    foreach ($note as $k=>$v)
        $_POST[$k]=$v;
}

$ids = query_many("SELECT id FROM notes ORDER BY id",array());

$contests = query_many("SELECT id,name FROM contests ORDER BY id",array());

?>

<?php foreach ($ids as $i): ?>
    <a href=edit_notes.php?id=<?=$i->id?>><?=$i->id?></a>
<?php endforeach; ?>
<a href=edit_notes.php>new</a>
<br>

<form action=edit_notes.php?id=<?=@$_GET['id']?> method=post>
<table class=form>
    <tr><th>Contest:  </th><td><?=gen_select('contest',$contests,@$_POST['contest'])?></td></tr>
    <tr><th>Problem:  </th><td><input type=text size=80 name=number   value='<?=@$_POST['number']?>' ></td></tr>
    <tr><th>Message:  </th><td><textarea rows=15 cols=80 name=note><?=@$_POST['note']?></textarea></td></tr>
    <tr><th>          </th><td><input type=submit name=submit value=Submit> <input type=submit name=delete value=Delete></td></tr>
</table>
</form>

<?php include "footer.php" ?>
