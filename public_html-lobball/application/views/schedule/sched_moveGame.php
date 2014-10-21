<?php
// $Id: sched_moveGame.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

/* ... data declarations */
$newDetails = array(
   "oldGameID" => $oldGameDetails['GameID'],
   "newGameID" => $newGameDetails['GameID'],
 );
?>

<h1>Moving Game To New Day And Time</h1>

<div id="rescheduling-old">
<h3>Original Game Details</h3>

<table>
   <tr>
      <th>Date</th>
      <td><?= $oldGameDetails['Date'] ?></td>
   </tr>
   <tr>
      <th>Time</th>
      <td><?= $oldGameDetails['Time'] ?></td>
   </tr>
   <tr>
      <th>Diamond</th>
      <td><?= $oldGameDetails['Diamond'] ?></td>
   </tr>
   <tr>
      <th>Visitors</th>
      <td><?= htmlspecialchars( $this->Model_Team->getTeamName( $oldGameDetails['VisitTeamID'] ) ) ?></td>
   </tr>
   <tr>
      <th>Home</th>
      <td><?= htmlspecialchars( $this->Model_Team->getTeamName( $oldGameDetails['HomeTeamID'] ) ) ?></td>
   </tr>

</table>
</div>

<div id="rescheduling-new">
<h3>New Date</h3>


<table>
   <tr>
      <th>Date</th>
      <td><?= $newGameDetails['Date'] ?></td>
   </tr>
   <tr>
      <th>Time</th>
      <td><?= $newGameDetails['Time'] ?></td>
   </tr>
   <tr>
      <th>Diamond</th>
      <td><?= $newGameDetails['Diamond'] ?></td>
   </tr>
</table>
</div>

<?php
echo form_open( "schedule/sched_rainouts/processConfirmation" );
echo form_hidden( $newDetails );
echo "\n";
echo form_label( "Are you sure you wish to proceed with this action? ", "confirm" );
echo "\n";
$javaScript = "id='confirm' onChange='form.submit();'";
echo form_dropdown( "confirm", array( " " => " ", "NO" => "No, cancel action", "YES" => "Yes, proceed with moving game" ), " ", $javaScript );
echo "\n";
echo form_close();
?>
