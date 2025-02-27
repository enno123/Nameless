<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 *
 *  Initialisation file
 */

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/core/includes/constants/autoload.php';

// Nameless error handling
set_exception_handler([ErrorHandler::class, 'catchException']);
// catchError() used for throw_error or any exceptions which may be missed by catchException()
set_error_handler([ErrorHandler::class, 'catchError']);
register_shutdown_function([ErrorHandler::class, 'catchShutdownError']);

session_start();

// Page variable must be set
if (!isset($page)) {
    die('$page variable is unset. Cannot continue.');
}

if (!file_exists(ROOT_PATH . '/core/config.php')) {
    if (is_writable(ROOT_PATH . '/core')) {
        fopen(ROOT_PATH . '/core/config.php', 'w');
    } else {
        die('Your <strong>/core</strong> directory is not writable, please check your file permissions.');
    }
}

if (!file_exists(ROOT_PATH . '/cache/templates_c')) {
    try {
        mkdir(ROOT_PATH . '/cache/templates_c', 0777, true);
    } catch (Exception $e) {
        die('Unable to create <strong>/cache</strong> directories, please check your file permissions.');
    }
}

// Require config
require(ROOT_PATH . '/core/config.php');

if (isset($conf) && is_array($conf)) {
    $GLOBALS['config'] = $conf;
} else {
    if (!isset($GLOBALS['config'])) {
        $page = 'install';
    }
}

// If we're accessing the upgrade script don't initialise further
if (isset($_GET['route']) && rtrim($_GET['route'], '/') == '/panel/upgrade') {
    $pages = new Pages();
    $pages->add('Core', '/panel/upgrade', 'pages/panel/upgrade.php');
    return;
}

