<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr8
 *
 *  License: MIT
 *
 *  Merge two topics together
 */

const PAGE = 'forum';
$page_title = $forum_language->get('forum', 'merge_topics');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

$forum = new Forum();

// User must be logged in to proceed
if (!$user->isLoggedIn()) {
    Redirect::to('/forum');
}

if (!isset($_GET['tid']) || !is_numeric($_GET['tid'])) {
    Redirect::to(URL::build('/forum/error/', 'error=not_exist'));
}

$topic_id = $_GET['tid'];
$forum_id = DB::getInstance()->selectQuery('SELECT forum_id FROM nl2_topics WHERE id = ?', [$topic_id])->first();
$forum_id = $forum_id->forum_id;

if ($forum->canModerateForum($forum_id, $user->getAllGroupIds())) {
    if (Input::exists()) {
        if (Token::check()) {
            $validation = Validate::check($_POST, [
                'merge' => [
                    Validate::REQUIRED => true
                ]
            ]);

            $posts_to_move = $queries->getWhere('posts', ['topic_id', '=', $topic_id]);
            if ($validation->passed()) {

                foreach ($posts_to_move as $post_to_move) {
                    $queries->update('posts', $post_to_move->id, [
                        'topic_id' => Input::get('merge')
                    ]);
                }
                $queries->delete('topics', ['id', '=', $topic_id]);
                Log::getInstance()->log(Log::Action('forums/merge'));
                // Update latest posts in categories
                $forum->updateForumLatestPosts();
                $forum->updateTopicLatestPosts();

                Redirect::to(URL::build('/forum/topic/' . urlencode(Input::get('merge'))));

            } else {
                echo 'Error processing that action. <a href="' . URL::build('/forum') . '">Forum index</a>';
            }
            die();
        }
    }
} else {
    Redirect::to(URL::build('/forum'));
}

$token = Token::get();

// Get topics
$topics = DB::getInstance()->selectQuery('SELECT * FROM nl2_topics WHERE forum_id = ? AND deleted = 0 AND id <> ? ORDER BY id ASC', [$forum_id, $topic_id])->results();

// Smarty
$smarty->assign([
    'MERGE_TOPICS' => $forum_language->get('forum', 'merge_topics'),
    'MERGE_INSTRUCTIONS' => $forum_language->get('forum', 'merge_instructions'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'CANCEL' => $language->get('general', 'cancel'),
    'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
    'CANCEL_LINK' => URL::build('/forum/topic/' . urlencode($topic_id)),
    'TOPICS' => $topics
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('forum/merge.tpl', $smarty);
