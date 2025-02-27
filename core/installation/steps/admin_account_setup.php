<?php
if (isset($_SESSION['admin_setup']) && $_SESSION['admin_setup'] == true) {
    Redirect::to('?step=conversion');
}

if (!isset($_SESSION['site_initialized']) || $_SESSION['site_initialized'] != true) {
    Redirect::to('?step=site_configuration');
}

function display_error(string $message) {
    echo "<div class=\"ui error message\">$message</div>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $validation = Validate::check($_POST, [
        'username' => [
            Validate::REQUIRED => true,
            Validate::MIN => 3,
            Validate::MAX => 20,
        ],
        'email' => [
            Validate::REQUIRED => true,
            Validate::MIN => 4,
            Validate::MAX => 64,
            Validate::EMAIL => true,
        ],
        'password' => [
            Validate::REQUIRED => true,
            Validate::MIN => 6,
            Validate::MAX => 30,
        ],
        'password_again' => [
            Validate::REQUIRED => true,
            Validate::MATCHES => 'password',
        ],
    ]);

    if (!$validation->passed()) {
        foreach ($validation->errors() as $item) {
            if (strpos($item, 'is required') !== false) {
                display_error($language['input_required']);
            } else if (strpos($item, 'minimum') !== false) {
                display_error($language['input_minimum']);
            } else if (strpos($item, 'maximum') !== false) {
                display_error($language['input_maximum']);
            } else if (strpos($item, 'must match') !== false) {
                display_error($language['passwords_must_match']);
            } else if (strpos($item, 'not a valid email') !== false) {
                display_error($language['email_invalid']);
            }
        }

    } else {
        $user = new User();
        $password = password_hash(Input::get('password'), PASSWORD_BCRYPT, ['cost' => 13]);

        try {
            $queries = new Queries();

            $language = $queries->getWhere('languages', ['is_default', '=', 1]);

            $ip = $user->getIP();

            $user->create([
                'username' => Input::get('username'),
                'nickname' => Input::get('username'),
                'password' => $password,
                'pass_method' => 'default',
                'joined' => date('U'),
                'email' => Input::get('email'),
                'lastip' => $ip,
                'active' => 1,
                'last_online' => date('U'),
                'theme_id' => 1,
                'language_id' => $language[0]->id,
            ]);

            $profile = ProfileUtils::getProfile(Output::getClean(Input::get('username')));
            if ($profile !== null) {
                $result = $profile->getProfileAsArray();
                if (isset($result['uuid']) && !empty($result['uuid'])) {
                    $uuid = $result['uuid'];
                    
                    $queries->create('users_integrations', [
                        'integration_id' => 1,
                        'user_id' => 1,
                        'identifier' => $uuid,
                        'username' => Input::get('username'),
                        'verified' => 1,
                        'date' => date('U'),
                    ]);
                }
            }

            $login = $user->login(Input::get('email'), Input::get('password'), true);
            if ($login) {
                $_SESSION['admin_setup'] = true;
                $user->addGroup(2);

                Redirect::to('?step=conversion');
            }

            $queries->delete('users', ['id', '=', 1]);
            display_error($language['unable_to_login']);
        } catch (Exception $e) {
            display_error($language['unable_to_create_account'] . ': ' . $e->getMessage());
        }
    }
}
?>

<form action="" method="post" id="form-user">
    <div class="ui segments">
        <div class="ui secondary segment">
            <h4 class="ui header">
                <?php echo $language['creating_admin_account']; ?>
            </h4>
        </div>
        <div class="ui segment">
            <p><?php echo $language['enter_admin_details']; ?></p>
            <div class="ui centered grid">
                <div class="sixteen wide mobile twelve wide tablet ten wide computer column">
                    <div class="ui form">
                        <?php
                        create_field('text', $language['username'], 'username', 'inputUsername', getenv('NAMELESS_ADMIN_USERNAME') ?: '');
                        create_field('email', $language['email_address'], 'email', 'inputEmail', getenv('NAMELESS_ADMIN_EMAIL') ?: '');
                        create_field('password', $language['password'], 'password', 'inputPassword');
                        create_field('password', $language['confirm_password'], 'password_again', 'inputPasswordAgain');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="ui right aligned secondary segment">
            <button type="submit" class="ui small primary button">
                <?php echo $language['proceed']; ?>
            </button>
        </div>
    </div>
</form>
