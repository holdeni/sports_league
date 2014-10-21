<?php
// $Id: information.php 147 2011-05-26 17:52:39Z Henry $
// Last Change: $Date: 2011-05-26 13:52:39 -0400 (Thu, 26 May 2011) $

?>

<h1>League Information</h1>

<p>Various information, including files and links to other web sites, are available from this page. If you have a link
   you would like added to this page, please send the relevant information in an email <a href="mailto:<?= $this->config->item( 'my_replyAddress' ); ?>">
   to the site webmaster</a>.
</p>
<br />

<div id="documents">
<h2>League Documents</h2>

<table width="100%">
   <tr>
      <td>League Constitution</td>
      <td>This document details the formation &amp; structure of the league.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/League_Constitution.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/League_Constitution.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>League Administrative Rules</td>
      <td>This document details the rules by which league operates under.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/admin_rules.html" ) ?></td>
      <td>

<?php
echo anchor( "information/showDocument/admin_rules.html/4000/".$this->config->item( 'siteType' ), "HTML" );
?>
      </td>
      <td>&nbsp;</td>
   </tr>

    <tr>
      <td>League Game Rules</td>
      <td>This document details the rules governing play druing league games.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/playing_rules.html" ) ?></td>
      <td>

<?php
echo anchor( "information/showDocument/playing_rules.html/4000/".$this->config->item( 'siteType' ), "HTML" );
?>
      </td>
     <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/playing_rules.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>Player Guidelines</td>
      <td>This document details the important points of league and softball rules that all players should be aware.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/PlayerGuidelines_2014.pdf" ) ?></td>
      <td>&nbsp;</td>
     <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/PlayerGuidelines_2014.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>Code of Discipline</td>
      <td>This document describes the league's Code of Discipline.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/Code_of_Discipline.html" ) ?></td>
      <td>

<?php
echo anchor( "information/showDocument/Code_of_Discipline.html/4000/".$this->config->item( 'siteType' ), "HTML" );
?>
		</td>
     <td>&nbsp;</td>
   </tr>

<!--    <tr>
      <td>Captain's Meeting Slides -- April, 2012</td>
      <td>This document contains the slide set for the 2012 Captain's Meeting.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/CaptainsMeeting2012.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/CaptainsMeeting2012.pdf" ?>">PDF</a></td>
   </tr>
 -->
<!--    <tr>
      <td>Waivers - Team</td>
      <td>This document contains the waiver form that must be completed by the team captain of each team.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/Waiver_Team.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/Waiver_Team.pdf" ?>">PDF</a></td>
   </tr>
 -->
<!--    <tr>
      <td>Waivers - Individual</td>
      <td>This document contains the waiver form that every player in the league must complete.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/Waiver_Individual.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/Waiver_Individual.pdf" ?>">PDF</a></td>
   </tr>
 -->
    <tr>
      <td>End Of Season Report - 2013</td>
      <td>This document contains the end of season report for 2013, as prepared by the executive.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2013.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2013.pdf" ?>">PDF</a></td>
   </tr>

    <tr>
      <td>End Of Season Report - 2012</td>
      <td>This document contains the end of season report for 2012, as prepared by the executive.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2012.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2012.pdf" ?>">PDF</a></td>
   </tr>

    <tr>
      <td>End Of Season Report - 2011</td>
      <td>This document contains the end of season report for 2011, as prepared by the executive.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2011.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2011.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>End Of Season Report - 2010</td>
      <td>This document contains the end of season report for 2010, as prepared by the executive.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2010.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/Ottawa_Tech_Softball_Report_2010.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>Game Defensive Assignments</td>
      <td>This form can be used for a team to track their defensive assigments for players, inning by inning, during a game.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/defence98.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/defence98.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>Game Batting Lineuup</td>
      <td>This form can be used for a team to track their batting lineup during a game.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/".$this->config->item( 'siteType' )."/gameSheet98.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/".$this->config->item( 'siteType' )."/gameSheet98.pdf" ?>">PDF</a></td>
   </tr>

</table>

</div>
<br />

<div id="Archive">
<h2>Archived Announcements</h2>

<p>

<?php
echo anchor( "information/archiveAnnouncements", "See previous league announcements" );
?>
</p>
</div>
<br />

<div id="links">
<h2>Important Links</h2>

<ul>
   <li><a href="http://www.nsacanada.ca/" target="_blank">National Slo-Pitch Association (NSA)</a> This is the softball organization this league is affiliated with.</li>
   <li><a href="http://www.nsacanada.ca/bats/approved-print.htm" target="_blank">NSA Approved Bats</a> This is the list of approved bats by NSA. They list the bats
      that can be used not the bats you cannot.
   </li>
   <li><a href="http://www.nsacanada.ca/bats/news.htm" target="_blank">NSA Bat Policy</a> What the NSA has to say about how they test bats and determine what is approved and what is not.</li>

</ul>
</div>