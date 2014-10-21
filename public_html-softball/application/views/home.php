<?php
// $Id: home.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );

/* ... include the league welcome screen */
include "siteSpecific/leagueWelcome-".$this->config->item( 'siteType' ).".html";

/* ... determine if we are before the start of the season, in the season, or just after a season*/
if (strtotime( $todaysDate ) < strtotime( $firstMonday )) {
   $daysToGo = round( (strtotime( $firstMonday ) - strtotime( $todaysDate )) / 60 /60 / 24 );
?>
<h3>
Only <?= $daysToGo ?> days until <?= date( "l, F j", strtotime( $firstMonday ) ) ?>, the opening day of the 
<?= date( "Y" ) ?> season!
</h3>

<?php
 }
elseif (strtotime( $todaysDate ) < strtotime( $endOfSeason )) {
?>
<h3>
Play Ball! The <?= date( "Y" ) ?> season is underway!
</h3>

<?php
 }
else {
?>
<h3>
The <?= date( "Y", strtotime( $endOfSeason ) ) ?> season has just concluded. Come back again soon as we prepare for the 
<?= date( "Y", strtotime( $endOfSeason ) )+1 ?> season!
</h3>

<?php
 }

/* ... if we are in the midst of the season, then we want to show the schedule & results for last week and this week */
if (isset( $lastWeekSched )  ||  isset( $thisWeekSched ) ) {
?>
<div id="weeklyResults">
   
<?php
   if (isset( $lastWeekSched )) {
?>
   <div id="lastWeeklyResults">
      <h3>Last Week's Results</h3>

<?php
      print $lastWeekSched;
?>
   </div>
   <div class="clearFloat"></div>
   
<?php
    }

   if (isset( $thisWeekSched )) {
?>
   <div id="thisWeeklyResults">
      <h3>This Week</h3>

<?php
      print $thisWeekSched;
?>
   </div>
   <div class="clearFloat"></div>

<?php
    }
?>
</div>

<?php
 }

/* ... display appropriate league announcements */
?>
<h2>League Announcements</h2>

<p>You can also <?php echo anchor( "information/archiveAnnouncements", "view previous announcements" ) ?> that were considered important.
</p>

<div id='announcements'>
<?php
if (count( $announcements ) > 0) {
   $i = 1;
   foreach ($announcements as $currAnnouncement) {
      if ($i % 2 != 0) {
         $noticeClass = "notice1";
       }
      else {
         $noticeClass = "notice2";
       }
      $i++;
?>
   <div class="<?= $noticeClass ?>">

<?php
      include $currAnnouncement['FullPath'];
?>
   </div>
   
<?php
    }
 }
else {

?>
   <p>No announcements to display at this time.</p>

<?php
 }
?>
</div>
