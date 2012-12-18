<?php

/***************************************************************************
 *
 *   OUGC Profile Activity plugin (/inc/plugins/ougc_profileactivity.php)
 *	 Author: Omar Gonzalez
 *   Copyright: © 2012 Omar Gonzalez
 *   
 *   Website: http://community.mybb.com/user-25096.html
 *
 *   Show an overview of latest threads and/or posts in profile.
 *
 ***************************************************************************
 
****************************************************************************
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/

// Die if IN_MYBB is not defined, for security reasons.
defined('IN_MYBB') or die('This file cannot be accessed directly.');

// Add our hooks
if(!defined('IN_ADMINCP') && defined('THIS_SCRIPT') && THIS_SCRIPT == 'member.php')
{
	global $templatelist, $mybb;

	// Profile hook
	$plugins->add_hook('member_profile_end', 'ougc_profileactivity');

	if(isset($mybb->input['action']) && $mybb->input['action'] == 'profile')
	{
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		else
		{
			$templatelist = '';
		}

		$templatelist .= 'ougcprofileactivity_icon, ougcprofileactivity_threads_thread, ougcprofileactivity_empty, ougcprofileactivity_threads, ougcprofileactivity_posts_post, ougcprofileactivity_posts';
	}
}

// Necessary plugin information for the ACP plugin manager.
function ougc_profileactivity_info()
{
	global $lang;
    $lang->load('ougc_profileactivity');

	return array(
		'name'			=> 'OUGC Profile Activity',
		'description'	=> $lang->ougc_profileactivity_d,
		'website'		=> 'http://mods.mybb.com/profile/25096',
		'author'		=> 'Omar Gonzalez',
		'authorsite'	=> 'http://community.mybb.com/user-25096.html',
		'version'		=> '1.0',
		'compatibility'	=> '16*',
		'guid'			=> ''
	);
}

// Activate plugin
function ougc_profileactivity_activate()
{
	global $PL, $lang;
    $lang->load('ougc_profileactivity');
	ougc_profileactivity_install();

	// Add our settings
	$PL->settings('ougc_profileactivity', $lang->ougc_profileactivity, $lang->ougc_profileactivity_d, array(
		'maxthreads'	=> array(
			'title'			=> $lang->ougc_profileactivity_maxthreads,
			'description'	=> $lang->ougc_profileactivity_maxthreads_d,
			'optionscode'	=> 'text',
			'value'			=> 10,
		),
		'maxposts'	=> array(
			'title'			=> $lang->ougc_profileactivity_maxposts,
			'description'	=> $lang->ougc_profileactivity_maxposts_d,
			'optionscode'	=> 'text',
			'value'			=> 10,
		),
		'forums'	=> array(
			'title'			=> $lang->ougc_profileactivity_forums,
			'description'	=> $lang->ougc_profileactivity_forums_d,
			'optionscode'	=> 'text',
			'value'			=> '',
		),
		'maxlengh'	=> array(
			'title'			=> $lang->ougc_profileactivity_maxlengh,
			'description'	=> $lang->ougc_profileactivity_maxlengh_d,
			'optionscode'	=> 'text',
			'value'			=> 100,
		),
		'posticon'	=> array(
			'title'			=> $lang->ougc_profileactivity_posticon,
			'description'	=> $lang->ougc_profileactivity_posticon_d,
			'optionscode'	=> 'text',
			'value'			=> 1,
		),
		'prefixes'	=> array(
			'title'			=> $lang->ougc_profileactivity_prefixes,
			'description'	=> $lang->ougc_profileactivity_prefixes_d,
			'optionscode'	=> 'yesno',
			'value'			=> 1,
		),
	));

	// Insert template/group
	$PL->templates('ougcprofileactivity', $lang->ougc_profileactivity, array(
		'empty'	=> '<tr><td class="trow1">{$lang->ougc_profileactivity_empty}</td></tr>',
		'icon'	=> '<img src="{$icon[\'path\']}" title="{$icon[\'name\']}" alt="{$icon[\'name\']}" style="vertical-align: middle;" />',
		'posts'	=> '<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$lang->ougc_profileactivity_titlep}</strong></td>
</tr>
{$post_list}
</table>',
		'posts_post'	=> '<tr>
<td class="{$alt_trow}">
{$post[\'icon\']} <strong><a href="{$mybb->settings[\'bburl\']}/{$post[\'postlink\']}" title="{$post[\'fullsubject\']}">{$post[\'subject\']}</a></strong><br />
<span class="smalltext">{$lang->ougc_profileactivity_postedin}: <a href="{$mybb->settings[\'bburl\']}/{$post[\'forumlink\']}" title="{$post[\'forumname\']}">{$post[\'forumname\']}</a>, {$post[\'dateline\']}</span><br />
<span class="smalltext">{$post[\'postpreview\']}</span>
</td>
</tr>',
		'threads'	=> '<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$lang->ougc_profileactivity_titlet}</strong></td>
</tr>
{$thread_list}
</table>',
		'threads_thread'	=> '<tr>
<td class="{$alt_trow}">
{$thread[\'icon\']} <strong>{$thread[\'displayprefix\']}<a href="{$mybb->settings[\'bburl\']}/{$thread[\'threadlink\']}" title="{$thread[\'fullsubject\']}">{$thread[\'subject\']}</a></strong><br />
<span class="smalltext">{$lang->ougc_profileactivity_postedin}: <a href="{$mybb->settings[\'bburl\']}/{$thread[\'forumlink\']}" title="{$thread[\'forumname\']}">{$thread[\'forumname\']}</a>, {$thread[\'dateline\']}</span><br class="clear" />
<span class="float_right smalltext">{$lang->ougc_profileactivity_replies}: {$thread[\'replies\']}, {$lang->ougc_profileactivity_views}: {$thread[\'views\']}</span><br />
<span class="smalltext">{$thread[\'postpreview\']}</span>
</td>
</tr>'
	));

	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('member_profile', '#'.preg_quote('{$signature}').'#', '{$signature}{$memprofile[\'activity_threads\']}{$memprofile[\'activity_posts\']}');
}

// Activate plugin
function ougc_profileactivity_deactivate()
{
	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('member_profile', '#'.preg_quote('{$memprofile[\'activity_threads\']}').'#', '', 0);
	find_replace_templatesets('member_profile', '#'.preg_quote('{$memprofile[\'activity_posts\']}').'#', '', 0);
}

// Install the plugin
function ougc_profileactivity_install()
{
	global $lang;
    $lang->load('ougc_profileactivity');
	$info = ougc_profileactivity_info();

	if(!file_exists(PLUGINLIBRARY))
	{
		flash_message($lang->sprintf($lang->ougc_profileactivity_plreq, $info['pl_url'], $info['pl_version']), 'error');
		admin_redirect('index.php?module=config-plugins');
	}

	global $PL;
	$PL or require_once PLUGINLIBRARY;

	if($PL->version < $info['pl_version'])
	{
		flash_message($lang->sprintf($lang->ougc_profileactivity_plold, $PL->version, $info['pl_version'], $info['pl_url']), 'error');
		admin_redirect('index.php?module=config-plugins');
	}
}

// Check if installed
function ougc_profileactivity_is_installed()
{
	global $settings;

	return isset($settings['ougc_profileactivity_maxthreads']);
}

// Uninstall the plugin
function ougc_profileactivity_uninstall()
{
	global $PL;
	ougc_profileactivity_install();

	// Delete setting group.
	$PL->settings_delete('ougc_profileactivity');

	// Delete any old templates.
	$PL->templates_delete('ougcprofileactivity');
}

//******************************FORUM******************************\\
// Show activity in profiles
function ougc_profileactivity()
{
	global $mybb, $memprofile, $templates;

	$memprofile['activity_threads'] = $memprofile['activity_posts'] = '';

	$ttc = (bool)my_strpos($templates->cache['member_profile'], '{$memprofile[\'activity_threads\']}');
	$ptc = (bool)my_strpos($templates->cache['member_profile'], '{$memprofile[\'activity_posts\']}');
	$thread_limit = (int)$mybb->settings['ougc_profileactivity_maxthreads'];
	$posts_limit = (int)$mybb->settings['ougc_profileactivity_maxposts'];

	// Return if no activated.
	if($thread_limit < 1 && $posts_limit < 1 || !$ttc && !$ptc)
	{
		return;
	}

	global $parser, $db, $theme, $lang, $forum_cache;
	$icon_cache = $mybb->cache->read('posticons');
    $lang->load('ougc_profileactivity');
	$forum_cache or cache_forums();

	if(!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new PostParser;
	}

	// Get unviewable forum list to build the where clause.
	$unviewableforums = get_unviewable_forums(true);
	$where = '';
	if($inactiveforums)
	{
		$where .= ' AND fid NOT IN('.$inactiveforums.')';
	}

	// Get the list of forums to ignore
	if(!empty($mybb->settings['ougc_profileactivity_forums']))
	{
		$ignoreforums = explode(',', $mybb->settings['ougc_profileactivity_forums']);
		$ignoreforums = implode(',', array_map('intval', $ignoreforums));
		if($ignoreforums)
		{
			$where .= ' AND fid NOT IN('.$ignoreforums.')';
		}
	}

	// Titles..
	$username = htmlspecialchars_uni($memprofile['username']);

	// Query settings
	$order_options = array('order_dir' => 'desc');

	$uid = (int)$memprofile['uid'];
	$max_lengh = (int)$mybb->settings['ougc_profileactivity_maxlengh'];

	// Threads list.
	if($thread_limit && $ttc)
	{
		$lang->ougc_profileactivity_titlet = $lang->sprintf($lang->ougc_profileactivity_titlet, $username);
		$order_options['order_by'] = 't.dateline';
		$order_options['limit'] = $thread_limit;
		$query = $db->simple_select('threads t LEFT JOIN '.TABLE_PREFIX.'posts p on (t.firstpost=p.pid)', 't.*, p.message', 't.uid=\''.$uid.'\' AND t.closed NOT LIKE \'moved|%\' AND t.visible=\'1\''.$where, $order_options);

		$thread_list = '';
		while($thread = $db->fetch_array($query))
		{
			// Determine the background color.
			$alt_trow = alt_trow();

			// Common variables...
			$thread['forumname'] = strip_tags($forum_cache[$thread['fid']]['name']);
			$thread['replies'] = my_number_format($thread['replies']);
			$thread['views'] = my_number_format($thread['views']);
			$thread['forumlink'] = get_forum_link($thread['fid']);
			$thread['threadlink'] = get_thread_link($thread['tid']);

			// Get thread prefix.
			$thread['threadprefix'] = $thread['displayprefix'] = '';
			if($thread['prefix'])
			{
				$prefix = build_prefixes($thread['prefix']);
				if($prefix['prefix'])
				{
					$thread['threadprefix'] = htmlspecialchars_uni($prefix['prefix']);
					$thread['displayprefix'] = $prefix['displaystyle'].' ';
					unset($prefix);
				}
			}

			// Sanitize and get some thread subject
			$thread['fullsubject'] = $thread['subject'] = htmlspecialchars_uni($parser->parse_badwords($thread['subject']));
			$subject_lengh = my_strlen($thread['subject']);
			$prefix_lengh = my_strlen($thread['threadprefix']);
			if($subject_lengh+$prefix_lengh > $max_lengh)
			{
				$thread['subject'] = my_substr($thread['subject'], 0, $max_lengh-$prefix_lengh).'...';
			}

			// Format Time.
			$thread['dateline'] = $lang->sprintf($lang->ougc_profileactivity_dateline, my_date($mybb->settings['dateformat'], $thread['dateline']), my_date($mybb->settings['timeformat'], $thread['dateline']));

			// "Thread Tooltip Preview" plugin support.
			if(!$thread['postpreview'])
			{
				$thread['postpreview'] = $thread['message'];
			}
			$thread['postpreview'] = ougc_profileactivity_get_preview($thread['postpreview'], $max_lengh);

			// Get thread icon
			if(!$thread['icon'] || !isset($icon_cache[$thread['icon']]))
			{
				$thread['icon'] = (int)$mybb->settings['ougc_profileactivity_posticons'];
			}
			if($thread['icon'] && $icon_cache[$thread['icon']])
			{
				$icon = $icon_cache[$thread['icon']];
				eval('$thread[\'icon\'] = "'.$templates->get('ougcprofileactivity_icon').'";');
				unset($icon);
			}
			else
			{
				$thread['icon'] = '';
			}

			eval('$thread_list .= "'.$templates->get('ougcprofileactivity_threads_thread').'";');
		}
		

		if(!$thread_list)
		{
			eval('$thread_list = "'.$templates->get('ougcprofileactivity_empty').'";');
		}

		eval('$memprofile[\'activity_threads\'] = "'.$templates->get('ougcprofileactivity_threads').'";');
	}

	// Posts list.
	if($posts_limit && $ptc)
	{
		$lang->ougc_profileactivity_titlep = $lang->sprintf($lang->ougc_profileactivity_titlep, $username);
		$order_options['limit'] = $posts_limit;
		$order_options['order_by'] = 'dateline';
		$query = $db->simple_select('posts', '*', 'uid=\''.$uid.'\' AND visible=\'1\''.$where, $order_options);

		$post_list = '';
		while($post = $db->fetch_array($query))
		{
			// Determine the backgroun color.
			$alt_trow = alt_trow();
			$alt_trow = alt_trow();

			// Common variables...
			$post['forumname'] = $forum_cache[$post['fid']]['name'];
			$post['forumlink'] = get_forum_link($post['fid']);
			$post['postlink'] = get_post_link($post['pid'], $post['tid'])."#pid{$post['pid']}";

			// Sanitize and get some thread subject
			$post['fullsubject'] = $post['subject'] = htmlspecialchars_uni($parser->parse_badwords($post['subject']));
			if(my_strlen($post['subject']) > $max_lengh)
			{
				$post['subject'] = my_substr($post['subject'], 0, $max_lengh).'...';
			}

			// Format Time.
			$post['dateline'] = $lang->sprintf($lang->ougc_profileactivity_dateline, my_date($mybb->settings['dateformat'], $post['dateline']), my_date($mybb->settings['timeformat'], $post['dateline']));

			// Get a preview if possible, by default it is not.
			$post['postpreview'] = ougc_profileactivity_get_preview($post['message']);

			// Get post icon
			if(!$post['icon'] || !isset($icon_cache[$post['icon']]))
			{
				$post['icon'] = (int)$mybb->settings['ougc_profileactivity_posticons'];
			}
			if($post['icon'] && $icon_cache[$post['icon']])
			{
				$icon = $icon_cache[$post['icon']];
				eval('$post[\'icon\'] = "'.$templates->get('ougcprofileactivity_icon').'";');
				unset($icon);
			}
			else
			{
				$post['icon'] = '';
			}

			eval('$post_list .= "'.$templates->get('ougcprofileactivity_posts_post').'";');
		}

		if(!$post_list)
		{
			$lang->ougc_profileactivity_empty = $lang->ougc_profileactivity_emptyp;
			eval('$post_list = "'.$templates->get('ougcprofileactivity_empty').'";');
		}

		eval('$memprofile[\'activity_posts\'] = "'.$templates->get('ougcprofileactivity_posts').'";');
	}
}

/*
* Shorts a message to look like a preview.
*
* @param string Message to short.
* @param int Maximum characters to show.
* @param bool Strip MyCode Quotes from message.
* @param bool Strip MyCode from message.
*/
function ougc_profileactivity_get_preview($message, $maxlen=100, $stripquotes=true, $stripmycode=true)
{
	// Attempt to remove any [quote][/quote] MyCode alogn its content
	if($stripquotes)
	{
		$message = preg_replace(array(
			'#\[quote=([\"\']|&quot;|)(.*?)(?:\\1)(.*?)(?:[\"\']|&quot;)?\](.*?)\[/quote\](\r\n?|\n?)#esi',
			'#\[quote\](.*?)\[\/quote\](\r\n?|\n?)#si',
			'#\[quote\]#si',
			'#\[\/quote\]#si'
		), '', $message);
	}

	// Attempt to remove any MyCode
	if($stripmycode)
	{
		global $parser;
		if(!is_object($parser))
		{
			require_once MYBB_ROOT.'inc/class_parser.php';
			$parser = new postParser;
		}

		$message = $parser->parse_message($message, array(
			'allow_html'		=>	0,
			'allow_mycode'		=>	1,
			'allow_smilies'		=>	0,
			'allow_imgcode'		=>	1,
			'filter_badwords'	=>	1,
			'nl2br'				=>	0
		));

		// before stripping tags, try converting some into spaces
		$message = preg_replace(array(
			'~\<(?:img|hr).*?/\>~si',
			'~\<li\>(.*?)\</li\>~si'
		), array(' ', "\n* $1"), $message);

		$message = unhtmlentities(strip_tags($message));
	}

	// convert \xA0 to spaces (reverse &nbsp;)
	$message = trim(preg_replace(array('~ {2,}~', "~\n{2,}~"), array(' ', "\n"), strtr($message, array("\xA0" => ' ', "\r" => '', "\t" => ' '))));

	// newline fix for browsers which don't support them
	$message = preg_replace("~ ?\n ?~", " \n", $message);

	// Shorten the message if too long
	if($maxlen && my_strlen($message) > $maxlen)
	{
		$message = my_substr($message, 0, $maxlen-1).'...';
	}

	return htmlspecialchars_uni($message);
}