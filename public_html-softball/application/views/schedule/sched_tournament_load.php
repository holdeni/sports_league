<?php
// $Id: sched_tournament_load.php 195 2011-08-07 14:44:36Z Henry $
// Last Change: $Date: 2011-08-07 10:44:36 -0400 (Sun, 07 Aug 2011) $

/* ... get last modification date for bulk file */
$lastModified = stat( $this->config->item( 'my_rootDirectory' ).$this->config->item( 'bulkTournament' ) );
$fileModified = date( "Y-M-d g:i A", $lastModified[9] );
?>

<h1>Bulk Tournament Load</h1>

<p>From here you can update the database with a tournament's schedule. This action is destructive to the
   current tournament information stored in the database. The configuration file that will be read is <em>
   <?= $this->config->item( 'my_rootDirectory' ).$this->config->item( 'bulkTournament' ) ?> </em>
   (last updated on <em><?= $fileModified ?></em>).
</p>

<!--
<p><strong>IMPORTANT</strong>: The tournament will be read in and all year values will be set to <?= $this->config->item( 'thisYear' ) ?>.
   If you wish to use a different value for the year update the website configuration file before running this loader.
</p>
-->

<?php
echo form_open( "schedule/sched_tournament_load/processConfirmation" );
echo "\n";
echo form_label( "Are you sure you wish to proceed with this action? ", "confirm" );
echo "\n";
$javaScript = "id='confirm' onChange='form.submit();'";
echo form_dropdown( "confirm", array( " " => " ", "NO" => "No, cancel action", "YES" => "Yes, proceed with bulk load" ), " ", $javaScript );
echo "\n";
echo form_close();
?>
