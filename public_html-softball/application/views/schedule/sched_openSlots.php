<?php
// $Id: sched_openSlots.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<h1><?= $scheduleHeader ?> (As of: <?= $todaysDate ?>)</h1>

<div id="schedule">

   <div id="scheduleTable">
   <br /> <br />
   <table border="border" width="35%">
      
      <tr>
         <th>Date</th>
         <th>Time</th>
         <th>Diamond</th>
      </tr>
   
<?php
/* ... cycle through the schedule and display all our entries */
   $lastGameDate = "";
   for ($i = 0; $i < count( $schedDetails ); $i++) {
      $gameDate = date( "D, M d Y", strtotime( $schedDetails[$i]['Date'] ) );
      $newGameDate = $gameDate != $lastGameDate ? true : false;
?>
      <tr class="<?= $newGameDate ? "newDay" : "" ?>">
         <td class="ML"><?= $newGameDate  ||  $scheduleFormat['type'] == "team" ? $gameDate : "" ?></td>
         <td class="MC"><?= $schedDetails[$i]['Time'] ?></td>
         <td class="MC"><?= ucfirst( strtolower( $schedDetails[$i]['Diamond'] ) ) ?></td>
      </tr>
   
<?php
      $lastGameDate = $gameDate;
    }
?>
   </table>
   </div>
   
</div>

