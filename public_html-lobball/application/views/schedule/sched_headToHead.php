<?php
// $Id: sched_headToHead.php 177 2011-06-28 17:12:58Z Henry $
// Last Change: $Date: 2011-06-28 13:12:58 -0400 (Tue, 28 Jun 2011) $
?>

<h1>Head To Head Records</h1>

<div id="standingsH2H">

<p>Team records against an opponent are read across in a row. A <strong>W</strong> indicates a win against the team whose
   column the entry appears. A <strong>L</strong> indicates a loss against that team. The number after the <strong>W</strong>
   or <strong>L</strong> indicates the positive or negative run differential for the team from that game.
</p>
   
<?php
foreach ($headToHead as $currDivision) {
   echo $currDivision;
?>

   <br />
<?php
 }
?>

</div>