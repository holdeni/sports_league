<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// $Id: database.php 84 2011-04-13 19:22:44Z Henry $

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|  ['hostname'] The hostname of your database server.
|  ['username'] The username used to connect to the database
|  ['password'] The password used to connect to the database
|  ['database'] The name of the database you want to connect to
|  ['dbdriver'] The database type. ie: mysql.  Currently supported:
             mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|  ['dbprefix'] You can add an optional prefix, which will be added
|            to the table name when using the  Active Record class
|  ['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|  ['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|  ['cache_on'] TRUE/FALSE - Enables/disables query caching
|  ['cachedir'] The path to the folder where cache files should be stored
|  ['char_set'] The character set used in communicating with the database
|  ['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "prodEnv";
$active_record = TRUE;

$db['testEnv']['hostname'] = "localhost";
$db['testEnv']['username'] = "uianhold_privusr";
$db['testEnv']['password'] = "H0me,P1te";
$db['testEnv']['database'] = "uianhold_lobball_test";
$db['testEnv']['dbdriver'] = "mysql";
$db['testEnv']['dbprefix'] = "";
$db['testEnv']['pconnect'] = TRUE;
$db['testEnv']['db_debug'] = TRUE;
$db['testEnv']['cache_on'] = FALSE;
$db['testEnv']['cachedir'] = "";
$db['testEnv']['char_set'] = "utf8";
$db['testEnv']['dbcollat'] = "utf8_general_ci";
$db['testEnv']['swap_pre'] = '';
$db['testEnv']['autoinit'] = TRUE;
$db['testEnv']['stricton'] = FALSE;

$db['prodEnv']['hostname'] = "localhost";
$db['prodEnv']['username'] = "uianhold_privusr";
$db['prodEnv']['password'] = "H0me,P1te";
$db['prodEnv']['database'] = "uianhold_lobball";
$db['prodEnv']['dbdriver'] = "mysql";
$db['prodEnv']['dbprefix'] = "";
$db['prodEnv']['pconnect'] = TRUE;
$db['prodEnv']['db_debug'] = TRUE;
$db['prodEnv']['cache_on'] = FALSE;
$db['prodEnv']['cachedir'] = "";
$db['prodEnv']['char_set'] = "utf8";
$db['prodEnv']['dbcollat'] = "utf8_general_ci";
$db['prodEnv']['swap_pre'] = '';
$db['prodEnv']['autoinit'] = TRUE;
$db['prodEnv']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */