<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// $Id: app_softball.php 108 2011-04-21 17:55:53Z Henry $

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
|  'my_environment'       = "DEV" | "TEST" | "PROD"
|  'firstMonday'          = date of first game (YYYY-MM-DD)
|  'thisYear'             = year for the current season
|  'bulkSchedule'         = file (relative to 'my_rootDirectory' path) used for bulk schedule loading
|  'bulkTournament'       = file (relative to 'my_RootDirectory' path) used for bulk tournament loading
|  'siteName'             = title to be used on browser identifying site
|  'my_fromAddr'          = email address to be used as FROM address
|  'my_fromName'          = common name to be used as FROM address
|  'my_devAddress'        = email address to substitute for TO in DEV environment
|  'my_replyAddress'      = email address to request any emails about the website are directed to
|  'my_execAddress'       = email address to contact the league executive
|  'my_tiebreakerFormula' = either BASIC or ADVANCED; determines how ties in standings are broken
|  'my_rootDirectory'     = website root directory as it appears on the server (no http:// reference)  [include end "/"]
|  'my_backupDirectory    = directory to store backups (must be a relative directory (without ending "/") to web root for CI
|  'backups_toKeep_Daily' = number of Daily backups to keep
|  'backups_toKeep_Weekly'= number of Weekly backups to keep
|  'backups_toKeep_Monthly'= number of Monthly backups to keep
*/

$config['my_environment'] = "PROD";

$config['firstMonday'] = '2014-05-05';
$config['thisYear'] = '2014';
$config['bulkSchedule'] = 'data/schedule.csv';
$config['bulkTournament'] = 'data/tournament.csv';
$config['siteName'] = "Kanata Men's Lobball League";

$config['my_fromAddress'] = "webmaster@kanatalobball.org";
$config['my_fromName'] = "Kanata Lobball Webmaster";

$config['my_devAddress'] = "holdeni@sympatico.ca";

$config['my_replyAddress'] = "webmaster@kanatalobball.org";
$config['my_execAddress'] = "the-exec@kanatalobball.org";

$config['my_tiebreakerFormula'] = "ADVANCED";

$config['my_rootDirectory'] = "/home/uianhold/public_html/";

$config['my_backupDirectory'] = "backups";
$config['backups_toKeep_Daily'] = 12;               // Keeps 2 weeks worth, minus the 2 weeklys in that time period
$config['backups_toKeep_Weekly'] = 4;
$config['backups_toKeep_Monthly'] = 6;