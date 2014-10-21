<?php
// $Id: contacts.php 232 2012-04-13 19:33:56Z Henry $
// Last Change: $Date: 2012-04-13 15:33:56 -0400 (Fri, 13 Apr 2012) $
?>

<h1>Team Contacts</h1>

<p>
From this page you can view the latest league contact lists in a variety of ways.
</p>

<ul>
   <li>
      <?= anchor( "contacts/showLeague", "View all league contacts" ) ?>
   </li>

   <br />
   <li>
      View specific team's contact list &nbsp;

<?php
echo form_open( "contacts/showTeam" );
$selectorAttributes = "";
$i = 0;
foreach ($data[$formFields[$i]['fieldName']] as $attribute => $value) {
   $selectorAttributes .= $attribute."='".$value."' ";
 }
?>
   <?= form_dropdown( $formFields[$i]['fieldName'], $options[$formFields[$i]['fieldName']], $formFields[$i]['default'] != "" ? $formFields[$i]['default'] : "", $selectorAttributes ) ?>
   &nbsp;

<?php
echo form_submit( "viewTeam", "View Team's Contacts" );
echo form_close();
?>
   </li>

   <br />
   <li>
      View team contacts for a specific division &nbsp;

<?php
echo form_open( "contacts/showDivision" );
$selectorAttributes = "";
$i = 0;
foreach ($data2[$formFields2[$i]['fieldName']] as $attribute => $value) {
   $selectorAttributes .= $attribute."='".$value."' ";
 }
?>
   <?= form_dropdown( $formFields2[$i]['fieldName'], $options2[$formFields2[$i]['fieldName']], $formFields2[$i]['default'] != "" ? $formFields2[$i]['default'] : "", $selectorAttributes ) ?>
   &nbsp;

<?php
echo form_submit( "viewDiv", "View Division's Contacts" );
echo form_close();
?>
   </li>
</ul>

<br />

<h1>League Executive Contacts</h1>

<div id="execContacts">
<?php
include "siteSpecific/leagueExec-".$this->config->item( 'siteType' ).".html";
?>
<p>You may also contact the executive by sending an email to <a href="mailto:<?= $this->config->item( 'my_execAddress' ) ?>"><?= $this->config->item( 'my_execAddress' )?></a>.</p>
</div>
