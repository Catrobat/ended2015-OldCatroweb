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

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_mobile
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $template;

		// Set up general vars
		$mobile_enabled = request_var('enabled', false);
		$mobile_agents = request_var('agents', (string) false);
		
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			set_config('mobile_agents', $mobile_agents);
			set_config('mobile_enabled', $mobile_enabled);
		}

		$this->tpl_name = 'acp_mobile';
		$this->page_title = 'ACP_MOBILE';

		$form_name = 'acp_mobile';
		add_form_key($form_name);

		$template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,
			'MOBILE_ENABLED'	=> $config['mobile_enabled'],
			'MOBILE_AGENTS'		=> $config['mobile_agents'],
		));
	}
}

?>
