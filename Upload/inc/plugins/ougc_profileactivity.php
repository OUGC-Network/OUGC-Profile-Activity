<?php

/***************************************************************************
 *
 *   OUGC Profile Activity plugin (/inc/plugins/ougc_profileactivity.php)
 *   Author: Omar Gonzalez
 *   Copyright: © 2012 Omar Gonzalez
 *
 *   Website: https://ougc.network/
 *
 *   Show an overview of latest user threads or posts in profiles.
 *
 ***************************************************************************
 ****************************************************************************
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ****************************************************************************/

declare(strict_types=1);

// Die if IN_MYBB is not defined, for security reasons.
if (!defined('IN_MYBB')) {
    die('This file cannot be accessed directly.');
}

const OUGC_PROFILEACTIVITY_ROOT = \MYBB_ROOT . 'inc/plugins/ougc/ProfileActivity';

// Plugin Settings
define('ougc\ProfileActivity\Core\SETTINGS', [
    //'allowedGroups' => '-1'
]);

// PLUGINLIBRARY
if (!defined('PLUGINLIBRARY')) {
    define('PLUGINLIBRARY', \MYBB_ROOT . 'inc/plugins/pluginlibrary.php');
}

require_once OUGC_PROFILEACTIVITY_ROOT . '/Core.php';

if (defined('IN_ADMINCP')) {
    require_once OUGC_PROFILEACTIVITY_ROOT . '/Admin.php';

    require_once OUGC_PROFILEACTIVITY_ROOT . '/Hooks/Admin.php';

    \ougc\ProfileActivity\Core\addHooks('ougc\ProfileActivity\Hooks\Admin');
} else {
    require_once OUGC_PROFILEACTIVITY_ROOT . '/Hooks/Forum.php';

    \ougc\ProfileActivity\Core\addHooks('ougc\ProfileActivity\Hooks\Forum');
}

require_once OUGC_PROFILEACTIVITY_ROOT . '/Core.php';

function ougc_profileactivity_info(): array
{
    return \ougc\ProfileActivity\Admin\pluginInfo();
}

function ougc_profileactivity_activate(): bool
{
    return \ougc\ProfileActivity\Admin\pluginActivate();
}

function ougc_profileactivity_is_installed(): bool
{
    return \ougc\ProfileActivity\Admin\pluginIsInstalled();
}

function ougc_profileactivity_uninstall(): bool
{
    return \ougc\ProfileActivity\Admin\pluginUninstall();
}



/*
    require_once MYBB_ROOT . '/inc/adminfunctions_templates.php';
    find_replace_templatesets(
        'member_profile',
        '#' . preg_quote('{$signature}') . '#',
        '{$signature}{$memprofile[\'activity_threads\']}{$memprofile[\'activity_posts\']}'
    );
*/