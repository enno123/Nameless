<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 *
 *  Panel widgets page
 */

if (!$user->handlePanelPageLoad('admincp.widgets')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'panel';
const PARENT_PAGE = 'layout';
const PANEL_PAGE = 'widgets';
$page_title = $language->get('admin', 'widgets');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (!isset($_GET['action'])) {
    $template_array = [];

    // List widgets
    foreach ($widgets->getAll() as $widget) {
        $widget_query = $queries->getWhere('widgets', ['name', '=', $widget->getName()]);
        if (!count($widget_query)) {
            $queries->create(
                'widgets',
                [
                    'name' => $widget->getName()
                ]
            );
            $widget_query = $queries->getLastId();
        } else {
            $widget_query = $widget_query[0]->id;
        }

        $template_array[] = [
            'name' => Output::getClean($widget->getName()),
            'module' => str_replace('{x}', Output::getClean($widget->getModule()), $language->get('admin', 'module_x')),
            'description' => Output::getClean($widget->getDescription()),
            'enabled' => $widgets->isEnabled($widget),
            'disable_link' => (($widgets->isEnabled($widget)) ? URL::build('/panel/core/widgets/', 'action=disable&w=' . urlencode($widget_query)) : null),
            'enable_link' => ((!$widgets->isEnabled($widget)) ? URL::build('/panel/core/widgets/', 'action=enable&w=' . urlencode($widget_query)) : null),
            'settings_link' => (($widgets->isEnabled($widget)) ? URL::build('/panel/core/widgets/', 'action=edit&w=' . urlencode($widget_query)) : null)
        ];
    }

    $smarty->assign(
        [
            'ENABLE' => $language->get('admin', 'enable'),
            'DISABLE' => $language->get('admin', 'disable'),
            'EDIT' => $language->get('general', 'edit'),
            'WIDGETS_LIST' => $template_array
        ]
    );

    $template_file = 'core/widgets.tpl';
} else {
    if ($_GET['action'] == 'enable') {
        // Enable a widget
        if (!isset($_GET['w']) || !is_numeric($_GET['w'])) {
            die('Invalid widget!');
        }

        if (Token::check($_POST['token'])) {
            // Get widget name
            $name = $queries->getWhere('widgets', ['id', '=', $_GET['w']]);

            if (count($name)) {
                $name = Output::getClean($name[0]->name);
                $widget = $widgets->getWidget($name);

                if (!is_null($widget)) {
                    $queries->update(
                        'widgets',
                        $_GET['w'],
                        [
                            'enabled' => 1
                        ]
                    );

                    $widgets->enable($widget);

                    Session::flash('admin_widgets', $language->get('admin', 'widget_enabled'));
                }
            }

        } else {
            Session::flash('admin_widgets_error', $language->get('general', 'invalid_token'));
        }

        Redirect::to(URL::build('/panel/core/widgets'));
    }

    if ($_GET['action'] == 'disable') {
        // Disable a widget
        if (!isset($_GET['w']) || !is_numeric($_GET['w'])) {
            die('Invalid widget!');
        }

        if (Token::check($_POST['token'])) {
            // Get widget name
            $name = $queries->getWhere('widgets', ['id', '=', $_GET['w']]);
            if (count($name)) {
                $name = Output::getClean($name[0]->name);
                $widget = $widgets->getWidget($name);

                if (!is_null($widget)) {
                    $queries->update(
                        'widgets',
                        $_GET['w'],
                        [
                            'enabled' => 0
                        ]
                    );

                    $widgets->disable($widget);

                    Session::flash('admin_widgets', $language->get('admin', 'widget_disabled'));
                }
            }

        } else {
            Session::flash('admin_widgets_error', $language->get('general', 'invalid_token'));
        }

        Redirect::to(URL::build('/panel/core/widgets'));
    }

    if ($_GET['action'] == 'edit') {
        // Ensure widget exists
        if (!isset($_GET['w']) || !is_numeric($_GET['w'])) {
            Redirect::to(URL::build('/panel/core/widgets'));
        }

        $widget = $queries->getWhere('widgets', ['id', '=', $_GET['w']]);
        if (!count($widget)) {
            Redirect::to(URL::build('/panel/core/widgets'));
        }
        $widget = $widget[0];

        // Editing widget
        $active_pages = json_decode($widget->pages, true);

        if (Input::exists()) {
            if (Token::check()) {
                try {
                    // Updated pages list
                    if (isset($_POST['pages']) && count($_POST['pages'])) {
                        $active_pages = $_POST['pages'];
                    } else {
                        $active_pages = [];
                    }

                    $active_pages_string = json_encode($active_pages);

                    $order = ($_POST['order'] ?? 10);

                    $location = Input::get('location');
                    if (!in_array($location, ['left', 'right'])) {
                        $location = 'right';
                    }

                    $queries->update('widgets', $widget->id, ['pages' => $active_pages_string, '`order`' => $order, '`location`' => $location]);

                    Session::flash('admin_widgets', $language->get('admin', 'widget_updated'));
                    Redirect::to(URL::build('/panel/core/widgets/', 'action=edit&w=' . urlencode($widget->id)));
                } catch (Exception $e) {
                    $errors = [$e->getMessage()];
                }
            } else {
                $errors = [$language->get('general', 'invalid_token')];
            }
        }

        if (is_null($active_pages)) {
            $active_pages = [];
        }

        if ($widgets->getWidget($widget->name)->getSettings() != null) {
            $smarty->assign(
                [
                    'SETTINGS' => $language->get('admin', 'settings'),
                    'SETTINGS_LINK' => URL::build('/panel/core/widgets/', 'action=settings&w=' . urlencode($widget->id))
                ]
            );
        }

        $order = Output::getClean($widgets->getWidget($widget->name)->getOrder());
        if (!$order) {
            $order = 10;
        }

        $location = Output::getClean($widgets->getWidget($widget->name)->getLocation());
        if (!in_array($location, ['left', 'right'])) {
            $location = 'right';
        }

        $smarty->assign(
            [
                'EDITING_WIDGET' => str_replace('{x}', Output::getClean($widget->name), $language->get('admin', 'editing_widget_x')),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/core/widgets'),
                'ORDER' => $order,
                'WIDGET_ORDER' => $language->get('admin', 'widget_order'),
                'LOCATION' => $location,
                'WIDGET_LOCATION' => $language->get('admin', 'widget_location'),
                'LEFT' => $language->get('admin', 'left'),
                'RIGHT' => $language->get('admin', 'right'),
                'ACTIVE_PAGES' => $active_pages,
                'POSSIBLE_PAGES' => $pages->returnWidgetPages(),
                'MODULE' => $language->get('admin', 'module'),
                'MODULE_SEPERATOR' => '&raquo'
            ]
        );

        $template_file = 'core/widgets_edit.tpl';
    } else {
        if ($_GET['action'] == 'settings') {
            // Ensure widget exists
            if (!isset($_GET['w']) || !is_numeric($_GET['w'])) {
                Redirect::to(URL::build('/panel/core/widgets'));
            }

            $widget = $queries->getWhere('widgets', ['id', '=', $_GET['w']]);
            if (!count($widget)) {
                Redirect::to(URL::build('/panel/core/widgets'));
            }
            $widget = $widget[0];

            if (
                $widgets->getWidget($widget->name)->getSettings() == null
                || !file_exists($widgets->getWidget($widget->name)->getSettings())
            ) {
                Redirect::to(URL::build('/admin/widgets'));
            }

            require_once($widgets->getWidget($widget->name)->getSettings());

            $smarty->assign(
                [
                    'EDITING_WIDGET' => str_replace('{x}', Output::getClean($widget->name), $language->get('admin', 'editing_widget_x')),
                    'BACK' => $language->get('general', 'back'),
                    'BACK_LINK' => URL::build('/panel/core/widgets/', 'action=edit&w=' . urlencode($widget->id))
                ]
            );

            $template_file = 'core/widget_settings.tpl';
        } else {
            Redirect::to('/panel/core/widgets');
        }
    }
}

if (Session::exists('admin_widgets')) {
    $success = Session::flash('admin_widgets');
}

if (Session::exists('admin_widgets_error')) {
    $errors = [Session::flash('admin_widgets_error')];
}

if (isset($success)) {
    $smarty->assign(
        [
            'SUCCESS' => $success,
            'SUCCESS_TITLE' => $language->get('general', 'success')
        ]
    );
}

if (isset($errors) && count($errors)) {
    $smarty->assign(
        [
            'ERRORS' => $errors,
            'ERRORS_TITLE' => $language->get('general', 'error')
        ]
    );
}

$smarty->assign(
    [
        'PARENT_PAGE' => PARENT_PAGE,
        'DASHBOARD' => $language->get('admin', 'dashboard'),
        'LAYOUT' => $language->get('admin', 'layout'),
        'WIDGETS' => $language->get('admin', 'widgets'),
        'PAGE' => PANEL_PAGE,
        'TOKEN' => Token::get(),
        'SUBMIT' => $language->get('general', 'submit')
    ]
);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
