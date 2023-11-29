<?php

/***************************************************************************
 *
 *   OUGC Profile Activity plugin (/inc/plugins/ougc/ProfileActivity/Core.php)
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

namespace ougc\ProfileActivity\Core;

const DEBUG = false;

function loadLanguage(): bool
{
    global $lang;

    if (!isset($lang->ougcProfileActivity)) {
        $lang->load('ougc_profileactivity');
    }

    return true;
}

function pluginLibraryRequirements(): object
{
    return (object)\ougc\ProfileActivity\Admin\pluginInfo()['pl'];
}

function loadPluginLibrary(): bool
{
    global $PL, $lang;

    loadLanguage();

    $fileExists = file_exists(\PLUGINLIBRARY);

    if ($fileExists && !($PL instanceof \PluginLibrary)) {
        require_once \PLUGINLIBRARY;
    }

    if (!$fileExists || $PL->version < pluginLibraryRequirements()->version) {
        \flash_message(
            $lang->sprintf(
                $lang->ougcProfileActivityPluginLibraryWarning,
                pluginLibraryRequirements()->url,
                pluginLibraryRequirements()->version
            ),
            'error'
        );

        \admin_redirect('index.php?module=config-plugins');
    }

    return true;
}

function addHooks(string $namespace)
{
    global $plugins;

    $namespaceLowercase = strtolower($namespace);
    $definedUserFunctions = get_defined_functions()['user'];

    foreach ($definedUserFunctions as $callable) {
        $namespaceWithPrefixLength = strlen($namespaceLowercase) + 1;

        if (substr($callable, 0, $namespaceWithPrefixLength) == $namespaceLowercase . '\\') {
            $hookName = substr_replace($callable, '', 0, $namespaceWithPrefixLength);

            $priority = substr($callable, -2);

            if (is_numeric(substr($hookName, -2))) {
                $hookName = substr($hookName, 0, -2);
            } else {
                $priority = 10;
            }

            $plugins->add_hook($hookName, $callable, $priority);
        }
    }
}

function getSetting(string $settingKey = '')
{
    global $mybb;

    return isset(SETTINGS[$settingKey]) ? SETTINGS[$settingKey] : (
    isset($mybb->settings['ougc_profileactivity_' . $settingKey]) ? $mybb->settings['ougc_profileactivity_' . $settingKey] : false
    );
}

function getTemplate(string $templateName, bool $enableHTMLComments = true): string
{
    global $templates;

    if (DEBUG) {
        $filePath = \OUGC_PROFILEACTIVITY_ROOT . "/Templates/{$templateName}.html";

        $templateContents = file_get_contents($filePath);

        $templates->cache["ougcprofileactivity_{$templateName}"] = $templateContents;
    }

    return $templates->render("ougcprofileactivity_{$templateName}", true, $enableHTMLComments);
}

/*
* Shorts a message to look like a preview.
*
* @param string Message to short.
* @param int Maximum characters to show.
* @param bool Strip MyCode Quotes from message.
* @param bool Strip MyCode from message.
*/
function getPreview($message, $maxlen = 100, $stripquotes = true, $stripmycode = true)
{
    // Attempt to remove any [quote][/quote] MyCode alogn its content
    if ($stripquotes) {
        $message = preg_replace(array(
            "#\[quote=([\"']|&quot;|)(.*?)(?:\\1)(.*?)(?:[\"']|&quot;)?\](.*?)\[/quote\](\r\n?|\n?)#si",
            '#\[quote\](.*?)\[\/quote\](\r\n?|\n?)#si',
            '#\[quote\]#si',
            '#\[\/quote\]#si'
        ), '', $message);
    }

    // Attempt to remove any MyCode
    if ($stripmycode) {
        global $parser;
        if (!is_object($parser)) {
            require_once MYBB_ROOT . 'inc/class_parser.php';
            $parser = new postParser();
        }

        $message = $parser->parse_message($message, array(
            'allow_html' => 0,
            'allow_mycode' => 1,
            'allow_smilies' => 0,
            'allow_imgcode' => 1,
            'filter_badwords' => 1,
            'nl2br' => 0
        ));

        // before stripping tags, try converting some into spaces
        $message = preg_replace(array(
            '~\<(?:img|hr).*?/\>~si',
            '~\<li\>(.*?)\</li\>~si'
        ), array(' ', "\n* $1"), $message);

        $message = unhtmlentities(strip_tags($message));
    }

    // convert \xA0 to spaces (reverse &nbsp;)
    $message = trim(
        preg_replace(array('~ {2,}~', "~\n{2,}~"),
            array(' ', "\n"),
            strtr($message, array("\xA0" => ' ', "\r" => '', "\t" => ' ')))
    );

    // newline fix for browsers which don't support them
    $message = preg_replace("~ ?\n ?~", " \n", $message);

    // Shorten the message if too long
    if ($maxlen && my_strlen($message) > $maxlen) {
        $message = my_substr($message, 0, $maxlen - 1) . '...';
    }

    return htmlspecialchars_uni($message);
}