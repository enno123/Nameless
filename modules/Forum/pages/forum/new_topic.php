<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr8
 *
 *  License: MIT
 *
 *  New topic page
 */

// Always define page name
const PAGE = 'forum';
$page_title = $forum_language->get('forum', 'new_topic');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// User must be logged in to proceed
if (!$user->isLoggedIn()) {
    Redirect::to(URL::build('/forum'));
}

$forum = new Forum();

if (!isset($_GET['fid']) || !is_numeric($_GET['fid'])) {
    Redirect::to(URL::build('/forum/error/', 'error=not_exist'));
}

$fid = (int)$_GET['fid'];

// Get user group ID
$user_groups = $user->getAllGroupIds();

// Does the forum exist, and can the user view it?
$list = $forum->forumExist($fid, $user_groups);
if (!$list) {
    Redirect::to(URL::build('/forum/error/', 'error=not_exist'));
}

// Can the user post a topic in this forum?
$can_reply = $forum->canPostTopic($fid, $user_groups);
if (!$can_reply) {
    Redirect::to(URL::build('/forum/view/' . urlencode($fid)));
}

$current_forum = DB::getInstance()->selectQuery('SELECT * FROM nl2_forums WHERE id = ?', [$fid])->first();
$forum_title = Output::getClean($current_forum->forum_title);

// Topic labels
$smarty->assign('LABELS_TEXT', $forum_language->get('forum', 'label'));
$labels = [];

$default_labels = $current_forum->default_labels ? explode(',', $current_forum->default_labels) : [];

$forum_labels = $queries->getWhere('forums_topic_labels', ['id', '<>', 0]);
if (count($forum_labels)) {
    foreach ($forum_labels as $label) {
        $forum_ids = explode(',', $label->fids);

        if (in_array($fid, $forum_ids)) {
            // Check permissions
            $lgroups = explode(',', $label->gids);

            $hasperm = false;
            foreach ($user_groups as $group_id) {
                if (in_array($group_id, $lgroups)) {
                    $hasperm = true;
                    break;
                }
            }

            if (!$hasperm) {
                continue;
            }

            // Get label HTML
            $label_html = $queries->getWhere('forums_labels', ['id', '=', $label->label]);
            if (!count($label_html)) {
                continue;
            }

            $label_html = str_replace('{x}', Output::getClean($label->name), Output::getPurified($label_html[0]->html));

            $labels[] = [
                'id' => $label->id,
                'html' => $label_html,
                'checked' => in_array($label->id, $default_labels)
            ];
        }
    }
}

