<?php

/***************************************************************************
 *
 *   OUGC Profile Activity plugin (/inc/plugins/ougc/ProfileActivity/Forum.php)
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

namespace ougc\ProfileActivity\Hooks\Forum;

use function ougc\ProfileActivity\Core\loadLanguage;

use function ougc\ProfileActivity\Core\getTemplate;

use function ougc\ProfileActivity\Core\getSetting;

use function ougc\ProfileActivity\Core\getPreview;

function global_start()
{
    global $templatelist;

    if (!isset($templatelist)) {
        $templatelist = '';
    } else {
        $templatelist .= ',';
    }

    if (\THIS_SCRIPT === 'member.php') {
        $templatelist .= 'ougcProfileActivity_PostIcon, ougcProfileActivity_PostsRow, ougcProfileActivity_PostsRowEmpty, ougcProfileActivity_PostsTable, ougcProfileActivity_ThreadsRow, ougcProfileActivity_ThreadsRowEmpty, ougcProfileActivity_ThreadsTable';
    }
}

function member_profile_end()
{
    global $mybb, $templates, $theme, $db, $lang;
    global $parser, $forum_cache, $memprofile;

    loadLanguage();

    $whereClause = ["t.closed NOT LIKE 'moved|%'", "t.visible='1'"];

    if ($unviewableForums = get_unviewable_forums(true)) {
        $whereClause[] = "t.fid NOT IN({$unviewableForums})";
    }

    if ($inactiveForums = get_inactive_forums()) {
        $whereClause[] = "t.fid NOT IN({$inactiveForums})";
    }

    if (!is_object($parser)) {
        require_once MYBB_ROOT . 'inc/class_parser.php';

        $parser = new \PostParser();
    }

    $profileUserID = (int)$memprofile['uid'];

    $dbQueryOptions = ['order_dir' => 'desc'];

    $userName = htmlspecialchars_uni($memprofile['username']);

    $forum_cache || cache_forums();

    $iconCache = $mybb->cache->read('posticons');

    foreach (
        [
            'Threads' => [
                'dbTables' => "threads t LEFT JOIN {$db->table_prefix}posts p on (p.pid=t.firstpost)",
                'dbFields' => 't.*, p.message, p.icon',
                'whereClauses' => ["t.uid='{$profileUserID}'"],
                'dbQueryOptions' => ['order_by' => 't.dateline'],
            ],
            'Posts' => [
                'dbTables' => "posts p LEFT JOIN {$db->table_prefix}threads t ON (t.tid=p.tid)",
                'dbFields' => 'p.*, t.prefix, t.replies, t.views',
                'whereClauses' => ["p.uid='{$profileUserID}'", 'p.visible=1', 'p.pid!=t.firstpost'],
                'dbQueryOptions' => ['order_by' => 'p.dateline'],
            ]
        ] as $activityType => $activityConfig
    ) {
        $memprofile["ougcProfileActivity{$activityType}"] = '';

        $maxItems = (int)getSetting("max{$activityType}");

        if ($maxItems < 1 || my_strpos(
                $templates->cache['member_profile'],
                "{\$memprofile['ougcProfileActivity{$activityType}']}"
            ) === false || (int)getSetting("ignored{$activityType}Forums") === -1) {
            continue;
        }

        if (getSetting("ignored{$activityType}Forums")) {
            $ignoredForums = implode(
                "','",
                array_map(
                    'intval',
                    explode(
                        ',',
                        getSetting("ignored{$activityType}Forums")
                    )
                )
            );

            if ($ignoredForums) {
                $whereClause['ignoredForums'] = "t.fid NOT IN('{$ignoredForums}')";
            }
        }

        $dbQueryOptions['limit'] = $maxItems;

        $dbQuery = $db->simple_select(
            $activityConfig['dbTables'],
            $activityConfig['dbFields'],
            implode(' AND ', array_merge($whereClause, $activityConfig['whereClauses'])),
            array_merge($dbQueryOptions, $activityConfig['dbQueryOptions'])
        );

        $activityContent = '';

        $rowBackground = alt_trow(true);

        while ($rowData = $db->fetch_array($dbQuery)) {
            $forumData = get_forum($rowData['fid']);

            $postIcon = '';

            if (!empty($rowData['icon']) && isset($iconCache[$rowData['icon']]) && !empty($forumData['allowpicons'])) {
                $icon = $iconCache[$rowData['icon']];

                $postIcon = eval(getTemplate('PostIcon'));

                unset($icon);
            }

            $threadPrefix = $displayPrefix = '';

            if (!empty($rowData['prefix'])) {
                $prefix = build_prefixes($rowData['prefix']);

                if (!empty($prefix['prefix'])) {
                    $threadPrefix = htmlspecialchars_uni($prefix['prefix']);

                    $displayPrefix = $prefix['displaystyle'];

                    unset($prefix);
                }
            }

            $threadUrl = get_thread_link($rowData['tid']);

            if (!empty($rowData['firstpost'])) {
                $postUrl = get_post_link($rowData['firstpost']);
            } else {
                $postUrl = get_post_link($rowData['pid']);
            }

            $fullSubject = $rowData['subject'] = htmlspecialchars_uni(
                $parser->parse_badwords($rowData['subject'])
            );

            if (my_strlen($rowData['subject']) + my_strlen($rowData['threadprefix']) > getSetting('maxTextLength')) {
                $rowData['subject'] = my_substr(
                        $rowData['subject'],
                        0,
                        getSetting('maxTextLength') - my_strlen($rowData['threadprefix'])
                    ) . '...';
            }

            $forumName = '';

            if (isset($forum_cache[$rowData['fid']])) {
                $forumName = strip_tags($forum_cache[$rowData['fid']]['name']);
            }

            $forumUrl = get_forum_link($rowData['fid']);

            $dateLine = my_date('relative', $rowData['dateline']);

            $rowData['replies'] = my_number_format($rowData['replies']);

            $rowData['views'] = my_number_format($rowData['views']);

            // "Thread Tooltip Preview" plugin support.
            if (!isset($rowData['postpreview'])) {
                $rowData['postpreview'] = $rowData['message'];
            }

            $postPreview = getPreview($rowData['postpreview'], getSetting('maxTextLength'));

            $activityContent .= eval(getTemplate("{$activityType}Row"));

            $rowBackground = alt_trow();
        }

        if (!$activityContent) {
            $emptyMessage = $lang->sprintf(
                $lang->{"ougcProfileActivityProfileTable{$activityType}Empty"},
                $userName
            );

            $activityContent = eval(getTemplate("{$activityType}RowEmpty"));
        }

        $tableTitle = $lang->sprintf(
            $lang->{"ougcProfileActivityProfileTable{$activityType}Title"},
            $userName
        );

        $memprofile["ougcProfileActivity{$activityType}"] = eval(getTemplate("{$activityType}Table"));
    }
}