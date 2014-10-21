<?php
// $Id: admin_showDir.php 163 2011-06-08 13:41:10Z Henry $
// Last Change: $Date: 2011-06-08 09:41:10 -0400 (Wed, 08 Jun 2011) $

/* ... data declarations */
$todaysDateTime = date( "D, j M Y - g:i A" );

/* ... ensure the user has sufficient authority to view this page */
if ($this->Model_Account->hasAuthority( "ADMIN" )) {

/* ... sort the directory contents using alphanumeric ordering */
   asort( $entries );

?>

<h1>Website - Backup Directory Listing</h1>

<h4>Date &amp Time on Server: <?= $todaysDateTime ?></h4>

<?php
if (isset( $summary )) {
?>
<div id='dirSummary'>
   <?= $summary ?>
</div>

<?php
 }
?>
<div id='dirContents'>

<?php
/* ... build the directory contents into a table */
   $tableTemplate = array ( 'table_open' => '<table border="1" width="70%">' );
   $this->table->set_template( $tableTemplate );

   $this->table->set_heading( "Entry", "Modified", "Size (Kbytes)" );

   foreach ($entries as $dirEntry) {

/* ... if the controller wants the file entry to be a link to display the file, then build the appropriate HTML to do this */
      $linkToFile = "";
      if ($displayFileLink  &&  $details[$dirEntry]['type'] == "file") {
         $linkToFile = "<a href='/webAdmin/admin_website/showFile/".$dirEntry."/".urlencode( $directoryPath )."' target='_blank'>".$dirEntry."</a>";
       }

/* ... define the format for each column's data display */
      $entryCol = array(
         'data' => $linkToFile == "" ? $dirEntry : $linkToFile,
         'class' => $details[$dirEntry]['type'],
       );
      $modCol = array(
         'data' => preg_replace( "/x/", "&nbsp;", $details[$dirEntry]['mtime'] ),
         'class' => "time",
       );
      $sizeCol = array(
         'data' => $details[$dirEntry]['size'] >= 0 ? number_format( $details[$dirEntry]['size'] / 1024, 2 )." KB" : "",
         'class' => 'MC',
       );

      $this->table->add_row( $entryCol, $modCol, $sizeCol );
    }

/* ... display/generate the table */
   echo $this->table->generate();
?>
</div>

<?php
 }
?>