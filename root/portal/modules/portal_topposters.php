<?php
/**
* @package Portal - Topposters
* @version $Id$
* @copyright (c) 2009, 2010 Board3 Portal Team
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package Topposters
*/
class portal_topposters_module
{
	/**
	* Allowed columns: Just sum up your options (Exp: left + right = 10)
	* top		1
	* left		2
	* center	4
	* right		8
	* bottom	16
	*/
	var $columns = 10;

	/**
	* Default modulename
	*/
	var $name = 'TOPPOSTERS';

	/**
	* Default module-image:
	* file must be in "{T_THEME_PATH}/images/portal/"
	*/
	var $image_src = 'topposters.png';

	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/portal/"
	*/
	var $language = 'portal_topposters_module';

	function get_template_center($module_id)
	{
		return false;
	}

	function get_template_side($module_id)
	{
		global $config, $db, $template;
		global $phpbb_root_path, $phpEx;

		$sql = 'SELECT user_id, username, user_posts, user_colour
			FROM ' . USERS_TABLE . '
			WHERE user_type <> ' . USER_IGNORE . "
				AND user_posts <> 0
				AND username <> ''
			ORDER BY user_posts DESC";
		$result = $db->sql_query_limit($sql, $config['portal_topposters']);

		while (($row = $db->sql_fetchrow($result)))
		{
			$template->assign_block_vars('topposters', array(
				'S_SEARCH_ACTION'	=> append_sid("{$phpbb_root_path}search.$phpEx", 'author_id=' . $row['user_id'] . '&amp;sr=posts'),
				'USERNAME_FULL'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'POSTER_POSTS'		=> $row['user_posts'],
			));
		}
		$db->sql_freeresult($result);

		return 'topposters_side.html';
	}

	function get_template_acp($module_id)
	{
		return array(
			'title'	=> 'TOPPOSTERS_CONFIG',
			'vars'	=> array(
				'legend1'				=> 'TOPPOSTERS',
				'portal_topposters'		=> array('lang' => 'NUM_TOPPOSTERS',		'validate' => 'int',	'type' => 'text:3:3',		'explain' => true),
			),
		);
	}

	/**
	* API functions
	*/
	function install($module_id)
	{
		set_config('portal_topposters', 5);
		return true;
	}

	function uninstall($module_id)
	{
		global $db;

		$del_config = array(
			'portal_topposters',
		);
		$sql = 'DELETE FROM ' . CONFIG_TABLE . '
			WHERE ' . $db->sql_in_set('config_name', $del_config);
		return $db->sql_query($sql);
	}
}

?>