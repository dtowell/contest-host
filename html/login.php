<?php

$auth_required = 'anyone';
$page_title = "Login";
include "header.php";

$error = "";

if (isset($_POST['login'])) {
    if (!isset($_POST['user']))
        $error .= "Contestant name blank.<br>";
    if (!isset($_POST['user']))
        $error .= "Contestant password blank.<br>";

    if ($error == "") {
        $qr = query_or_die("SELECT id,judge FROM contestants WHERE name=? AND password=?",
            [$_POST['user'],$_POST['password']]);
        $user = $qr->fetchArray(SQLITE3_ASSOC);
        if (empty($user))
            $error .= "Contestant name and/or password incorrect.<br>";
        else {
            query_or_die("UPDATE contestants SET last_login=datetime('now') WHERE name=?",[$_POST['user']]);
        	$_SESSION['username'] = $_POST['user'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['judge'] = $user['judge'];
            redirect("view_problems.php");
        }
    }
}

?>

<font color=red><?=$error?></font>

<form action=login.php method=post>
<table class=form>
<tr><th>Team ID:</th><td><input type=text name=user></td></tr>
<tr><th>Password:</th><td><input type=password name=password></td></tr>
<tr><th>&nbsp;</th><td><input type=submit name=login value='Login'></td></tr>
</table>
</form>

<?php include "footer.php" ?>
