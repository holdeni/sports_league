<?php
// $Id: sched_seedTournament.php 195 2011-08-07 14:44:36Z Henry $
// Last Change: $Date: 2011-08-07 10:44:36 -0400 (Sun, 07 Aug 2011) $

?>

<h1>Determine Seeds For Tournament</h1>

<p>From here you can update the playoff schedule with the actual teams seeded according to their current standings.
</p>

<?php
echo form_open( "schedule/sched_standings/processConfirmation" );
echo "\n";
echo form_label( "Are you sure you wish to proceed with this action? ", "confirm" );
echo "\n";
$javaScript = "id='confirm' onChange='form.submit();'";
echo form_dropdown( "confirm", array( " " => " ", "NO" => "No, cancel action", "YES" => "Yes, proceed with seeding" ), " ", $javaScript );
echo "\n";
echo form_close();
?>
