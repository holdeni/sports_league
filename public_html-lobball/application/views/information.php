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
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/LeagueConstitution.html" ) ?></td>
      <td>

<?php
echo anchor( "information/showDocument/LeagueConstitution.html/1250", "HTML" );
?>
      </td>
      <td><a href="<?= base_url()."data/LeagueConstitution.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>League Rules</td>
      <td>This document details the rules by which league games operate under.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/LeagueRules.html" ) ?></td>
      <td>

<?php
echo anchor( "information/showDocument/LeagueRules.html/4000", "HTML" );
?>
      </td>
      <td><a href="<?= base_url()."data/LeagueRules.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>League Appeal Process</td>
      <td>This document describes the league's formal appeal/protest process.</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/LeagueAppeals.html" ) ?></td>
      <td>

<?php
echo anchor( "information/showDocument/LeagueAppeals.html/775", "HTML" );
?>
      </td>
      <td><a href="<?= base_url()."data/LeagueAppeals.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
   	<td>Team Roster <strong>NEW</strong></td>
      <td>This document contains the form to be completed by each team, listing their players</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/Team_Roster.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/Team_Roster.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
   	<td>8 Team Playoff <strong>NEW</strong></td>
      <td>This document contains the structure used for 8 team playoffs</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/8_team.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/8_team.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
   	<td>9 Team Playoff <strong>NEW</strong></td>
      <td>This document contains the structure used for 9 team playoffs</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/9_team.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/9_team.pdf" ?>">PDF</a></td>
   </tr>

<!--
   <tr>
      <td>Website Overview</td>
      <td>This document describes version 1.7 of the new league website</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/Overview_of_Website.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/Overview_of_Website.pdf" ?>">PDF</a></td>
   </tr>

   <tr>
      <td>Website Process - Game Results</td>
      <td>This document describes how to submit game results including rainouts</td>
      <td><?= _getLastUpdated( $this->config->item( 'my_rootDirectory' )."data/How_to_Submit_Game_Results.pdf" ) ?></td>
      <td>&nbsp;</td>
      <td><a href="<?= base_url()."data/How_to_Submit_Game_Results.pdf" ?>">PDF</a></td>
   </tr>
-->

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

<?php
function _getLastUpdated( $filename ) {

/* ... get last modification date for bulk file */
   $lastModified = stat( $filename );
   $fileModified = date( "d M Y", $lastModified[9] );

/* ... time to go */
   return( $fileModified );
 }
