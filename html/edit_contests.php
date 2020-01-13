<?php

$auth_required = 'judge';
$page_title = "Edit Contests";
include "header.php";

if (isset($_POST['submit'])) {
    if ($_GET['id']==="") {
        $_GET['id'] = query_insert("INSERT INTO contests (id,name,path,live,start_time,freeze_time,stop_time,languages) 
			VALUES (default,?,?,?,?,?,?,?)",
            [$_POST['name'],$_POST['path'],$_POST['live']?'t':'f',$_POST['start_time'],
			$_POST['freeze_time'],$_POST['stop_time'],$_POST['languages']]);
    }
    else {
        query_or_die("UPDATE contests SET name=?,path=?,live=?,start_time=?,
			freeze_time=?,stop_time=?,languages=? WHERE id=?",
            array($_POST['name'],$_POST['path'],$_POST['live']?'t':'f',$_POST['start_time'],
			$_POST['freeze_time'],$_POST['stop_time'],$_POST['languages'],$_POST['id']));
    }
}
else if (isset($_POST['delete']) && !empty($_GET['id'])) {
    query_or_die("DELETE FROM contests WHERE id=?",array($_GET['id']));
    redirect("edit_contests.php");
}
else if (isset($_POST['delete_attempts']) && !empty($_GET['id'])) {
    query_or_die("DELETE FROM attempts WHERE problem IN (SELECT id FROM problems WHERE contest IN (SELECT id FROM contests WHERE id=?))",array($_GET['id']));
    redirect("edit_contests.php");
}

if (!empty($_GET['id'])) {
    $contest = query_one("SELECT * FROM contests WHERE id=?",array($_GET['id']));
    foreach ($contest as $k=>$v)
        $_POST[$k]=$v;
    $_POST['live'] = $_POST['live']=='t';
}

$ids = query_many("SELECT id FROM contests ORDER BY id",array());

?>

<?php foreach ($ids as $i): ?>
    <a href=edit_contests.php?id=<?=$i->id?>><?=$i->id?></a>
<?php endforeach; ?>
<a href=edit_contests.php>new</a>
<br>

<form action=edit_contests.php?id=<?=@$_GET['id']?> method=post>
<table class=form>
    <tr><th>Name:        </th><td><input type=text size=80 name=name        value='<?=@$_POST['name']?>'        ></td></tr>
    <tr><th>Path:        </th><td><input type=text size=80 name=path        value='<?=@$_POST['path']?>'        ></td></tr>
    <tr><th>Live:        </th><td><input type=checkbox     name=live 	    value=1 <?=@$_POST['live']?'checked':''?>></td></tr>
    <tr><th>Start time:  </th><td><input type=text size=40 name=start_time  value='<?=@$_POST['start_time']?>'  > YYYY-MM-DD HH:MM</td></tr>
    <tr><th>Freeze time: </th><td><input type=text size=40 name=freeze_time value='<?=@$_POST['freeze_time']?>' ></td></tr>
    <tr><th>Stop time:   </th><td><input type=text size=40 name=stop_time   value='<?=@$_POST['stop_time']?>'   ></td></tr>
    <tr><th>Languages:   </th><td><input type=text size=40 name=languages   value='<?=@$_POST['languages']?>'   > e.g., {java,cpp,c}</td></tr>
    <tr><th>             </th><td><input type=submit name=submit value=Submit> <input type=submit name=delete value=Delete> <input type=submit name=delete_attempts value='Delete ALL attempts for this contest'></td></tr>
</table>
</form>

<?php include "footer.php" ?>
