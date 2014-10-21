<?php
// $Id: leag_listSpares.php 233 2012-04-13 20:11:09Z Henry $
// Last Change: $Date: 2012-04-13 16:11:09 -0400 (Fri, 13 Apr 2012) $

/* ... data declarations */
$todaysDate = date( "Y-m-d" );
$playingOptions = $this->config->item( 'scheduling_options' );

/* ... prepare a fine table to display our data */
$this->load->library( 'table' );

$this->table->set_heading( 'Name', 'Email', 'Phone', 'Category', 'Playing Option', 'Details' );

foreach ($sparesList as $spareInfo) {

	$name = $spareInfo['AccountDetails']['FirstName'].' '.$spareInfo['AccountDetails']['LastName'];

	$emailDetails = $spareInfo['AccountDetails']['EmailAccount'];
	if (!empty( $spareInfo['AccountDetails']['AltEmail'] )) {
		$emailDetails .= $emailDetails."<br />".$spareInfo['AccountDetails']['AltEmail'];
	}

	$phoneDetails = "[H] ".formatPhoneNr( $spareInfo['AccountDetails']['HomePhone'] );
	if (!empty( $spareInfo['AccountDetails']['CellPhone'] )) {
		$phoneDetails .= "<br />[C] ".formatPhoneNr( $spareInfo['AccountDetails']['CellPhone'] );
	}
	if (!empty( $spareInfo['AccountDetails']['WorkPhone'] )) {
		$phoneDetails .= "<br />[W] ".formatPhoneNr( $spareInfo['AccountDetails']['WorkPhone'] );
	}

	switch ($spareInfo['Gender']) {
		case 'F':
			$spareCategory = "Female";
			break;
		case 'M':
			$spareCategory = "Male";
			break;
		case 'B':
			$spareCategory = "Group";
			break;
	}

/* ... time to put all this nice data in a row */
	$this->table->add_row( $name, $emailDetails, $phoneDetails, $spareCategory, $playingOptions[$spareInfo['Scheduling']], $spareInfo['Notes'] );

}

/* ... and now, here it be in all the glory */
echo $this->table->generate();
?>

