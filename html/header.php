<?php

$current_time = time(); 
// to fake time use: mktime(h,m,s,m,d,y)
//$current_time = mktime(22,00,00,2,22,2011);

// each page should define the following:
//
// $page_title		optional, sets title and generates <h1>
// $auto_refresh	optional, true causes meta header to refresh page
// $auth_required	required, must be one of 'judge', 'contestant', 'anyone'

$db = null; // SQLite3 DB object

function redirect($url) {
	echo "<META HTTP-EQUIV='refresh' content='0;URL=$url'>";
	exit();
}

function query_or_die($query,$params=[]) {
	global $db;
	$stmt = $db->prepare($query);
	if (!$stmt) {
    	$dbg = debug_backtrace(false);
		//print_r($dbg);
        die(	"<h2>Query failed</h2>
        		 <P><b>File: </b>".$dbg[1]['file'].",".$dbg[1]['line']."
                 <p><b>Query: </b>$query</p>
                 <p><b>Values: </b>".serialize($params)."
                 <p><b>Error: </b>".$db->lastErrorMsg());
	}
	foreach ($params as $k=>$p)
		$stmt->bindValue($k+1,$p);
	$qr = $stmt->execute();
    if (!$qr) {
    	$dbg = debug_backtrace(false);
		//print_r($dbg);
        die(	"<h2>Query failed</h2>
        		 <P><b>File: </b>".$dbg[1]['file'].",".$dbg[1]['line']."
                 <p><b>Query: </b>$query</p>
                 <p><b>Values: </b>".serialize($params)."
                 <p><b>Error: </b>".$db->lastErrorMsg());
    }
	return $qr;
}

function query_many($query,$params=[]) {
	$qr = query_or_die($query,$params);
	$result = [];
	while ($row = $qr->fetchArray(SQLITE3_ASSOC))
		$result[] = (object)$row;
	return $result;
}

function query_one($query,$params=[]) {
	$qr = query_or_die($query,$params);
	$row = $qr->fetchArray(SQLITE3_ASSOC);
	return (object)$row;
}

function format_time($time) {
    if ($time == '')
        return '';
    return date("G:i:s",$time);
}

function format_minutes($time) {
    global $contest;
    return (int)(($time-$contest->start_time+59)/60);
}

function gen_select($name,$list,$default) {
	$result = "<select name=$name>";
	foreach ($list as $x)
		$result .= "<option value=$x->id".($x->id==$default?" selected":"").">$x->name</option>";
	$result .= "</select>";
	return $result; 
}

session_save_path('../sessions');
session_start();

if (!isset($auth_required)) die('$auth_required not set');
if (!in_array($auth_required,array('judge','contestant','anyone'))) die('invalid authentication requirement');

if ($auth_required!='anyone' && !isset($_SESSION['user_id']))
    redirect("login.php");
    
if ($auth_required=='judge' && !$_SESSION['judge'])
    redirect("/");

$db = new SQLite3('../contest.db');

$contest = query_one("SELECT id,name,path,languages,
	strftime('%s',start_time) AS start_time,
	strftime('%s',freeze_time) AS freeze_time,
	strftime('%s',stop_time) AS stop_time FROM contests WHERE live",[]);
$contest->languages = explode(",",trim($contest->languages,"{} ")); 

if (@$no_head) return;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
  <title><?=$contest->name?> <?=@$page_title?></title>
  <?php if (isset($auto_refresh) && $auto_refresh) : ?><meta http-equiv="refresh" content="60"><?php endif; ?>
</head>
<body>
<?php if (!empty($page_title)) : ?><H1 class=left><?=@$page_title?></h1><?php endif; ?>