// Deal with any inputted data
if (Input::exists()) {
    if (Token::check()) {
        // Check post limits
        $last_post = $queries->orderWhere('posts', 'post_creator = ' . $user->data()->id, 'post_date', 'DESC LIMIT 1');
        if (count($last_post)) {
            if ($last_post[0]->created > strtotime('-30 seconds')) {
                $spam_check = true;
            }
        }

        if (!isset($spam_check)) {
            // Spam check passed
            $validate = Validate::check($_POST, [
                'title' => [
                    Validate::REQUIRED => true,
                    Validate::MIN => 2,
                    Validate::MAX => 64
                ],
                'content' => [
                    Validate::REQUIRED => true,
                    Validate::MIN => 2,
                    Validate::MAX => 50000
                ]
            ])->messages([
                'title' => [
                    Validate::REQUIRED => $forum_language->get('forum', 'title_required'),
                    Validate::MIN => $forum_language->get('forum', 'title_min_2'),
                    Validate::MAX => $forum_language->get('forum', 'title_max_64')
                ],
                'content' => [
                    Validate::REQUIRED => $forum_language->get('forum', 'content_required'),
                    Validate::MIN => $forum_language->get('forum', 'content_min_2'),
                    Validate::MAX => $forum_language->get('forum', 'content_max_50000')
                ]
            ]);

            if ($validate->passed()) {
                $post_labels = [];

                if (isset($_POST['topic_label']) && !empty($_POST['topic_label']) && is_array($_POST['topic_label'])) {
                    foreach ($_POST['topic_label'] as $topic_label) {
                        $label = $queries->getWhere('forums_topic_labels', ['id', '=', $topic_label]);
                        if (count($label)) {
                            $lgroups = explode(',', $label[0]->gids);

                            $hasperm = false;
                            foreach ($user_groups as $group_id) {
                                if (in_array($group_id, $lgroups)) {
                                    $hasperm = true;
                                    break;
                                }
                            }

                            if ($hasperm) {
                                $post_labels[] = $label[0]->id;
                            }
                        }
                    }
                } else {
                    if (count($default_labels)) {
                        $post_labels = $default_labels;
                    }
                }

                $queries->create('topics', [
                    'forum_id' => $fid,
                    'topic_title' => Input::get('title'),
                    'topic_creator' => $user->data()->id,
                    'topic_last_user' => $user->data()->id,
                    'topic_date' => date('U'),
                    'topic_reply_date' => date('U'),
                    'labels' => implode(',', $post_labels)
                ]);
                $topic_id = $queries->getLastId();

                $content = Input::get('content');

                $queries->create('posts', [
                    'forum_id' => $fid,
                    'topic_id' => $topic_id,
                    'post_creator' => $user->data()->id,
                    'post_content' => $content,
                    'post_date' => date('Y-m-d H:i:s'),
                    'created' => date('U')
                ]);

                // Get last post ID
                $last_post_id = $queries->getLastId();
                $content = EventHandler::executeEvent('preTopicCreate', [
                    'content' => $content,
                    'post_id' => $last_post_id,
                    'topic_id' => $topic_id,
                    'user' => $user,
                ])['content'];

                $queries->update('posts', $last_post_id, [
                    'post_content' => $content
                ]);

                $queries->update('forums', $fid, [
                    'last_post_date' => date('U'),
                    'last_user_posted' => $user->data()->id,
                    'last_topic_posted' => $topic_id
                ]);

                Log::getInstance()->log(Log::Action('forums/topic/create'), Output::getClean(Input::get('title')));

                // Execute hooks and pass $available_hooks
                $available_hooks = $queries->getWhere('forums', ['id', '=', $fid]);
                $available_hooks = json_decode($available_hooks[0]->hooks);
                EventHandler::executeEvent('newTopic', [
                    'user_id' => Output::getClean($user->data()->id),
                    'username' => $user->getDisplayname(true),
                    'nickname' => $user->getDisplayname(),
                    'content' => str_replace(['{x}', '{y}'], [$forum_title, $user->getDisplayname()], $forum_language->get('forum', 'new_topic_text')),
                    'content_full' => strip_tags(str_ireplace(['<br />', '<br>', '<br/>'], "\r\n", Input::get('content'))),
                    'avatar_url' => $user->getAvatar(128, true),
                    'title' => Input::get('title'),
                    'url' => Util::getSelfURL() . ltrim(URL::build('/forum/topic/' . urlencode($topic_id) . '-' . $forum->titleToURL(Input::get('title'))), '/'),
                    'available_hooks' => $available_hooks == null ? [] : $available_hooks
                ]);

                Session::flash('success_post', $forum_language->get('forum', 'post_successful'));

                Redirect::to(URL::build('/forum/topic/' . urlencode($topic_id) . '-' . $forum->titleToURL(Input::get('title'))));
            } else {
                $error = $validate->errors();
            }
        } else {
            $error = [str_replace('{x}', (strtotime($last_post[0]->post_date) - strtotime('-30 seconds')), $forum_language->get('forum', 'spam_wait'))];
        }
    } else {
        $error = [$language->get('general', 'invalid_token')];
    }
}

// Generate a token
$token = Token::get();

$template->addCSSFiles([
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/prism/prism_' . (DARK_MODE ? 'dark' : 'light_default') . '.css' => [],
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/tinymce/plugins/spoiler/css/spoiler.css' => [],
]);

// Generate content for template
if (isset($error)) {
    $smarty->assign('ERROR', $error);
}

$creating_topic_in = str_replace('{x}', $forum_title, $forum_language->get('forum', 'creating_topic_in_x'));
$smarty->assign('CREATING_TOPIC_IN', $creating_topic_in);

// Get info about forum
$forum_query = $queries->getWhere('forums', ['id', '=', $fid]);
$forum_query = $forum_query[0];

// Placeholder?
if ($forum_query->topic_placeholder) {
    $placeholder = Output::getPurified($forum_query->topic_placeholder);
}

// Smarty variables
$smarty->assign([
    'LABELS' => $labels,
    'TOPIC_TITLE' => $forum_language->get('forum', 'topic_title'),
    'LABEL' => $forum_language->get('forum', 'label'),
    'SUBMIT' => $language->get('general', 'submit'),
    'CANCEL' => $language->get('general', 'cancel'),
    'CLOSE' => $language->get('general', 'close'),
    'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
    'YES' => $language->get('general', 'yes'),
    'NO' => $language->get('general', 'no'),
    'TOKEN' => '<input type="hidden" name="token" value="' . $token . '">',
    'FORUM_LINK' => URL::build('/forum'),
    'CONTENT_LABEL' => $language->get('general', 'content'),
    'FORUM_TITLE' => Output::getClean($forum_title),
    'FORUM_DESCRIPTION' => Output::getPurified($forum_query->forum_description),
    'NEWS_FORUM' => $forum_query->news
]);

$content = $_POST['content'] ?? $forum_query->topic_placeholder ?? null;
if ($content) {
    // Purify post content
    $content = EventHandler::executeEvent('renderPostEdit', ['content' => $content])['content'];
}

$template->addJSFiles([
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/prism/prism.js' => [],
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/tinymce/plugins/spoiler/js/spoiler.js' => [],
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/tinymce/tinymce.min.js' => []
]);

$template->addJSScript(Input::createTinyEditor($language, 'reply', $content, true));

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('forum/new_topic.tpl', $smarty);
