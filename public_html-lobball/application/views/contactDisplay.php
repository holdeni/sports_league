<?php
// $Id: contactDisplay.php 130 2011-05-11 19:39:08Z Henry $
// Last Change: $Date: 2011-05-11 15:39:08 -0400 (Wed, 11 May 2011) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<p>Note: You may send an email to all contact addresses for a team by clicking on the team's name. It will open an email in your chosen mail
   client with all addresses already added in the TO field.
</p>
<br />

<h1><?= $header ?> (As of: <?= $todaysDate ?>)</h1>


<div id="contacts">

<table border="border" width="98%">
   
   <tr>
      <th>Team</th>
      <th>Captain Contact Details</th>
      <th>CoCaptain Contact Details</th>
      <th>Third Contact Details</th>
   </tr>

<?php
/* ... cycle through the list of teams to be displayed on this page */
$allContactsURL = "";
for ($i = 0; $i < count( $contactDetails ); $i++) {

/* ... combine all the team mailing addresses into one large mailto: URL string */
   $teamMailURL = "";
   if ($contactDetails[$i]['Captain']['EmailAddr'] != "") {
      $teamMailURL .= join( ",", $contactDetails[$i]['Captain']['EmailAddr'] ).",";
    }
   if ($contactDetails[$i]['CoCaptain']['EmailAddr'] != "") {
      $teamMailURL .= join( ",", $contactDetails[$i]['CoCaptain']['EmailAddr'] ).",";
    }
   if ($contactDetails[$i]['ThirdContact']['EmailAddr'] != "") {
      $teamMailURL .= join( ",", $contactDetails[$i]['ThirdContact']['EmailAddr'] ).",";
    }

/* ... if we have multiple teams on a page, we will display, at the end, an all inclusive URL for mailing */
   if (count( $contactDetails ) > 1) {
      if ($allContactsURL == "") {
         $allContactsURL = $teamMailURL;
       }
      else {
         $allContactsURL .= ",".$teamMailURL;
       }
    }
?>

   <tr class="contactRow">

      <td class="ML"><a href="mailto:<?= $teamMailURL ?>"><?= htmlspecialchars( $this->Model_Team->getTeamName( $listOfTeams[$i] ) ) ?></a></td>

<!-- Captain Contact Column -->
      <td>
         <table>
            <tr>
               <td class="ML"><?= $contactDetails[$i]['Captain']['Name'] != "" ? $contactDetails[$i]['Captain']['Name'] : nbs( 1 ) ?></td>
            </tr>

            <tr>
               <td class="ML">

<?php
   foreach ($contactDetails[$i]['Captain']['EmailAddr'] as $emailAddr) {
      if ($emailAddr != "") {
?>
                  <a href="mailto:<?= $emailAddr ?>"><?= $emailAddr ?></a><br />

<?php
       }
      else {
         echo nbs( 1 );
       }
    }
?>
               </td>
            </tr>

            <tr>
               <td class="ML">Home: <?= formatPhoneNr( $contactDetails[$i]['Captain']['PhoneNr']['Home'] ) ?></td>
            </tr>
            <tr>
               <td class="ML">Work: <?= formatPhoneNr( $contactDetails[$i]['Captain']['PhoneNr']['Work'] ) ?></td>
            </tr>
            <tr>
               <td class="ML">Cell: <?= formatPhoneNr( $contactDetails[$i]['Captain']['PhoneNr']['Cell'] ) ?></td>
            </tr>
         </table>
      </td>

<!-- CoCaptain Contact Column -->
      <td>
         <table>
            <tr>
               <td class="ML"><?= $contactDetails[$i]['CoCaptain']['Name'] != "" ? $contactDetails[$i]['CoCaptain']['Name'] : nbs( 1 ) ?></td>
            </tr>

            <tr>
               <td class="ML">

<?php
   foreach ($contactDetails[$i]['CoCaptain']['EmailAddr'] as $emailAddr) {
      if ($emailAddr != "") {
?>
                  <a href="mailto:<?= $emailAddr ?>"><?= $emailAddr ?></a><br />

<?php
       }
      else {
         echo nbs( 1 );
       }
    }
?>
               </td>
            </tr>

            <tr>
               <td class="ML">Home: <?= formatPhoneNr( $contactDetails[$i]['CoCaptain']['PhoneNr']['Home'] ) ?></td>
            </tr>
            <tr>
               <td class="ML">Work: <?= formatPhoneNr( $contactDetails[$i]['CoCaptain']['PhoneNr']['Work'] ) ?></td>
            </tr>
            <tr>
               <td class="ML">Cell: <?= formatPhoneNr( $contactDetails[$i]['CoCaptain']['PhoneNr']['Cell'] ) ?></td>
            </tr>
         </table>
      </td>

<!-- Third Contact Column -->
      <td>
         <table>
            <tr>
               <td class="ML"><?= $contactDetails[$i]['ThirdContact']['Name'] != "" ? $contactDetails[$i]['ThirdContact']['Name'] : nbs( 1 ) ?></td>
            </tr>

            <tr>
               <td class="ML">

<?php
   foreach ($contactDetails[$i]['ThirdContact']['EmailAddr'] as $emailAddr) {
      if ($emailAddr != "") {
?>
                  <a href="mailto:<?= $emailAddr ?>"><?= $emailAddr ?></a><br />

<?php
       }
      else {
         echo nbs( 1 );
       }
    }
?>
               </td>
            </tr>

            <tr>
               <td class="ML">Home: <?= formatPhoneNr( $contactDetails[$i]['ThirdContact']['PhoneNr']['Home'] ) ?></td>
            </tr>
            <tr>
               <td class="ML">Work: <?= formatPhoneNr( $contactDetails[$i]['ThirdContact']['PhoneNr']['Work'] ) ?></td>
            </tr>
            <tr>
               <td class="ML">Cell: <?= formatPhoneNr( $contactDetails[$i]['ThirdContact']['PhoneNr']['Cell'] ) ?></td>
            </tr>
         </table>
      </td>

   </tr>

<?php
 }
?>
</table>

<?php
/* ... if the user has a sufficient role, provide the option of mailing to all contacts shown */
if ($this->Model_Account->hasAuthority( "COMMISH" )  &&  count( $contactDetails ) > 1) {
?>

<br />
<p>Mail to <a href="mailto:<?= $allContactsURL ?>">ALL contacts shown</a></p>

<?php
 }
?>
</div>

