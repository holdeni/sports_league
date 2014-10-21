<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// $Id: app_softball.php 221 2012-03-26 22:46:39Z Henry $
// Last Change: $Date: 2012-03-26 18:46:39 -0400 (Mon, 26 Mar 2012) $

/*
| -------------------------------------------------------------------
| APP_SOFTBALL
| -------------------------------------------------------------------
| This file specifies specific configuration values for the Softball
| web application.
|
*/

/*
| -------------------------------------------------------------------
|  Environment
| -------------------------------------------------------------------
| Variable to describe type of environment the application is running in.
| The value is one of "DEV", "TEST" or "PROD". Setting the environment value
| can drive features of the application (e.g. amount of logging or output
| messages).
|
| Prototype:
|
|  'my_environment'          = "DEV" | "TEST" | "PROD"
|  'firstMonday'             = date of first game (YYYY-MM-DD)
|  'thisYear'                = year for the current season
|  'bulkSchedule'            = file (relative to 'my_rootDirectory' path) used for bulk schedule loading
|  'bulkTournament'          = file (relative to 'my_RootDirectory' path) used for bulk tournament loading
|  'siteName'                = title to be used on browser identifying site
|  'siteType'                = "softball" | "lobball" (Used to build filenames for variant site information)
|  'my_fromAddr'             = email address to be used as FROM address
|  'my_fromName'             = common name to be used as FROM address
|  'my_devAddress'           = email address to substitute for TO in DEV environment
|  'my_replyAddress'         = email address to request any emails about the website are directed to
|  'my_execAddress'          = email address to contact the league executive
|  'my_tiebreakerFormula'    = either BASIC, ADVANCED or ADVANCED-2; determines how ties in standings are broken
|  'my_rootDirectory'        = website root directory as it appears on the server (no http:// reference)  [include end "/"]
|  'my_backupDirectory'      = directory to store backups (must be a relative directory (without ending "/") to web root for CI
|  'backups_toKeep_Daily'    = number of Daily backups to keep
|  'backups_toKeep_Weekly'   = number of Weekly backups to keep
|  'backups_toKeep_Monthly'  = number of Monthly backups to keep
*/

$config['my_environment'] = "PROD";

$config['firstMonday']    = '2014-05-27';
$config['thisYear']       = '2014';
$config['bulkSchedule']   = 'data/schedule.csv';
$config['bulkTournament'] = 'data/tournament.csv';

$config['siteURL']        = "http://otttechsoftball.org";
$config['siteName']       = "Ottawa Tech Softball League";
$config['siteType']       = "softball";

$config['my_fromAddress']      = "webmaster@otttechsoftball.org";
$config['my_fromName']         = "Ottawa Tech Softball Webmaster";
$config['mail_subject_prefix'] = "Softball: ";

$config['my_devAddress'] = "holdeni@sympatico.ca";

$config['my_replyAddress'] = "webmaster@otttechsoftball.org";
$config['my_execAddress']  = "softball-exec@otttechsoftball.org";

$config['my_tiebreakerFormula'] = "ADVANCED-2";

$config['my_rootDirectory'] = "/home/ottte983/public_html/";

$config['my_backupDirectory']     = "backups";
$config['backups_toKeep_Daily']   = 12;               // Keeps 2 weeks worth, minus the 2 weeklys in that time period
$config['backups_toKeep_Weekly']  = 4;
$config['backups_toKeep_Monthly'] = 6;

$config['registration_team_begin']   = "2012-03-26";
$config['registration_team_end']     = "2012-04-13";
$config['registration_player_begin'] = "2014-04-01";
$config['registration_player_end']   = "2014-06-30";

$config['divisions'] = array(
	"A" => "A"
);

$config['scheduling_options'] = array(
	0 => " ",
   1 => "All games must be at 4:45pm",
   2 => "Games may be at either 4:45pm or 6:15pm",
   3 => "All games must be at 6:15pm",
);

$config['sportsmanship_ratings'] = array(
   0 => "Select one ...",
   1 => "Very poor sportsmanship with multiple disappointing moments",
   2 => "Poor sportsmanship with some disappointing moments",
   3 => "Good with nothing enhancing nor hindering the game experience",
   4 => "Very good sportsmanship with some memorable moments",
   5 => "Excellent sportsmanship with multiple memorable moments"
);
