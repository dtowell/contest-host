<?php

$auth_required = 'judge';
$page_title = "Judge Submission";
include "header.php";

if (!isset($_GET['id']))
    redirect("view_problems.php");
    
if (isset($_POST['accept'])) {
    query_or_die("UPDATE attempts SET result='accepted' WHERE id=?",array($_GET['id']));
    redirect("view_attempts.php");
}

if (isset($_POST['reject']) && !empty($_POST['reason'])) {
    query_or_die("UPDATE attempts SET result='rejected: '||? WHERE id=?",[$_POST['reason'],$_GET['id']]);
    redirect("view_attempts.php");
}

function execute($cwd,$program,$stdin,&$stdout,&$stderr) {
    $descriptorspec = array(
       0 => array("pipe", "r"),  // stdin
       1 => array("pipe", "w"),  // stdout
       2 => array("pipe", "w")); // stderr

    $process = proc_open($program,$descriptorspec,$pipes,$cwd);
    if (!is_resource($process)) {
        $stderr = 'execution error: process could not be opened';
        return false;
    }
    // echo "execute($cwd,$program,$stdin)<br>";

    fwrite($pipes[0], $stdin);
    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1],200000);
    fclose($pipes[1]);

    $stderr = stream_get_contents($pipes[2],200000);
    fclose($pipes[2]);

    proc_close($process);
    return true;
}

$attempt = query_one("SELECT problem,c.name AS contestant,language,code,filename,
			extract(epoch from time) AS unixtime,p.number,p.name,savepath,contests.path
        FROM attempts 
		LEFT JOIN problems p ON problem=p.id
		LEFT JOIN contestants c ON contestant=c.id
        LEFT JOIN contests ON contests.id=contest
		WHERE attempts.id=?",array($_GET['id']));

?>

<table class=form>
<tr><th>Number:      </th><td><?=$attempt->number?></td></tr>
<tr><th>Name:        </th><td><?=$attempt->name?></td></tr>
<tr><th>Submit time: </th><td><?=format_time($attempt->unixtime)?></td></tr>
<tr><th>Contestant:  </th><td><?=$attempt->contestant?></td></tr>
<tr><th>Language:    </th><td><?=$attempt->language?></td></tr>
<tr><th>Filename:    </th><td><?=$attempt->filename?></td></tr>
<tr><th>Archive:     </th><td><?=$attempt->savepath?></td></tr>
</table>
<br>

<?php

flush();

$work_path = "/var/www/contests/workdir/$_GET[id]";
mkdir($work_path);
file_put_contents("$work_path/$attempt->filename",$attempt->code);

if ($attempt->language == 'cpp') {
    execute($work_path,"/usr/bin/g++ \"$attempt->filename\"","",$stdout,$stderr);
    if ($stderr == "")
        $output = $stdout;
    else
        $output = "stderr: \n$stderr\nstdout: $stdout";
}
else if ($attempt->language == 'c') {
    execute($work_path,"/usr/bin/gcc -lm \"$attempt->filename\"","",$stdout,$stderr);
    if ($stderr == "")
        $output = $stdout;
    else
        $output = "stderr: \n$stderr\nstdout: $stdout";
}
else if ($attempt->language == 'java') {
    execute($work_path,"/usr/bin/javac \"$attempt->filename\"","",$stdout,$stderr);
    if ($stderr == "")
        $output = $stdout;
    else
        $output = "stderr: \n$stderr\nstdout: $stdout";
}
else if ($attempt->language == 'php') {
    execute($work_path,"php -l \"$attempt->filename\"","",$stdout,$stderr);
    if (stripos($stdout,'No syntax errors detected')!==false)
        $stdout = '';
    if ($stderr == "")
        $output = $stdout;
    else
        $output = "stderr: \n$stderr\nstdout: $stdout";
}
else if ($attempt->language == 'py')
    ; // nothing to do
else
    $output = 'Undefined compile process, panic!';

// save compile results
query_or_die("UPDATE attempts SET compile_output=? WHERE id=?",array($output,$_GET['id']));

if ($output != '')
    echo "<b>Compile results</b><pre class=file>$output</pre>";

$input = file_get_contents("$attempt->path/in$attempt->number.txt");
$output = file_get_contents("$attempt->path/out$attempt->number.txt");

flush();
if ($attempt->language == 'cpp' || $attempt->language == 'c')
    execute($work_path,"ulimit -t 10; ./a.out",$input,$stdout,$stderr);
else if ($attempt->language == 'java')
    execute($work_path,"ulimit -t 10; java ".substr($attempt->filename,0,strrpos($attempt->filename,'.')),$input,$stdout,$stderr);
else if ($attempt->language == 'php')
    execute($work_path,"ulimit -t 10; php \"$attempt->filename\"",$input,$stdout,$stderr);
else if ($attempt->language == 'py')
    execute($work_path,"ulimit -t 10; python3 \"$attempt->filename\"",$input,$stdout,$stderr);
else
    $output = 'Undefined execution process, panic!';

// save execution results
query_or_die("UPDATE attempts SET run_output=?, run_errors=? WHERE id=?",array($output,$stderr,$_GET['id']));

if ($stderr != '')
    echo "<b>Execution errors:</b><pre class=file>$stderr</pre>";

// delete working copies and directory
foreach(glob("$work_path/*") as $fn)
    unlink($fn);
rmdir($work_path);


if ($stdout !== $output) {
    echo "<table><tr><th>Contestant output</th><th>Judge output</th></tr>";
    echo "<tr><td>".strlen($stdout)."</td><td>".strlen($output)."</td>";
    echo "<tr><td><pre class=file><span style='background-color:#EEE;'>$stdout</span></pre></td><td><pre class=file><span style='background-color:#EEE;'>$output</span></pre></td>";
    echo "</tr></table>";
}
else
    echo "<p><b>Output was identical.</b></p>";

?>

<p><b>Judgement</b></p>

<table><tr><td>

<form action=test_attempt.php?id=<?=$_GET['id']?> method=post>
<input type=submit name=accept value=Accept>
</form>

</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>

<form action=test_attempt.php?id=<?=$_GET['id']?> method=post>
<input type=submit name=reject value='Reject :'>
<input type=text name=reason>
</form>

</td></tr></table>

<?php include "footer.php" ?>
