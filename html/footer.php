<?php if ($auth_required=='contestant') : ?>
<br>
<b>Other options</b><br>
<a href=/>contest home and documentation</a><br>
<a href=view_standings.php>Standings</a><br>
<a href=view_problems.php>Problems</a><br>
<?php endif; ?>

<?php if (isset($auto_refresh) && $auto_refresh) :?>
<br><br>
<p><small>This page should automatically refresh.</small></p>
<?php endif; ?>

<?php if (@$_SESSION['judge']) : ?>
<br>
<b>Judge Menu</b><br>
<a href=/>contest home</a><br>
<a href=view_attempts.php>view attempts</a><br>
<a href=view_standings.php>view standings</a><br>
<a href=view_prints.php>view prints</a><br>
<a href=view_problems.php>view problems</a><br>
<a href=edit_problems.php>edit problems</a><br>
<a href=edit_notes.php>edit notes</a><br>
<a href=edit_contestants.php>edit contestants</a><br>
<a href=edit_contests.php>edit contests</a><br>
<?php endif; ?>

</body></html>
