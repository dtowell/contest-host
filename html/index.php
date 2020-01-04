<?php

$auth_required = 'anyone';
include "header.php";

?>

<h1><?=$contest->name?></h1>

<h1>Welcome to the contest!</h1>

<h2>Contest Links</h2>
<a href=view_file.php?file=rules.html>Rules</a><br>
<a href=view_problems.php>Problems</a><br>
<a href=view_standings.php>Standings</a><br>
<br>

<h2>Documentation</h2>
<?php if (in_array('java',$contest->languages)) : ?><a href=docs/java/api/     >Java Docs  </a><br><?php endif; ?>
<?php if (in_array('c',   $contest->languages)) : ?><a href=docs/c             >C Docs     </a><br><?php endif; ?>
<?php if (in_array('cpp', $contest->languages)) : ?><a href=docs/cpp           >C++ Docs   </a><br><?php endif; ?>
<?php if (in_array('php', $contest->languages)) : ?><a href=docs/php           >PHP Docs   </a><br><?php endif; ?>
<?php if (in_array('py',  $contest->languages)) : ?><a href=docs/python        >Python Docs</a><br><?php endif; ?>
<br>

<?php include "footer.php" ?>
