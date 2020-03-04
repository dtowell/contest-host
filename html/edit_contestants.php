<?php

$auth_required = 'judge';
$page_title = "Contestants";
include "header.php";

if (isset($_POST['submit'])) {
    if ($_GET['id']==="") {
        $_GET['id'] = query_insert("INSERT INTO contestants (name,password,judge) VALUES (?,?,?)",
            [$_POST['name'],$_POST['password'],(int)isset($_POST['judge'])]);
    }
    else {
        query_or_die("UPDATE contestants SET name=?, password=?, judge=? WHERE id=?",
            [$_POST['name'],$_POST['password'],(int)isset($_POST['judge']),$_GET['id']]);
    }
}
else if (isset($_POST['delete']) && !empty($_GET['id'])) {
    query_or_die("DELETE FROM contestants WHERE id=?",[$_GET['id']]);
    redirect("edit_contestants.php");
}
else if (isset($_POST['reset_last_login'])) {
    query_or_die("UPDATE contestants SET last_login=null");
    redirect("edit_contestants.php");
}

if (!empty($_GET['id'])) {
    $contestant = query_one("SELECT * FROM contestants WHERE id=?",[$_GET['id']]);
    foreach ($contestant as $k=>$v)
        $_POST[$k]=$v;
}

$ids = query_many("SELECT id FROM contestants ORDER BY id",array());

?>

<?php foreach ($ids as $i): ?>
    <a href=edit_contestants.php?id=<?=$i->id?>><?=$i->id?></a>
<?php endforeach; ?>
<a href=edit_contestants.php>new</a>
<br>

<form action=edit_contestants.php?id=<?=@$_GET['id']?> method=post>
<table class=form>
    <tr><th>Name:              </th><td><input type=text size=80 name=name      value='<?=@$_POST['name']?>'     ></td></tr>
    <tr><th>Password:          </th><td><input type=text size=80 name=password  value='<?=@$_POST['password']?>' ></td></tr>
    <tr><th>Judge:             </th><td><input type=checkbox name=judge value=1 <?=@$_POST['judge']?"checked":""?>></td></tr>
    <tr><th>Last login:        </th><td><?=@$_POST['last_login']?></td></tr>
    <tr><th>                   </th><td><input type=submit name=submit value=Submit> <input type=submit name=delete value=Delete> <input type=submit name=reset_last_login value='Reset ALL last login times'></td></tr>
</table>
</form>

<?php include "footer.php" ?>
