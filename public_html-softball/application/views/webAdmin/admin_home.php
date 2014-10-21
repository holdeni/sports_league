<?php
// $Id: admin_home.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $

/* ... data declarations */
$todaysDateTime = date( "D, j M Y - g:i A" );

if ($this->Model_Account->hasAuthority( "ADMIN" )) {
?>

<h1>Website Administration</h1>

<p>
From this page you can perform various website administrative functions. You are only able to perform those
actions that you have been authorized for by the webmaster.
</p>

<h2>Site Information</h2>

<ul>
   <li>Website Date &amp; Time: <?= $todaysDateTime ?></li>

   <li>Current uptime:

<?php
   exec( 'uptime', $cmdOutput );
   echo $cmdOutput[0];
?>
   </li>
</ul>

<h2>Directory Locations</h2>

<ul>
   <li><?= anchor( "webAdmin/admin_website/showBackups", "Review backups directory content" )?></li>
   <li><?= anchor( "webAdmin/admin_website/showLogs", "Review Logs directory content" ) ?></li>
</ul>

<?php
 }


