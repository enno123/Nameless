<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr11
 *
 *  License: MIT
 *
 *  Panel custom pages page
 */

if (!$user->handlePanelPageLoad('admincp.pages')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'panel';
const PARENT_PAGE = 'pages';
const PANEL_PAGE = 'custom_pages';
$page_title = $language->get('admin', 'custom_pages');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (!isset($_GET['action'])) {
    $custom_pages = $queries->getWhere('custom_pages', ['id', '<>', 0]);
    $template_array = [];

    if (count($custom_pages)) {
        foreach ($custom_pages as $custom_page) {
            $template_array[] = [
                'id' => Output::getClean($custom_page->id),
                'edit_link' => URL::build('/panel/core/pages/', 'action=edit&id=' . urlencode($custom_page->id)),
                'title' => Output::getClean($custom_page->title)
            ];
        }
    }

    $smarty->assign([
        'NEW_PAGE' => $language->get('admin', 'new_page'),
        'NEW_PAGE_LINK' => URL::build('/panel/core/pages/', 'action=new'),
        'EDIT' => $language->get('general', 'edit'),
        'DELETE' => $language->get('general', 'delete'),
        'NO_CUSTOM_PAGES' => $language->get('admin', 'no_custom_pages'),
        'CUSTOM_PAGE_LIST' => $template_array,
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_DELETE_PAGE' => $language->get('admin', 'confirm_delete_page'),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no'),
        'DELETE_LINK' => URL::build('/panel/core/pages', 'action=delete'),
    ]);

    $template_file = 'core/pages.tpl';

} else {
    switch ($_GET['action']) {
        case 'new':
            if (Input::exists()) {
                $errors = [];

                if (Token::check()) {
                    $validation = Validate::check($_POST, [
                        'page_title' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 255
                        ],
                        'page_url' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 255
                        ],
                        'content' => [
                            Validate::MAX => 100000
                        ],
                        'link_location' => [
                            Validate::REQUIRED => true
                        ],
                        'redirect_link' => [
                            Validate::MAX => 512
                        ]
                    ])->messages([
                        'page_title' => [
                            Validate::REQUIRED => $language->get('admin', 'page_title_required'),
                            Validate::MIN => $language->get('admin', 'page_title_minimum_2'),
                            Validate::MAX => $language->get('admin', 'page_title_maximum_255')
                        ],
                        'page_url' => [
                            Validate::REQUIRED => $language->get('admin', 'page_url_required'),
                            Validate::MIN => $language->get('admin', 'page_url_minimum_2'),
                            Validate::MAX => $language->get('admin', 'page_url_maximum_255')
                        ],
                        'content' => $language->get('admin', 'page_content_maximum_100000'),
                        'link_location' => [
                            Validate::REQUIRED => $language->get('admin', 'link_location_required')
                        ],
                        'redirect_link' => $language->get('admin', 'page_redirect_link_maximum_512')
                    ]);

                    if ($validation->passed()) {
                        try {
                            // Get link location
                            if (isset($_POST['link_location'])) {
                                switch ($_POST['link_location']) {
                                    case 1:
                                    case 2:
                                    case 3:
                                    case 4:
                                        $location = $_POST['link_location'];
                                        break;
                                    default:
                                        $location = 1;
                                }
                            } else {
                                $location = 1;
                            }

                            $redirect = intval(isset($_POST['redirect_page']) && $_POST['redirect_page'] == 'on');
                            $target = intval(isset($_POST['target']) && $_POST['target'] == 'on');
                            $link = $_POST['redirect_link'] ?? '';
                            $unsafe = intval(isset($_POST['unsafe_html']) && $_POST['unsafe_html'] == 'on');
                            $sitemap = intval(isset($_POST['sitemap']) && $_POST['sitemap'] == 'on');
                            $basic = intval(isset($_POST['basic']) && $_POST['basic'] == 'on');

                            $queries->create('custom_pages', [
                                'url' => rtrim(Input::get('page_url'), '/'),
                                'title' => Input::get('page_title'),
                                'content' => Input::get('content'),
                                'link_location' => $location,
                                'redirect' => $redirect,
                                'link' => $link,
                                'target' => $target,
                                'all_html' => $unsafe,
                                'sitemap' => $sitemap,
                                'basic' => $basic,
                            ]);

                            $last_id = $queries->getLastId();

                            // Permissions
                            $perms = [];
                            if (isset($_POST['perm-view-0']) && $_POST['perm-view-0'] == 1) {
                                $perms[0] = 1;
                            } else {
                                $perms[0] = 0;
                            }

                            foreach (Group::all() as $group) {
                                if (isset($_POST['perm-view-' . $group->id]) && $_POST['perm-view-' . $group->id] == 1) {
                                    $perms[$group->id] = 1;
                                } else {
                                    $perms[$group->id] = 0;
                                }
                            }

                            foreach ($perms as $key => $perm) {
                                $queries->create('custom_pages_permissions', [
                                    'page_id' => $last_id,
                                    'group_id' => $key,
                                    'view' => $perm
                                ]);
                            }

                            Session::flash('admin_pages', $language->get('admin', 'page_created_successfully'));
                            Redirect::to(URL::build('/panel/core/pages'));
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }

                    } else {
                        $errors = $validation->errors();
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            $template_array = [];
            foreach (Group::all() as $group) {
                $template_array[$group->id] = [
                    'id' => $group->id,
                    'name' => Output::getClean($group->name),
                    'html' => $group->group_html
                ];
            }

            $smarty->assign([
                'CANCEL' => $language->get('general', 'cancel'),
                'CANCEL_LINK' => URL::build('/panel/core/pages'),
                'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
                'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
                'YES' => $language->get('general', 'yes'),
                'NO' => $language->get('general', 'no'),
                'CREATING_PAGE' => $language->get('admin', 'creating_new_page'),
                'PAGE_TITLE' => $language->get('admin', 'page_title'),
                'PAGE_TITLE_VALUE' => Output::getClean(Input::get('page_title')),
                'PAGE_PATH' => $language->get('admin', 'page_path'),
                'PAGE_PATH_VALUE' => Output::getClean(Input::get('page_url')),
                'PAGE_LINK_LOCATION' => $language->get('admin', 'page_link_location'),
                'PAGE_LINK_NAVBAR' => $language->get('admin', 'page_link_navbar'),
                'PAGE_LINK_MORE' => $language->get('admin', 'page_link_more'),
                'PAGE_LINK_FOOTER' => $language->get('admin', 'page_link_footer'),
                'PAGE_LINK_NONE' => $language->get('admin', 'page_link_none'),
                'PAGE_CONTENT' => $language->get('admin', 'page_content'),
                'PAGE_CONTENT_VALUE' => Output::getClean(Input::get('content')),
                'BASIC_PAGE' => $language->get('admin', 'basic_page'),
                'PAGE_REDIRECT' => $language->get('admin', 'page_redirect'),
                'PAGE_REDIRECT_TO' => $language->get('admin', 'page_redirect_to'),
                'PAGE_REDIRECT_TO_VALUE' => Output::getClean(Input::get('redirect_link')),
                'TARGET' => $language->get('admin', 'page_target'),
                'UNSAFE_HTML' => $language->get('admin', 'unsafe_html'),
                'UNSAFE_HTML_WARNING' => $language->get('admin', 'unsafe_html_warning'),
                'INCLUDE_IN_SITEMAP' => $language->get('admin', 'include_in_sitemap'),
                'PAGE_PERMISSIONS' => $language->get('admin', 'page_permissions'),
                'GROUP' => $language->get('admin', 'group'),
                'VIEW_PAGE' => $language->get('admin', 'view_page'),
                'GUESTS' => $language->get('user', 'guests'),
                'GROUPS' => $template_array
            ]);

            $template_file = 'core/pages_new.tpl';

            break;

        case 'edit':
            // Get page
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                Redirect::to(URL::build('/panel/core/pages'));
            }
            $page = $queries->getWhere('custom_pages', ['id', '=', $_GET['id']]);
            if (!count($page)) {
                Redirect::to(URL::build('/panel/core/pages'));
            }
            $page = $page[0];

            // Handle input
            if (Input::exists()) {
                $errors = [];

                if (Token::check()) {
                    $validation = Validate::check($_POST, [
                        'page_title' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 255
                        ],
                        'page_url' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 255
                        ],
                        'content' => [
                            Validate::MAX => 100000
                        ],
                        'link_location' => [
                            Validate::REQUIRED => true
                        ],
                        'redirect_link' => [
                            Validate::MAX => 512
                        ]
                    ])->messages([
                        'page_title' => [
                            Validate::REQUIRED => $language->get('admin', 'page_title_required'),
                            Validate::MIN => $language->get('admin', 'page_title_minimum_2'),
                            Validate::MAX => $language->get('admin', 'page_title_maximum_255')
                        ],
                        'page_url' => [
                            Validate::REQUIRED => $language->get('admin', 'page_url_required'),
                            Validate::MIN => $language->get('admin', 'page_url_minimum_2'),
                            Validate::MAX => $language->get('admin', 'page_url_maximum_255')
                        ],
                        'content' => $language->get('admin', 'page_content_maximum_100000'),
                        'link_location' => [
                            Validate::REQUIRED => $language->get('admin', 'link_location_required')
                        ],
                        'redirect_link' => $language->get('admin', 'page_redirect_link_maximum_512')
                    ]);

                    if ($validation->passed()) {
                        try {
                            // Get link location
                            if (isset($_POST['link_location'])) {
                                switch ($_POST['link_location']) {
                                    case 1:
                                    case 2:
                                    case 3:
                                    case 4:
                                        $location = $_POST['link_location'];
                                        break;
                                    default:
                                        $location = 1;
                                }
                            } else {
                                $location = 1;
                            }

                            $redirect = intval(isset($_POST['redirect_page']) && $_POST['redirect_page'] == 'on');
                            $target = intval(isset($_POST['target']) && $_POST['target'] == 'on');
                            $link = $_POST['redirect_link'] ?? '';
                            $unsafe = intval(isset($_POST['unsafe_html']) && $_POST['unsafe_html'] == 'on');
                            $sitemap = intval(isset($_POST['sitemap']) && $_POST['sitemap'] == 'on');
                            $basic = intval(isset($_POST['basic']) && $_POST['basic'] == 'on');

                            $queries->update('custom_pages', $page->id, [
                                'url' => rtrim(Input::get('page_url'), '/'),
                                'title' => Input::get('page_title'),
                                'content' => Input::get('content'),
                                'link_location' => $location,
                                'redirect' => $redirect,
                                'link' => $link,
                                'target' => $target,
                                'all_html' => $unsafe,
                                'sitemap' => $sitemap,
                                'basic' => $basic
                            ]);

                            // Update all widget and announcement page arrays with the custom pages' new name
                            $widget_query = $queries->getWhere('widgets', ['id', '<>', 0]);
                            if (count($widget_query)) {
                                foreach ($widget_query as $widget_row) {
                                    $pages = json_decode($widget_row->pages, true);
                                    $new_pages = [];
                                    if (is_array($pages) && count($pages)) {
                                        foreach ($pages as $widget_page) {
                                            if ($page->title == $widget_page) {
                                                $new_pages[] = Input::get('page_title');
                                            } else {
                                                $new_pages[] = $widget_page;
                                            }
                                        }
                                        $queries->update('widgets', $widget_row->id, [
                                            'pages' => json_encode($new_pages)
                                        ]);
                                    }
                                }
                            }
                            $announcement_query = $queries->getWhere('custom_announcements', ['id', '<>', 0]);
                            if (count($announcement_query)) {
                                foreach ($announcement_query as $announcement_row) {
                                    $pages = json_decode($announcement_row->pages, true);
                                    $new_pages = [];
                                    if (count($pages)) {
                                        foreach ($pages as $announcement_page) {
                                            if ($page->title == $announcement_page) {
                                                $new_pages[] = Input::get('page_title');
                                            } else {
                                                $new_pages[] = $announcement_page;
                                            }
                                        }
                                        $queries->update('custom_announcements', $announcement_row->id, [
                                            'pages' => json_encode($new_pages)
                                        ]);
                                    }
                                }
                            }

                            // Permissions
                            // Guest first
                            $view = Input::get('perm-view-0');

                            if (!($view)) {
                                $view = 0;
                            }

                            $page_perm_exists = 0;

                            $page_perm_query = $queries->getWhere('custom_pages_permissions', ['page_id', '=', $page->id]);
                            if (count($page_perm_query)) {
                                foreach ($page_perm_query as $query) {
                                    if ($query->group_id == 0) {
                                        $page_perm_exists = 1;
                                        $update_id = $query->id;
                                        break;
                                    }
                                }
                            }

                            try {
                                if ($page_perm_exists != 0) { // Permission already exists, update
                                    // Update the category
                                    $queries->update('custom_pages_permissions', $update_id, [
                                        'view' => $view
                                    ]);
                                } else { // Permission doesn't exist, create
                                    $queries->create('custom_pages_permissions', [
                                        'group_id' => 0,
                                        'page_id' => $page->id,
                                        'view' => $view
                                    ]);
                                }

                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }

                            // Group category permissions
                            foreach (Group::all() as $group) {
                                $view = Input::get('perm-view-' . $group->id);

                                if (!($view)) {
                                    $view = 0;
                                }

                                $page_perm_exists = 0;

                                if (count($page_perm_query)) {
                                    foreach ($page_perm_query as $query) {
                                        if ($query->group_id == $group->id) {
                                            $page_perm_exists = 1;
                                            $update_id = $query->id;
                                            break;
                                        }
                                    }
                                }

                                try {
                                    if ($page_perm_exists != 0) { // Permission already exists, update
                                        // Update the category
                                        $queries->update('custom_pages_permissions', $update_id, [
                                            'view' => $view
                                        ]);
                                    } else { // Permission doesn't exist, create
                                        $queries->create('custom_pages_permissions', [
                                            'group_id' => $group->id,
                                            'page_id' => $page->id,
                                            'view' => $view
                                        ]);
                                    }

                                } catch (Exception $e) {
                                    $errors[] = $e->getMessage();
                                }
                            }

                            Session::flash('admin_pages', $language->get('admin', 'page_updated_successfully'));
                            Redirect::to(URL::build('/panel/core/pages'));
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }

                    } else {
                        $errors = $validation->errors();
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            $group_permissions = DB::getInstance()->selectQuery('SELECT id, `name`, group_html, subquery.view AS `view` FROM nl2_groups LEFT JOIN (SELECT `view`, group_id FROM nl2_custom_pages_permissions WHERE page_id = ?) AS subquery ON nl2_groups.id = subquery.group_id ORDER BY `order`', [$page->id])->results();
            $template_array = [];
            foreach ($group_permissions as $group) {
                $template_array[Output::getClean($group->id)] = [
                    'id' => Output::getClean($group->id),
                    'name' => Output::getClean($group->name),
                    'html' => $group->group_html,
                    'view' => $group->view
                ];
            }

            $guest_permissions = DB::getInstance()->selectQuery('SELECT `view` FROM nl2_custom_pages_permissions WHERE group_id = 0 AND page_id = ?', [$page->id])->results();
            $guest_can_view = 0;
            if (count($guest_permissions)) {
                if ($guest_permissions[0]->view == 1) {
                    $guest_can_view = 1;
                }
            }

            $smarty->assign([
                'CANCEL' => $language->get('general', 'cancel'),
                'CANCEL_LINK' => URL::build('/panel/core/pages'),
                'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
                'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
                'YES' => $language->get('general', 'yes'),
                'NO' => $language->get('general', 'no'),
                'EDITING_PAGE' => str_replace('{x}', Output::getClean($page->title), $language->get('admin', 'editing_page_x')),
                'PAGE_TITLE' => $language->get('admin', 'page_title'),
                'PAGE_TITLE_VALUE' => (isset($_POST['page_title']) ? Output::getClean(Input::get('page_title')) : Output::getClean($page->title)),
                'PAGE_PATH' => $language->get('admin', 'page_path'),
                'PAGE_PATH_VALUE' => (isset($_POST['page_url']) ? Output::getClean(Input::get('page_url')) : Output::getClean($page->url)),
                'PAGE_LINK_LOCATION' => $language->get('admin', 'page_link_location'),
                'PAGE_LINK_LOCATION_VALUE' => $page->link_location,
                'PAGE_LINK_NAVBAR' => $language->get('admin', 'page_link_navbar'),
                'PAGE_LINK_MORE' => $language->get('admin', 'page_link_more'),
                'PAGE_LINK_FOOTER' => $language->get('admin', 'page_link_footer'),
                'PAGE_LINK_NONE' => $language->get('admin', 'page_link_none'),
                'PAGE_CONTENT' => $language->get('admin', 'page_content'),
                'PAGE_CONTENT_VALUE' => (isset($_POST['content']) ? Output::getClean(Input::get('content')) : Output::getClean($page->content)),
                'PAGE_REDIRECT' => $language->get('admin', 'page_redirect'),
                'PAGE_REDIRECT_VALUE' => $page->redirect,
                'PAGE_REDIRECT_TO' => $language->get('admin', 'page_redirect_to'),
                'PAGE_REDIRECT_TO_VALUE' => (isset($_POST['redirect_link']) ? Output::getClean(Input::get('redirect_link')) : $page->link),
                'TARGET' => $language->get('admin', 'page_target'),
                'TARGET_VALUE' => $page->target,
                'UNSAFE_HTML' => $language->get('admin', 'unsafe_html'),
                'UNSAFE_HTML_VALUE' => $page->all_html,
                'UNSAFE_HTML_WARNING' => $language->get('admin', 'unsafe_html_warning'),
                'INCLUDE_IN_SITEMAP' => $language->get('admin', 'include_in_sitemap'),
                'INCLUDE_IN_SITEMAP_VALUE' => $page->sitemap,
                'BASIC_PAGE' => $language->get('admin', 'basic_page'),
                'BASIC_PAGE_VALUE' => $page->basic,
                'PAGE_PERMISSIONS' => $language->get('admin', 'page_permissions'),
                'GROUP' => $language->get('admin', 'group'),
                'VIEW_PAGE' => $language->get('admin', 'view_page'),
                'GUESTS' => $language->get('user', 'guests'),
                'GROUPS' => $template_array,
                'GUEST_PERMS' => $guest_can_view
            ]);

            $template_file = 'core/pages_edit.tpl';

            break;

        case 'delete':
            if (Input::exists()) {
                if (Token::check(Input::get('token'))) {
                    if (isset($_POST['id']) && is_numeric($_POST['id'])) {

                        $queries->delete('custom_pages', ['id', '=', $_POST['id']]);
                        $queries->delete('custom_pages_permissions', ['page_id', '=', $_POST['id']]);

                        Session::flash('admin_pages', $language->get('admin', 'page_deleted_successfully'));
                    }
                } else {
                    Session::flash('admin_pages_error', $language->get('general', 'invalid_token'));
                }
            }
            die();

        default:
            Redirect::to(URL::build('/panel/core/pages'));
    }
}

if (Session::exists('admin_pages')) {
    $success = Session::flash('admin_pages');
}

if (Session::exists('admin_pages_error')) {
    $errors = [Session::flash('admin_pages_error')];
}

if (isset($success)) {
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);
}

if (isset($errors) && count($errors)) {
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);
}

$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'PAGES' => $language->get('admin', 'pages'),
    'CUSTOM_PAGES' => $language->get('admin', 'custom_pages'),
    'PAGE' => PANEL_PAGE,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
