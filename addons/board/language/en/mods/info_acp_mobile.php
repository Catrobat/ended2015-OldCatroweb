<?php
/**
*
* phpBB Mobile [English]
*
* @author Callum Macrae (Callum95) http://callum.x10hosting.com
*
*
* @package acp
* @version $Id: info_acp_mobile.php 03/05/2010 10:32:00 GMT $
* @copyright (c) 2010 Callum Macrae
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_MOBILE'			=> 'phpBB Mobile',
	'ACP_MOBILE_EXPLAIN'	=> 'phpBB Mobile is a fully automatic modification which, when a user accesses your forum using a mobile device, sends them a mobile optimised version of the forum. The style was built using iWebKit, so is designed for iOS devices such as the iPhone and iPad, but it also works with other phones with webkit based browsers or browsers that support css3, meaning it will work for Android, BlackBerries, etc.<br /><br />Although it is automatic, you can still find some configuration settings: the ability to disable and the ability to change the regex that the modification checks for. Do not change the regex unless you know what you are doing.',
	'MOBILE_CHANGE'			=> 'phpBB Mobile settings changed',
	'EDIT_MOBILE'			=> 'Edit phpBB Mobile settings:',
	'MOBILE_ENABLED'		=> 'phpBB Mobile enabled?',
	'MOBILE_AGENTS'			=> 'User agent regex',
));

?>