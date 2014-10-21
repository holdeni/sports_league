<?php
// $Id: header.php 215 2012-03-26 22:40:03Z Henry $
// Last Change: $Date: 2012-03-26 18:40:03 -0400 (Mon, 26 Mar 2012) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
?>

<a href="/mainpage/index">
   <img src="<?= base_url() ?>images/league-logo.jpg" border='0' alt="<?= $this->config->item( 'siteName' ); ?>" />
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

if (strtotime( $this->config->item( 'registration_team_begin' ) ) <= strtotime( $todaysDate )  &&  strtotime( $todaysDate ) <= strtotime( $this->config->item( 'registration_team_end' ) )  ||
	strtotime( $this->config->item( 'registration_player_begin' ) ) <= strtotime( $todaysDate )  &&  strtotime( $todaysDate ) <= strtotime( $this->config->item( 'registration_player_end' ) ) ) {
	echo "<li>".anchor( "league/leag_register", "register" )."</li>\n";
}
?>

</ul>

<br /><br /><br /><br />

<p class="header"><?= $this->config->item( 'siteName' ); ?></p>

</div>

<!-- following is used to reset flow for following DIVs back to absolute left side -->
<div id="clearBoth"></div>