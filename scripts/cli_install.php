<?php
/*
 * there is NO SUPPORT offered for this script.
 * this script is provided AS IS and without any warranty.
 * this script was made with the primary goal of making the install process automatic for hosting providers + our API test suite.
 */

function getEnvVar(string $name, string $fallback = null) {
    $value = getenv($name);
    $required = $fallback === null;

    if ($value === false && $required) {
        print("⚠️  Required environment variable '$name' is not set!" . PHP_EOL);
        exit(1);
    }

    if (!$value && $fallback !== null) {
        $value = $fallback;
        print("ℹ️  Environment variable '$name' is not set, using fallback '$fallback'" . PHP_EOL);
    }

    return $value;
}

if (PHP_SAPI !== 'cli') {
    die('This script must be run from the command line.');
}

if (!isset($argv[1]) || $argv[1] !== '--iSwearIKnowWhatImDoing') {
    print("🚫 You don't know what you're doing." . PHP_EOL);
    exit(1);
}

print(PHP_EOL);

$reinstall = false;
if (isset($argv[2]) && $argv[2] == '--reinstall') {
    $reinstall = true;
    print('🧨 Reinstall mode enabled! ' . PHP_EOL . PHP_EOL);
}

if (!file_exists('./vendor/autoload.php')) {
    print('⚠️  You need to run "composer install" first!' . PHP_EOL);
    exit(1);
}

if (!$reinstall && file_exists('./core/config.php')) {
    print('⚠️  NamelessMC is already installed! ' . PHP_EOL);
    print("🧨 If you want to reinstall, run this script with the '--reinstall' flag." . PHP_EOL);
    exit(1);
}

// check all the required environment variables are set
foreach (['NAMELESS_SITE_NAME', 'NAMELESS_SITE_CONTACT_EMAIL', 'NAMELESS_SITE_OUTGOING_EMAIL', 'NAMELESS_ADMIN_EMAIL'] as $var) {
    getEnvVar($var);
}

$start = microtime(true);

