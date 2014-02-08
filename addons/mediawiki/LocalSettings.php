<?php

# This file was automatically generated by the MediaWiki installer.
# If you make manual changes, please keep track in case you need to
# recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# http://www.mediawiki.org/wiki/Manual:Configuration_settings

# If you customize your file layout, set $IP to the directory that contains
# the other MediaWiki files. It will be used as a base to locate files.
if( defined( 'MW_INSTALL_PATH' ) ) {
	$IP = MW_INSTALL_PATH;
} else {
	$IP = dirname( __FILE__ );
}

$path = array( $IP, "$IP/includes", "$IP/languages" );
$urlpath = str_replace('//', '/', str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']).'/'));
set_include_path( implode( PATH_SEPARATOR, $path ) . PATH_SEPARATOR . get_include_path() );

require_once( "$IP/includes/DefaultSettings.php" );

if ( $wgCommandLineMode ) {
	if ( isset( $_SERVER ) && array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
		die( "This script must be run from the command line\n" );
	}
}
## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;
require_once(dirname(__FILE__).'/../../config.php');
require_once(CORE_BASE_PATH . 'passwords.php');
$wgSitename         = "Catroid";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs please see:
## http://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath       = $urlpath . "addons/mediawiki";
$wgArticlePath      = $urlpath . "wiki/$1";
$wgUsePathInfo = true;
$wgScriptExtension  = ".php";

## The relative URL path to the skins directory
$wgStylePath        = "$wgScriptPath/skins";

## The relative URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogo             = "$wgStylePath/common/images/wiki.png";

## UPO means: this is also a user preference option

$wgEnableEmail      = true;
$wgEnableUserEmail  = true; # UPO

$wgEmergencyContact = "wiki@catroid.org";
$wgPasswordSender = "wiki@catroid.org";

$wgEnotifUserTalk = true; # UPO
$wgEnotifWatchlist = true; # UPO
$wgEmailAuthentication = true;

## Database settings
$wgDBtype           = "postgres";
$wgDBserver         = DB_HOST_WIKI;
$wgDBname           = DB_NAME_WIKI;
$wgDBuser           = DB_USER_WIKI;
$wgDBpassword       = DB_PASS_WIKI;

# Postgres specific settings
$wgDBport           = "5432";
$wgDBmwschema       = "public";
$wgDBts2schema      = "public";

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = array();

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads       = false;
# $wgUseImageMagick = true;
# $wgImageMagickConvertCommand = "/usr/bin/convert";

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
# $wgShellLocale = "en_US.UTF-8";

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
# $wgHashedUploadDirectory = false;

## If you have the appropriate support software installed
## you can enable inline LaTeX equations:
$wgUseTeX           = false;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

$wgLocalInterwiki   = strtolower( $wgSitename );

$wgLanguageCode = "en";

$wgSecretKey = "18098c1b5fe73ab94b5852523f7b64cf1c425e395e5920f75128555a474457f7";

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = 'monobook';

require_once(CORE_BASE_PATH . 'classes/CoreClientDetection.php');
$clientDetection = new CoreClientDetection();
$mobile_style = $clientDetection->isMobile();
if($mobile_style) {
  $wgDefaultSkin = 'wptouch';
}

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgEnableCreativeCommonsRdf = true;
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "http://www.gnu.org/copyleft/fdl.html";
$wgRightsText = "GNU Free Documentation License 1.3";
$wgRightsIcon = "${wgScriptPath}/skins/common/images/gnu-fdl.png";
# $wgRightsCode = "gfdl1_3"; # Not yet used

$wgDiff3 = "";

# When you make changes to this configuration file, this will make
# sure that cached pages are cleared.
$wgCacheEpoch = max( $wgCacheEpoch, gmdate( 'YmdHis', @filemtime( __FILE__ ) ) );

# [catroid - tg] defined the following
$wgGroupPermissions['*']['createaccount'] = false; //turn user registration off
$wgGroupPermissions['*']['edit'] = false; //turn off page-edit for non registered users#

// turn off the login/logout special pages
//function LessSpecialPages(&$aSpecialPages) {
//  unset( $aSpecialPages['Userlogout'] );
//  unset( $aSpecialPages['Userlogin'] );
//  unset( $aSpecialPages['Changepassword'] );
//  return true;
//}
//$wgHooks['SpecialPage_initList'][]='LessSpecialPages';

// turn off login/logout link
//function NoLoginLinkOnMainPage( &$personal_urls ){
//    unset( $personal_urls['login'] );
//    unset( $personal_urls["logout"] );
//    unset( $personal_urls['anonlogin'] );
//    return true;
//}
//$wgHooks['PersonalUrls'][]='NoLoginLinkOnMainPage';

// PHPBB User Database Plugin.
//require_once './extensions/Auth_phpBB/Auth_phpBB.php';
 
//$wgAuth_Config = array(); // Clean.
// 
//$wgAuth_Config['WikiGroupName'] = 'REGISTERED'; // Name of your PHPBB group
//                                                // users need to be a member
//                                                // of to use the wiki. (i.e. wiki)
//                                                // This can also be set to an array 
//                                                // of group names to use more then 
//                                                // one. (ie. 
//                                                // $wgAuth_Config['WikiGroupName'][] = 'Wiki';
//                                                // $wgAuth_Config['WikiGroupName'][] = 'Wiki2';
//                                                // or
//                                                // $wgAuth_Config['WikiGroupName'] = array('Wiki', 'Wiki2');
//                                                // )
// 
//$wgAuth_Config['UseWikiGroup'] = true;          // This tells the Plugin to require
//                                                // a user to be a member of the above
//                                                // phpBB group. (ie. wiki) Setting
//                                                // this to false will let any phpBB
//                                                // user edit the wiki.
// 
//$wgAuth_Config['UseExtDatabase'] = true;       // This tells the plugin that the phpBB tables
//                                                // are in a different database then the wiki.
//                                                // The default settings is false.
// 
//$wgAuth_Config['PGSQL_Host']        = DB_HOST_BOARD; // phpBB PGSQL Host Name.
//$wgAuth_Config['PGSQL_Username']    = DB_USER_BOARD; // phpBB PGSQL Username.
//$wgAuth_Config['PGSQL_Password']    = DB_PASS_BOARD; // phpBB PGSQL Password.
//$wgAuth_Config['PGSQL_Database']    = DB_NAME_BOARD; // phpBB PGSQL Database Name.
// 
//$wgAuth_Config['UserTB']         = 'phpbb_users';       // Name of your PHPBB user table. (i.e. phpbb_users)
//$wgAuth_Config['GroupsTB']       = 'phpbb_groups';      // Name of your PHPBB groups table. (i.e. phpbb_groups)
//$wgAuth_Config['User_GroupTB']   = 'phpbb_user_group';  // Name of your PHPBB user_group table. (i.e. phpbb_user_group)
//$wgAuth_Config['PathToPHPBB']    = '../board/';         // Path from this file to your phpBB install. Must end with '/'.
//$wgAuth_Config['PathToRegistration'] = '../../catroid/registration';        // Path from this file to the catroid registration
//$wgAuth_Config['PathToLogin'] = '../../catroid/login';        // Path from this file to the catroid login
 
// Local
//$wgAuth_Config['LoginMessage']   = '<b>You need a Catroid account to log in to the Wiki.</b><br />
//									<a href="'.$wgAuth_Config['PathToRegistration'].'">
//									Click here to create an account.
//									</a><br />
//									<b>Please use the Catroid login-page.</b><br />
//									<a href="'.$wgAuth_Config['PathToRegistration'].'">
//									Click here to log in.
//									</a>'; // Localize this message.
//$wgAuth_Config['NoWikiError']    = 'You are not a member of the required phpBB group.'; // Localize this message.
//$wgAuth = new Auth_phpBB($wgAuth_Config);     // Auth_phpBB Plugin.