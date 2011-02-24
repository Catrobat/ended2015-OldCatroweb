<?php
/**
 *
 * @package phpBB Mobile
 * @author Callum95 (Callum Macrae) callum@lynxphp.com
 * @version $Id$
 * @copyright (c) 2011 lynxphp.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/
class acp_mobile_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_mobile',
			'title'		=> 'ACP_MOBILE',
			'version'	=> '2.0.0',
			'modes'		=> array(
				'words'		=> array('title' => 'ACP_MOBILE', 'auth' => 'acl_a_styles', 'cat' => array('ACP_STYLE_MANAGEMENT')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>