if ($page != 'install') {
    /*
     * Initialise
     */

    // Friendly URLs?
    define('FRIENDLY_URLS', Config::get('core/friendly') == 'true');

    // Set up cache
    $cache = new Cache(['name' => 'nameless', 'extension' => '.cache', 'path' => ROOT_PATH . '/cache/']);

    // Force https/www?
    if (Config::get('core/force_https')) {
        define('FORCE_SSL', true);
    }
    if (Config::get('core/force_www')) {
        define('FORCE_WWW', true);
    }

    if (defined('FORCE_SSL') && !Util::isConnectionSSL()) {
        if (defined('FORCE_WWW') && !str_contains($_SERVER['HTTP_HOST'], 'www.')) {
            header('Location: https://www.' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            die();
        }

        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        die();
    }

    if (defined('FORCE_WWW') && !str_contains($_SERVER['HTTP_HOST'], 'www.')) {
        if (!Util::isConnectionSSL()) {
            header('Location: http://www.' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        } else {
            header('Location: https://www.' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }
    }

    // Queries
    $queries = new Queries();

    // Page load timer?
    $cache->setCache('page_load_cache');
    $page_loading = $cache->retrieve('page_load');
    define('PAGE_LOADING', $page_loading);

    // Error reporting
    if (!defined('DEBUGGING')) {
        $cache->setCache('error_cache');
        if ($cache->isCached('error_reporting')) {
            if ($cache->retrieve('error_reporting') == 1) {
                // Enabled
                ini_set('display_startup_errors', 1);
                ini_set('display_errors', 1);
                error_reporting(-1);

                define('DEBUGGING', 1);
            } else {
                // Disabled
                error_reporting(0);
                ini_set('display_errors', 0);
            }
        } else {
            // Disable by default
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }

    $smarty = new Smarty();

    if ((defined('DEBUGGING') && DEBUGGING) && Composer\InstalledVersions::isInstalled('maximebf/debugbar')) {
        define('PHPDEBUGBAR', true);
        DebugBarHelper::getInstance()->enable($smarty);
    }

    // Configurations
    $configuration = new Configuration($cache);

    // Get the Nameless version
    $nameless_version = $queries->getWhere('settings', ['name', '=', 'nameless_version']);
    $nameless_version = $nameless_version[0]->value;
    define('NAMELESS_VERSION', $nameless_version);

    // Set the date format
    define('DATE_FORMAT', Config::get('core/date_format') ?: 'd M Y, H:i');

    // User initialisation
    $user = new User();
    // Do they need logging in (checked remember me)?
    if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
        $hash = Cookie::get(Config::get('remember/cookie_name'));
        $hashCheck = DB::getInstance()->get('users_session', ['hash', '=', $hash]);

        if ($hashCheck->count()) {
            $user = new User($hashCheck->first()->user_id);
            $user->login();
        }
    }

    // Check if we're in a subdirectory
    if (isset($directories)) {
        if (empty($directories[0])) {
            unset($directories[0]);
        }

        $directories = array_values($directories);

        $config_path = Config::get('core/path');

        if (!empty($config_path)) {
            $config_path = explode('/', Config::get('core/path'));

            for ($i = 0, $iMax = count($config_path); $i < $iMax; $i++) {
                unset($directories[$i]);
            }

            define('CONFIG_PATH', '/' . Config::get('core/path'));

            $directories = array_values($directories);
        }

        $directory = implode('/', $directories);

        $directory = '/' . $directory;

        // Remove the trailing /
        if (strlen($directory) > 1) {
            $directory = rtrim($directory, '/');
        }
    }

    // Set timezone
    if ($user->isLoggedIn()) {
        define('TIMEZONE', $user->data()->timezone);
    } else {
        $cache->setCache('timezone_cache');
        if ($cache->isCached('timezone')) {
            define('TIMEZONE', $cache->retrieve('timezone'));
        } else {
            define('TIMEZONE', 'Europe/London');
        }
    }

    date_default_timezone_set(TIMEZONE);

    // Language
    if (!$user->isLoggedIn() || !($user->data()->language_id)) {
        // Default language for guests
        $cache->setCache('languagecache');
        $language = $cache->retrieve('language');

        if (!$language) {
            define('LANGUAGE', 'EnglishUK');
            $language = new Language();
        } else {
            define('LANGUAGE', $language);
            $language = new Language('core', $language);
        }
    } else {
        // User selected language
        $language = $queries->getWhere('languages', ['id', '=', $user->data()->language_id]);
        if (!count($language)) {
            // Get default language
            $cache->setCache('languagecache');
            $language = $cache->retrieve('language');

            if (!$language) {
                define('LANGUAGE', 'EnglishUK');
                $language = new Language();
            } else {
                define('LANGUAGE', $language);
                $language = new Language('core', $language);
            }
        } else {
            define('LANGUAGE', $language[0]->name);
            $language = new Language('core', $language[0]->name);
        }
    }

    // Site name
    $cache->setCache('sitenamecache');
    $sitename = $cache->retrieve('sitename');

    if (!$sitename) {
        define('SITE_NAME', 'NamelessMC');
    } else {
        define('SITE_NAME', $sitename);
    }

    // Template
    if (!$user->isLoggedIn() || !($user->data()->theme_id)) {
        // Default template for guests
        $cache->setCache('templatecache');
        $template = $cache->retrieve('default');

        if (!$template) {
            define('TEMPLATE', 'DefaultRevamp');
        } else {
            define('TEMPLATE', $template);
        }
    } else {
        // User selected template
        $template = $queries->getWhere('templates', ['id', '=', $user->data()->theme_id]);
        if (!count($template)) {
            // Get default template
            $cache->setCache('templatecache');
            $template = $cache->retrieve('default');

            if (!$template) {
                define('TEMPLATE', 'DefaultRevamp');
            } else {
                define('TEMPLATE', $template);
            }
        } else {
            // Check permissions
            $template = $template[0];
            $hasPermission = false;

            if ($template->enabled) {
                $user_templates = $user->getUserTemplates();

                foreach ($user_templates as $user_template) {
                    if ($user_template->id === $template->id) {
                        $hasPermission = true;
                        define('TEMPLATE', $template->name);
                        break;
                    }
                }
            }

            if (!$hasPermission) {
                // Get default template
                $cache->setCache('templatecache');
                $template = $cache->retrieve('default');

                if (!$template) {
                    define('TEMPLATE', 'DefaultRevamp');
                } else {
                    define('TEMPLATE', $template);
                }
            }
        }
    }

    // Panel template
    $cache->setCache('templatecache');
    $template = $cache->retrieve('panel_default');

    if (!$template) {
        define('PANEL_TEMPLATE', 'Default');
    } else {
        define('PANEL_TEMPLATE', $template);
    }

    // Smarty
    $securityPolicy = new Smarty_Security($smarty);
    $securityPolicy->php_modifiers = [
        'escape',
        'count',
        'key',
        'round',
        'ucfirst',
        'defined',
        'date',
        'explode',
        'implode',
        'strtolower',
        'strtoupper'
    ];
    $securityPolicy->php_functions = [
        'isset',
        'empty',
        'count',
        'sizeof',
        'in_array',
        'is_array',
        'time',
        'nl2br',
        'is_numeric',
        'file_exists',
        'array_key_exists'
    ];
    $securityPolicy->secure_dir = [ROOT_PATH . '/custom/templates', ROOT_PATH . '/custom/panel_templates'];
    $smarty->enableSecurity($securityPolicy);

    // Basic Smarty variables
    $smarty->assign([
        'CONFIG_PATH' => defined('CONFIG_PATH') ? CONFIG_PATH . '/' : '/',
        'OG_URL' => Output::getClean(rtrim(Util::getSelfURL(), '/') . $_SERVER['REQUEST_URI']),
        'OG_IMAGE' => Output::getClean(rtrim(Util::getSelfURL(), '/') . '/core/assets/img/site_image.png'),
        'SITE_NAME' => SITE_NAME,
        'SITE_HOME' => URL::build('/'),
        'USER_INFO_URL' => URL::build('/queries/user/', 'id='),
        'GUEST' => $language->get('user', 'guest')
    ]);

    // Avatars
    $cache->setCache('avatar_settings_cache');
    if ($cache->isCached('custom_avatars') && $cache->retrieve('custom_avatars') == 1) {
        define('CUSTOM_AVATARS', true);
    }

    if ($cache->isCached('default_avatar_type')) {
        define('DEFAULT_AVATAR_TYPE', $cache->retrieve('default_avatar_type'));
        if (DEFAULT_AVATAR_TYPE == 'custom' && $cache->isCached('default_avatar_image')) {
            define('DEFAULT_AVATAR_IMAGE', $cache->retrieve('default_avatar_image'));
        } else {
            define('DEFAULT_AVATAR_IMAGE', '');
        }
    } else {
        define('DEFAULT_AVATAR_TYPE', 'minecraft');
    }

    if ($cache->isCached('avatar_source')) {
        define('DEFAULT_AVATAR_SOURCE', $cache->retrieve('avatar_source'));
    } else {
        define('DEFAULT_AVATAR_SOURCE', 'cravatar');
    }

    if ($cache->isCached('avatar_perspective')) {
        define('DEFAULT_AVATAR_PERSPECTIVE', $cache->retrieve('avatar_perspective'));
    } else {
        define('DEFAULT_AVATAR_PERSPECTIVE', 'face');
    }

    // Widgets
    $widgets = new Widgets($cache);

    // Maintenance mode?
    $cache->setCache('maintenance_cache');
    $maintenance = $cache->retrieve('maintenance');
    if (isset($maintenance['maintenance']) && $maintenance['maintenance'] != 'false') {
        // Enabled
        // Admins only beyond this point
        if (!$user->isLoggedIn() || !$user->canViewStaffCP()) {
            // Maintenance mode
            if (isset($_GET['route']) && (rtrim($_GET['route'], '/') == '/login' || rtrim($_GET['route'], '/') == '/forgot_password' || strpos($_GET['route'], '/api/') === 0)) {
                // Can continue as normal
            } else {
                require(ROOT_PATH . '/maintenance.php');
                die();
            }
        } else {
            // Display notice to admin stating maintenance mode is enabled
            $smarty->assign('MAINTENANCE_ENABLED', $language->get('admin', 'maintenance_enabled'));
        }
    }

    // Minecraft integration?
    $mc_integration = $queries->getWhere('settings', ['name', '=', 'mc_integration']);
    if (count($mc_integration) && $mc_integration[0]->value == '1') {
        define('MINECRAFT', true);
    } else {
        define('MINECRAFT', false);
    }

    // Navbar links
    $navigation = new Navigation();
    $cc_nav = new Navigation();
    $staffcp_nav = new Navigation(true); // $staffcp_nav = panel nav

    // Add links to cc_nav
    $cc_nav->add('cc_overview', $language->get('user', 'overview'), URL::build('/user'));
    $cc_nav->add('cc_alerts', $language->get('user', 'alerts'), URL::build('/user/alerts'));
    $cc_nav->add('cc_messaging', $language->get('user', 'messaging'), URL::build('/user/messaging'));
    $cc_nav->add('cc_connections', $language->get('user', 'connections'), URL::build('/user/connections'));
    $cc_nav->add('cc_settings', $language->get('user', 'profile_settings'), URL::build('/user/settings'));
    $cc_nav->add('cc_oauth', $language->get('admin', 'oauth'), URL::build('/user/oauth'));

    // Placeholders enabled?
    $placeholders_enabled = $configuration->get('Core', 'placeholders');
    if ($placeholders_enabled == 1) {
        $cc_nav->add('cc_placeholders', $language->get('user', 'placeholders'), URL::build('/user/placeholders'));
    }

    // Add homepage to navbar
    // Check navbar order + icon in cache
    $cache->setCache('navbar_order');
    if (!$cache->isCached('index_order')) {
        // Create cache entry now
        $home_order = 1;
        $cache->store('index_order', 1);
    } else {
        $home_order = $cache->retrieve('index_order');
    }

    $cache->setCache('navbar_icons');
    if ($cache->isCached('index_icon')) {
        $home_icon = $cache->retrieve('index_icon');
    } else {
        $home_icon = '';
    }

    $navigation->add('index', $language->get('general', 'home'), URL::build('/'), 'top', null, $home_order, $home_icon);

    // Endpoints
    $endpoints = new Endpoints();

    // Announcements
    $announcements = new Announcements($cache);

    // Modules
    $cache->setCache('modulescache');
    if (!$cache->isCached('enabled_modules')) {
        $cache->store('enabled_modules', [
            ['name' => 'Core', 'priority' => 1]
        ]);
        $cache->store('module_core', true);
    }
    $enabled_modules = $cache->retrieve('enabled_modules');

    foreach ($enabled_modules as $module) {
        if ($module['name'] == 'Core') {
            $core_exists = true;
            break;
        }
    }

    if (!isset($core_exists)) {
        $enabled_modules[] = [
            'name' => 'Core',
            'priority' => 1
        ];
    }

    $pages = new Pages();

    // Sort by priority
    usort($enabled_modules, static function ($a, $b) {
        return $a['priority'] - $b['priority'];
    });

    foreach ($enabled_modules as $module) {
        if (file_exists(ROOT_PATH . '/modules/' . $module['name'] . '/init.php')) {
            require(ROOT_PATH . '/modules/' . $module['name'] . '/init.php');
        }
    }

    // Get IP
    $ip = $user->getIP();

    // Perform tasks if the user is logged in
    if ($user->isLoggedIn()) {
        // Ensure a user is not banned
        if ($user->data()->isbanned == 1) {
            $user->logout();
            Session::flash('home_error', $language->get('user', 'you_have_been_banned'));
            Redirect::to(URL::build('/'));
        }

        // Is the IP address banned?
        $ip_bans = $queries->getWhere('ip_bans', ['ip', '=', $ip]);
        if (count($ip_bans)) {
            $user->logout();
            Session::flash('home_error', $language->get('user', 'you_have_been_banned'));
            Redirect::to(URL::build('/'));
        }

        // Update user last IP and last online
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $user->update([
                'last_online' => date('U'),
                'lastip' => $ip
            ]);
        } else {
            $user->update([
                'last_online' => date('U')
            ]);
        }

        // Insert it into the logs
        $user_ip_logged = $queries->getWhere('users_ips', ['ip', '=', $ip]);
        if (!count($user_ip_logged)) {
            // Create the entry now
            $queries->create('users_ips', [
                'user_id' => $user->data()->id,
                'ip' => $ip
            ]);
        } else {
            if (count($user_ip_logged) > 1) {
                foreach ($user_ip_logged as $user_ip) {
                    // Check to see if it's been logged by the current user
                    if ($user_ip->user_id == $user->data()->id) {
                        // Already logged for this user
                        $already_logged = true;
                        break;
                    }
                }

                if (!isset($already_logged)) {
                    // Not yet logged, do so now
                    $queries->create('users_ips', [
                        'user_id' => $user->data()->id,
                        'ip' => $ip
                    ]);
                }
            } else {
                // Does the entry already belong to the current user?
                if ($user_ip_logged[0]->user_id != $user->data()->id) {
                    $queries->create('users_ips', [
                        'user_id' => $user->data()->id,
                        'ip' => $ip
                    ]);
                }
            }
        }

        // Does their group have TFA forced?
        foreach ($user->getGroups() as $group) {
            if ($group->force_tfa) {
                $forced = true;
                break;
            }
        }

        if (isset($forced) && $forced) {
            // Do they have TFA configured?
            if (!$user->data()->tfa_enabled && rtrim($_GET['route'], '/') != '/logout') {
                if (!str_contains($_SERVER['REQUEST_URI'], 'do=enable_tfa')) {
                    Session::put('force_tfa_alert', $language->get('admin', 'force_tfa_alert'));
                    Redirect::to(URL::build('/user/settings', 'do=enable_tfa'));
                }
            }
        }

        $user_integrations = [];
        foreach ($user->getIntegrations() as $integrationUser) {
            $user_integrations[$integrationUser->getIntegration()->getName()] = [
                'username' => Output::getClean($integrationUser->data()->username),
                'identifier' => Output::getClean($integrationUser->data()->identifier)
            ];
        }

        // Basic user variables
        $smarty->assign('LOGGED_IN_USER', [
            'username' => $user->getDisplayname(true),
            'nickname' => $user->getDisplayname(),
            'profile' => $user->getProfileURL(),
            'panel_profile' => URL::build('/panel/user/' . urlencode($user->data()->id) . '-' . urlencode($user->data()->username)),
            'username_style' => $user->getGroupStyle(),
            'user_title' => Output::getClean($user->data()->user_title),
            'avatar' => $user->getAvatar(),
            'integrations' => $user_integrations
        ]);

        // Panel access?
        if ($user->canViewStaffCP()) {
            $smarty->assign([
                'PANEL_LINK' => URL::build('/panel'),
                'PANEL' => $language->get('moderator', 'staff_cp')
            ]);
        }
    } else {
        // Perform tasks for guests
        if (!$_SESSION['checked'] || (isset($_SESSION['checked']) && $_SESSION['checked'] <= strtotime('-5 minutes'))) {
            $already_online = $queries->getWhere('online_guests', ['ip', '=', $ip]);

            $date = date('U');

            if (count($already_online)) {
                $queries->update('online_guests', $already_online[0]->id, ['last_seen' => $date]);
            } else {
                $queries->create('online_guests', ['ip' => $ip, 'last_seen' => $date]);
            }

            $_SESSION['checked'] = $date;
        }
    }

    // Dark mode
    $cache->setCache('template_settings');
    $darkMode = $cache->isCached('darkMode') ? $cache->retrieve('darkMode') : '0';
    if ($user->isLoggedIn()) {
        $darkMode = $user->data()->night_mode !== null ? $user->data()->night_mode : $darkMode;
    } else {
        if (Cookie::exists('night_mode')) {
            $darkMode = Cookie::get('night_mode');
        }
    }

    define('DARK_MODE', $darkMode);
}
