<?php
// $Id: archiveAnnouncements.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

?>

<h1>Prior Announcements</h1>

<p>Below are important announcements that have previously appeared on the main web page.
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