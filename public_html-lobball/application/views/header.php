<?php
// $Id: header.php 177 2011-06-28 17:12:58Z Henry $
// Last Change: $Date: 2011-06-28 13:12:58 -0400 (Tue, 28 Jun 2011) $
?>

<a href="/mainpage/index">
   <img src="<?= base_url() ?>images/kanata-logo.jpg" border='0' alt="Kanata Lobball" />
</a>

<div id="globalnav">   
<ul>

<?php
# ... put a link back to main/home page on every page but the home page
if ($this->uri->segment( 1 ) != "mainpage"  ||
   ($this->uri->segment( 1 ) == "mainpage"  &&  ($this->uri->segment( 2 ) != "index"  &&
                                                 $this->uri->segment( 2 ) != "processLogin"  &&
                                                 $this->uri->segment( 2 ) != "logout"))) {
   echo "<li>".anchor( "mainpage/index", "home" )."</li>\n";
 }

echo "<li>".anchor( "information", "league info" )."</li>\n";
echo "<li>".anchor( "contacts", "contacts" )."</li>\n";
echo "<li>".anchor( "schedule/sched_mainpage/index", "schedules" )."</li>\n";
echo "<li>".anchor( "schedule/sched_standings", "standings" )."</li>\n";
?>

</ul>

<br /><br /><br /><br />

<p class="header">Kanata Men's Lobball League</p>

</div>

<!-- following is used to reset flow for following DIVs back to absolute left side -->
<div id="clearBoth"></div>