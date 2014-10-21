<?php
// $Id: contacts.php 88 2011-04-14 18:52:55Z Henry $
// Last Change: $Date: 2011-04-14 14:52:55 -0400 (Thu, 14 Apr 2011) $
?>

<h1>League Executive Contacts</h1>

<div id="execContacts">
<table>
   <tr>
      <th>President &amp; Treasurer</th>
      <td>Dennis Seebach</td>
      <td>(613) 599-6172</td>
   </tr>
   <tr>
      <th>Vice-President</th>
      <td>Don Moore </td>
      <td>(613) 836-3567</td>
   </tr>
   <tr>
      <th>Scheduler</th>
      <td>Paul Holland</td>
      <td>(613) 270-8884</td>
   </tr>
   <tr>
      <th>Secretary &amp; Webmaster</th>
      <td>Ian Holden </td>
      <td>(613) 612-7875</td>
      <td><a href="mailto:<?= $this->config->item( 'my_replyAddress' ); ?>"><?= $this->config->item( 'my_replyAddress' ); ?></a></td>
   </tr>
</table>
<p>You may also contact the executive by sending an email to <a href="mailto:<?= $this->config->item( 'my_execAddress' ) ?>"><?= $this->config->item( 'my_execAddress' )?></a>.</p>
</div>

<br />

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
