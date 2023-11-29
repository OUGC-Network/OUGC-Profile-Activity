<?php

/***************************************************************************
 *
 *   OUGC Profile Activity plugin (/inc/plugins/ougc/ProfileActivity/Admin.php)
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

namespace ougc\ProfileActivity\Admin;

function pluginInfo(): array
{
    global $lang;

    \ougc\ProfileActivity\Core\loadLanguage();

    return [
        'name' => 'OUGC Profile Activity',
        'description' => $lang->ougcProfileActivityDescription,
        'website' => 'https://ougc.network',
        'author' => 'Omar G.',
        'authorsite' => 'https://ougc.network',
        'version' => '1.8.36',
        'versioncode' => 1836,
        'compatibility' => '18*',
        'codename' => 'ougc_profileactivity',
        'pl' => [
            'version' => 13,
            'url' => 'https://community.mybb.com/mods.php?action=view&pid=573'
        ],
    ];
}

function pluginActivate(): bool
{
    global $PL, $cache, $lang;

    \ougc\ProfileActivity\Core\loadLanguage();

    $pluginInfo = pluginInfo();

    \ougc\ProfileActivity\Core\loadPluginLibrary();

    // Add settings group
    $settingsContents = \file_get_contents(OUGC_PROFILEACTIVITY_ROOT . '/settings.json');

    $settingsData = \json_decode($settingsContents, true);

    foreach ($settingsData as $settingKey => &$settingData) {
        if (empty($lang->{"setting_ougc_profileactivity_{$settingKey}"})) {
            continue;
        }

        if ($settingData['optionscode'] == 'select') {
            foreach ($settingData['options'] as $optionKey) {
                $settingData['optionscode'] .= "\n{$optionKey}={$lang->{"setting_ougc_profileactivity_{$settingKey}_{$optionKey}"}}";
            }
        }

        $settingData['title'] = $lang->{"setting_ougc_profileactivity_{$settingKey}"};
        $settingData['description'] = $lang->{"setting_ougc_profileactivity_{$settingKey}_desc"};
    }

    $PL->settings(
        'ougc_profileactivity',
        $lang->setting_group_ougc_profileactivity,
        $lang->setting_group_ougc_profileactivity_desc,
        $settingsData
    );

    // Add templates
    $templatesDirIterator = new \DirectoryIterator(
        \OUGC_PROFILEACTIVITY_ROOT . '/Templates'
    );

    $templates = [];

    foreach ($templatesDirIterator as $template) {
        if (!$template->isFile()) {
            continue;
        }

        $pathName = $template->getPathname();

        $pathInfo = (object)pathinfo($pathName);

        if ($pathInfo->extension === 'html') {
            $templates[$pathInfo->filename] = file_get_contents($pathName);
        }
    }

    if ($templates) {
        $PL->templates('ougcprofileactivity', 'OUGC Profile Activity', $templates);
    }

    // Insert/update version into cache
    $plugins = (array)$cache->read('ougc_plugins');

    if (!isset($plugins['profileactivity'])) {
        $plugins['profileactivity'] = $pluginInfo['versioncode'];
    }

    /*~*~* RUN UPDATES START *~*~*/

    /*~*~* RUN UPDATES END *~*~*/

    $plugins['profileactivity'] = $pluginInfo['versioncode'];

    $cache->update('ougc_plugins', $plugins);

    return true;
}

function pluginIsInstalled(): bool
{
    static $isInstalled = null;

    if ($isInstalled === null) {
        global $cache;

        $plugins = (array)$cache->read('ougc_plugins');

        $isInstalled = isset($plugins['profileactivity']);
    }

    return $isInstalled;
}

function pluginUninstall(): bool
{
    global $db, $PL, $cache;

    \ougc\ProfileActivity\Core\loadPluginLibrary();

    $PL->settings_delete('ougc_profileactivity');

    $PL->templates_delete('ougcprofileactivity');

    // Delete version from cache
    $plugins = (array)$cache->read('ougc_plugins');

    if (isset($plugins['profileactivity'])) {
        unset($plugins['profileactivity']);
    }

    if (!empty($plugins)) {
        $cache->update('ougc_plugins', $plugins);
    } else {
        $PL->cache_delete('ougc_plugins');
    }

    return true;
}