print('🗑  Deleting cache directories...' . PHP_EOL);
// clear the cache directories
$folders = [
    './cache',
    './cache/templates_c'
];
foreach ($folders as $folder) {
    if (is_dir($folder)) {
        $files = glob($folder . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

if ($reinstall) {
    print('🗑  Deleting old config.php file...' . PHP_EOL);
    // delete the core/config.php file
    if (is_file('./core/config.php')) {
        unlink('./core/config.php');
    }
}

const ROOT_PATH = __DIR__;

print('✍️  Creating new config.php file...' . PHP_EOL);
$conf = [
    'mysql' => [
        'host' => getEnvVar('NAMELESS_DATABASE_ADDRESS', '127.0.0.1'),
        'port' => getEnvVar('NAMELESS_DATABASE_PORT', '3306'),
        'username' => getEnvVar('NAMELESS_DATABASE_USERNAME', 'root'),
        'password' => getEnvVar('NAMELESS_DATABASE_PASSWORD', ''),
        'db' => getEnvVar('NAMELESS_DATABASE_NAME', 'nameless'),
        'prefix' => 'nl2_',
        'charset' => getEnvVar('NAMELESS_DATABASE_CHARSET', 'utf8mb4'),
        'engine' => getEnvVar('NAMELESS_DATABASE_ENGINE', 'InnoDB'),
        'initialise_charset' => true,
    ],
    'remember' => [
        'cookie_name' => 'nl2',
        'cookie_expiry' => 604800,
    ],
    'session' => [
        'session_name' => '2user',
        'admin_name' => '2admin',
        'token_name' => '2token',
    ],
    'core' => [
        'hostname' => getEnvVar('NAMELESS_HOSTNAME', 'localhost'),
        'path' => getEnvVar('NAMELESS_PATH', ''),
        'friendly' => getEnvVar('NAMELESS_FRIENDLY_URLS', 'false') === 'true' ? true : false,
        'force_https' => false,
        'force_www' => false,
        'captcha' => false,
    ],
    'allowedProxies' => '',
];

file_put_contents(
    './core/config.php',
    '<?php' . PHP_EOL . '$conf = ' . var_export($conf, true) . ';'
);
$GLOBALS['config'] = $conf;

print('♻️  Registering autoloader...' . PHP_EOL);
require './vendor/autoload.php';

if ($reinstall) {
    print('🗑️  Deleting old database...' . PHP_EOL);
    $instance = DB_Custom::getInstance($conf['mysql']['host'], $conf['mysql']['db'], $conf['mysql']['username'], $conf['mysql']['password'], $conf['mysql']['port']);
    $instance->createQuery('DROP DATABASE IF EXISTS `' . $conf['mysql']['db'] . '`');
    print('✍️  Creating new database...' . PHP_EOL);
    $instance->createQuery('CREATE DATABASE `' . $conf['mysql']['db'] . '`');
}

print('✍️  Creating tables...' . PHP_EOL);
$queries = new Queries();
$queries->dbInitialise('utf8mb4');

Session::put('default_language', getEnvVar('NAMELESS_DEFAULT_LANGUAGE', 'EnglishUK'));

$nameless_terms = 'This website uses "Nameless" website software. The ' .
    '"Nameless" software creators will not be held responsible for any content ' .
    'that may be experienced whilst browsing this site, nor are they responsible ' .
    'for any loss of data which may come about, for example a hacking attempt. ' .
    'The website is run independently from the software creators, and any content' .
    ' is the responsibility of the website administration.';

print('✍️  Inserting default data to database...' . PHP_EOL);
require './core/installation/includes/site_initialize.php';
$sitename = getEnvVar('NAMELESS_SITE_NAME');
$queries->create('settings', [
    'name' => 'sitename',
    'value' => $sitename,
]);
$cache->setCache('sitenamecache');
$cache->store('sitename', $sitename);
$queries->create('settings', [
    'name' => 'incoming_email',
    'value' => getEnvVar('NAMELESS_SITE_CONTACT_EMAIL'),
]);
$queries->create('settings', [
    'name' => 'outgoing_email',
    'value' => getEnvVar('NAMELESS_SITE_OUTGOING_EMAIL'),
]);

print('👮 Creating admin account...' . PHP_EOL);

$username = getEnvVar('NAMELESS_ADMIN_USERNAME', 'admin');
$password = getEnvVar('NAMELESS_ADMIN_PASSWORD', 'password');
$email = getEnvVar('NAMELESS_ADMIN_EMAIL');

$user = new User();
$user->create([
    'username' => $username,
    'nickname' => $username,
    'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]),
    'pass_method' => 'default',
    'joined' => date('U'),
    'email' => $email,
    'lastip' => '127.0.0.1',
    'active' => 1,
    'last_online' => date('U'),
    'theme_id' => 1,
    'language_id' => $queries->getWhere('languages', ['is_default', '=', 1])[0]->id,
]);
DB::getInstance()->createQuery('INSERT INTO `nl2_users_groups` (`user_id`, `group_id`, `received`, `expire`) VALUES (?, ?, ?, ?)', [
    1,
    2,
    date('U'),
    0,
]);

$profile = ProfileUtils::getProfile($username);
if ($profile !== null) {
    $result = $profile->getProfileAsArray();
    if (isset($result['uuid']) && !empty($result['uuid'])) {
        $uuid = $result['uuid'];

        $queries->create('users_integrations', [
            'integration_id' => 1,
            'user_id' => 1,
            'identifier' => $uuid,
            'username' => $username,
            'verified' => 1,
            'date' => date('U'),
        ]);
    }
}

print(PHP_EOL . '✅ Installation complete! (Took ' . round(microtime(true) - $start, 2) . ' seconds)' . PHP_EOL);
print(PHP_EOL . '🖥  URL: http://' . $conf['core']['hostname'] . $conf['core']['path']);
print(PHP_EOL . '🔑 Admin username: ' . $username);
print(PHP_EOL . '🔑 Admin email: ' . $email);
print(PHP_EOL . '🔑 Admin password: ' . $password);
print(PHP_EOL);
exit(0);
