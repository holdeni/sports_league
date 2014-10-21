<?php
// $Id: sched_standings.php 177 2011-06-28 17:12:58Z Henry $
// Last Change: $Date: 2011-06-28 13:12:58 -0400 (Tue, 28 Jun 2011) $

$rightNow = date( "l, j M Y g:i A" );
?>

<h1>League Standings (as of <?= $rightNow ?>)</h1>

<div id="standings">
   
<?php
foreach ($positions as $curDivision => $divOrder) {
?>

<table border="border" width="95%">
   <caption>Division <?= $curDivision ?></caption>

   <tr>
      <th width="30%">Team name</th>
      <th width="4%">GP</th>
      <th width="4%">W</th>
      <th width="4%">L</th>
      <th width="4%">T</th>
      <th width="4%">Pts</th>
      <th width="7%">Runs For</th>
      <th width="7%">Runs Aga</th>
      <th width="7%">Runs Delta</th>
      <th width="29%">Tiebreaker</th>
   </tr>

<?php
   foreach ($divOrder as $teamID) {
      $runsDelta = $standings['runsFor'][$teamID] - $standings['runsAga'][$teamID];
      $deltaClass = "zero";
      if ($runsDelta < 0) {
         $deltaClass = "negative";
       }
      elseif ($runsDelta > 0) {
         $deltaClass = "positive";
       }
?>

   <tr class="teamRow">
      <td class="ML"><?= htmlspecialchars( $this->Model_Team->getTeamName( $teamID ) ) ?></td>
      <td><?= $standings['wins'][$teamID] + $standings['losses'][$teamID] + $standings['ties'][$teamID] ?></td>
      <td><?= $standings['wins'][$teamID] ?></td>
      <td><?= $standings['losses'][$teamID] ?></td>
      <td><?= $standings['ties'][$teamID] ?></td>
      <td><?= $standings['points'][$teamID] ?></td>
      <td><?= $standings['runsFor'][$teamID] ?></td>
      <td><?= $standings['runsAga'][$teamID] ?></td>
      <td class="<?= $deltaClass ?>"><?= $runsDelta ?></td>

<?php
      if (isset( $standings['tiebreaks'] )) {
?>
      <td><?= $standings['tiebreaks'][$curDivision][$teamID] ?></td>

<?php
       }
      else {
?>
      <td>&nbsp;</td>

<?php
       }
?>
   </tr>

<?php
    }
?>

</table>
<br />

<?php
 }

/* ... put up the information on which tiebreakers we are using */
if ($this->config->item( 'my_tiebreakerFormula' ) == "BASIC") {
?>
<h3>Basic Tiebreakers In Effect</h3>
<p>Standing order is being determined using the following tiebreakers, in order. These tiebreakers are used only until mid-season
   at which time most teams will have played each other at least once making the use of Head to Head tiebreakers worthwhile.</p>
<ol>
   <li>Most Points</li>
   <li>Better delta in runs for than against</li>
   <li>More wins</li>
   <li>Fewer losses</li>
   <li>Fewer games played</li>
</ol>

<?php
 }
else {
?>
<h3>Advanced Tiebreakers In Effect</h3>
<p>Standing order is being determined using the following tiebreakers, in order. These tiebreakers follow the full set as defined
   in the league rules.
</p>
<ol>
   <li>Most overall points</li>
   <li>Better head to head record</li>
   <li>Total overall wins</li>
   <li>Better head to head run differential</li>
   <li>Better overall run differential</li>
   <li>Coin Toss</li>
</ol>
<p>Each tiebreaker is applied in order. When more than 2 teams are tied, only teams that remain tied after a tiebreaker is applied
   are reviewed at subsequent tiebreaker levels. To review head to head details, use the link below to view how teams have performed
   against each other.
</p>

<?php
 }

/* ... put up link for Head to Head information */
echo anchor( "schedule/sched_standings/headToHead", "View Head to Head details" );
?>

</div>