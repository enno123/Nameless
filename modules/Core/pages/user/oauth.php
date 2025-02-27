<?php

/*
 *	Made by Aberdeener
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  User OAuth page
 */

// Must be logged in
if (!$user->isLoggedIn()) {
    Redirect::to(URL::build('/'));
}

// Always define page name for navbar
const PAGE = 'cc_oauth';
$page_title = $language->get('user', 'user_cp');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

if (Input::exists()) {
    if (Token::check()) {
        $provider_name = Input::get('provider');

        if (Input::get('action') === 'unlink') {
            OAuth::getInstance()->unlinkProviderForUser($user->data()->id, $provider_name);
            Session::flash('oauth_success', $language->get('user', 'oauth_unlinked'));
        }
    } else {
        // Invalid token
        Session::flash('oauth_error', $language->get('general', 'invalid_token'));
    }
}

Session::put('oauth_method', 'link');

$providers = OAuth::getInstance()->getProvidersAvailable();
$user_providers = OAuth::getInstance()->getAllProvidersForUser($user->data()->id);

$user_providers_template = [];
foreach ($user_providers as $user_provider) {
    $user_providers_template[$user_provider->provider] = $user_provider;
}

$oauth_messsages = [];
foreach ($providers as $name => $data) {
    $oauth_messsages[$name] = [
        'unlink_confirm' => str_replace('{x}', ucfirst($name), $language->get('user', 'oauth_unlink_confirm')),
        'link_confirm' => str_replace('{x}', ucfirst($name), $language->get('user', 'oauth_link_confirm')),
    ];
}

if (Session::exists('oauth_success')) {
    $smarty->assign([
        'SUCCESS' => $language->get('general', 'success'),
        'SUCCESS_MESSAGE' => Session::flash('oauth_success'),
    ]);
}

if (Session::exists('oauth_error')) {
    $smarty->assign([
        'ERROR' => $language->get('general', 'error'),
        'ERROR_MESSAGE' => Session::flash('oauth_error'),
    ]);
}

$smarty->assign([
    'TOKEN' => Token::get(),
    'NO' => $language->get('general', 'no'),
    'YES' => $language->get('general', 'yes'),
    'CONFIRM' => $language->get('general', 'confirm'),
    'USER_CP' => $language->get('user', 'user_cp'),
    'OAUTH_PROVIDERS' => $providers,
    'NO_PROVIDERS' => $language->get('user', 'no_providers'),
    'USER_OAUTH_PROVIDERS' => $user_providers_template,
    'OAUTH' => $language->get('admin', 'oauth'),
    'LINK' => $language->get('general', 'link'),
    'UNLINK' => $language->get('general', 'unlink'),
    'OAUTH_MESSAGES' => $oauth_messsages,
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

require(ROOT_PATH . '/core/templates/cc_navbar.php');

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('user/oauth.tpl', $smarty);
