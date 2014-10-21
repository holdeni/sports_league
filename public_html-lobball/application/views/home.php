<?php
// $Id: home.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<h1>Welcome</h1>

<p>
You are visiting the website for the Kanata Mens Lobball League, a league that has seen teams playing lobball
from the Kanata, ON and Stittsville, ON area for more than 25+ years. Our season runs from May through the end
of September with games played Mon thru Wednesday on diamonds in the Bridlewood area. Games are played at
6:30pm, 8:00pm and 9:30pm each evening with teams playing once a week normally.
</p>

<p>
In March and April of each year, new players who are interested in joining teams are welcome to give us
their contact details. As team captains indicate they need players, we will offer them your information so they
can contact you about their team. Please come back to our website in March and April if you thinking about 
trying to find a team to play on.
</p>

<p>
Feel free to visit our site to learn more about the league. You can also use our contact page to send us any
questions you may have about the league. Someone will get back to you within 72 hours with an answer.
</p>

<?php
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
