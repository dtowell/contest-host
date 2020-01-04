<?php

$auth_required = 'contestant';
$page_title = "Submit Solution";
include "header.php";

if (empty($_GET['id']))
    redirect("view_problems.php");

$problem = query_one("SELECT id,number,name,
			(SELECT count(*) FROM attempts WHERE problem=p.id AND contestant=?) AS count,contest
        FROM problems p WHERE id=?",[$_SESSION['user_id'],$_GET['id']]);

if (!$_SESSION['judge'] && $problem->contest != $contest->id)
    redirect("view_problems.php");

$error_msg = "";

if (isset($_POST['submit'])) {
    if (empty($_POST['language']))
        $error_msg .= "Language not specified.<br>";
        
    if ($_FILES["file"]["error"] == 4)
        $error_msg .= "No file provided.<br>";
    else if ($_FILES["file"]["error"] != 0)
	   $error_msg .= "File upload error.<br>";
    else if ($_FILES["file"]["size"] > 30000)
	   $error_msg .= "File is too long.<br>";
    //else if ($_FILES["file"]["size"] < 100)
	//   $error_msg .= "File is too small.<br>";
	else if (strpos($_FILES["file"]["name"],' ')!==false)
	   $error_msg .= "Filename contains a space.<br>"; 

    if ($error_msg == "") {
        $file_path = "/var/www/contests/workdir/{$problem->number}_$_SESSION[username]_$problem->count.$_POST[language]"; 
        if (!move_uploaded_file($_FILES['file']['tmp_name'],$file_path))
    	   $error_msg .= "File could not be moved after uploading.<br>\n".print_r($_FILES,true)."\n$file_path <br>\n";;
    }
    
    if ($error_msg == "") {
        query_or_die("INSERT INTO attempts (contestant,problem,time,code,savepath,filename,language,result) 
			VALUES (?,?,?.?,?,?,?,'submitted')",
            array($_SESSION['user_id'],$_GET['id'],date("Y-m-d H:i:s",$current_time),
			file_get_contents($file_path),$file_path,$_FILES["file"]["name"],$_POST['language']));
        redirect("view_problems.php");
    }
}

$attempts = query_many("SELECT result,language, extract(epoch from time) AS unixtime FROM attempts
        WHERE problem=? AND contestant=? ORDER BY time",array($_GET['id'],$_SESSION['user_id']));

$notes = query_many("SELECT note FROM notes WHERE problem=?",array($_GET['id']));
        
$extentions = array(
	'java'	=> 'Java',
	'c'		=> 'C',
	'cpp'	=> 'C++',
	'php'	=> 'PHP',
	'py'    => 'Python',
    'txt'	=> 'Print',
    'q'     => 'Clarification');

?>

<form enctype="multipart/form-data" action=submit_attempt.php?id=<?=$_GET['id']?> method=post>
<table class=form>
    <tr><th>Contestant:     </th><td><?=$_SESSION['username']?></td></tr>
    <tr><th>Number:         </th><td><?=$problem->number?></td></tr>
    <tr><th>Name:           </th><td><?=$problem->name?></td></tr>
    <tr><th>Current time:   </th><td><?=format_time($current_time)?></td></tr>
    <tr><th>Minutes:        </th><td><?=format_minutes($current_time)?></td></tr>
    <tr><th>Language:       </th><td><select name=language>
		<option selected></option><?php foreach ($contest->languages as $l) : ?><option value=<?=$l?>><?=$extentions[$l]?></option><?php endforeach; ?>
		</select></td></tr>
    <tr><th>Source code:    </th><td><input type=file name=file size=45></td></tr>
    <?php if ($error_msg != ""): ?>
        <tr><td>            </td><td><font color=red><?=$error_msg ?></font></td>
    <?php endif; ?>
    <tr><th>                </th><td><input type=submit name=submit value=Submit></td></tr>

</table>
</form>

<?php if (count($notes)>0): ?>
  <p><b>Announcements:</b></p>
  <?php foreach ($notes as $n) echo $n->note."<br>"; ?>
  <br><br>
<?php endif; ?>

<table class=form>
<tr class=line><th>Time</th><th>Minutes</th><th>Language</th><th>Result</th></tr>
<?php foreach ($attempts as $a): ?>
    <tr>
    <td><?=format_time($a->unixtime)?></td>
    <td><?=format_minutes($a->unixtime)?></td>
    <td><?=$a->language?></td>
    <td><?=$a->result?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php if (count($attempts) == 0): ?>
    <p> no attempts</p>
<?php endif; ?>

<?php include "footer.php" ?>
