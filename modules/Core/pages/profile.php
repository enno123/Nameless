<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  User profile page
 */

// Always define page name
const PAGE = 'profile';

$timeago = new TimeAgo(TIMEZONE);

$profile = explode('/', rtrim($_GET['route'], '/'));
if (count($profile) >= 3 && ($profile[count($profile) - 1] != 'profile' || $profile[count($profile) - 2] == 'profile') && !isset($_GET['error'])) {
    // User specified
    $md_profile = $profile[count($profile) - 1];

    $page_metadata = $queries->getWhere('page_descriptions', ['page', '=', '/profile']);
    if (count($page_metadata)) {
        define('PAGE_DESCRIPTION', str_replace(['{site}', '{profile}'], [SITE_NAME, Output::getClean($md_profile)], $page_metadata[0]->description));
        define('PAGE_KEYWORDS', $page_metadata[0]->tags);
    }

    $page_title = $language->get('user', 'profile') . ' - ' . Output::getClean($md_profile);
} else {
    $page_title = $language->get('user', 'profile');
}

require_once(ROOT_PATH . '/core/templates/frontend_init.php');

$template->addCSSFiles(
    [
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/image-picker/image-picker.css' => [],
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/prism/prism.css' => [],
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/tinymce/plugins/spoiler/css/spoiler.css' => []
    ]
);

$template->addJSFiles(
    [
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/prism/prism.js' => [],
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/tinymce/plugins/spoiler/js/spoiler.js' => []
    ]
);

$template->addCSSStyle(
    '.thumbnails li img{
      width: 200px;
    }'
);

if (count($profile) >= 3 && ($profile[count($profile) - 1] != 'profile' || $profile[count($profile) - 2] == 'profile') && !isset($_GET['error'])) {
    // User specified
    $profile = $profile[count($profile) - 1];

    $profile_user = new User($profile, 'username');
    if (!$profile_user->exists()) {
        Redirect::to(URL::build('/profile/&error=not_exist'));
    }
    $query = $profile_user->data();

    // Deal with input
    if (Input::exists() && $user->isLoggedIn()) {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'banner':
                    if ($user->data()->username == $profile) {
                        if (Token::check()) {
                            // Update banner
                            if (isset($_POST['banner'])) {
                                // Check image specified actually exists
                                if (is_file(implode(DIRECTORY_SEPARATOR, [ROOT_PATH, 'uploads', 'profile_images', $_POST['banner']]))) {
                                    // Exists
                                    // Is it an image file?
                                    if (in_array(pathinfo(implode(DIRECTORY_SEPARATOR, [ROOT_PATH, 'uploads', 'profile_images', $_POST['banner']]), PATHINFO_EXTENSION), ['gif', 'png', 'jpg', 'jpeg'])) {
                                        // Yes, update settings
                                        $user->update(
                                            [
                                                'banner' => Output::getClean($_POST['banner'])
                                            ]
                                        );

                                        // Requery to update banner
                                        $user = new User();
                                        $profile_user = new User($profile, 'username');
                                        $query = $profile_user->data();
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'new_post':
                    if (Token::check()) {
                        $validation = Validate::check($_POST, [
                            'post' => [
                                Validate::REQUIRED => true,
                                Validate::MIN => 1,
                                Validate::MAX => 10000
                            ]
                        ])->message($language->get('user', 'invalid_wall_post'));

                        if ($validation->passed()) {
                            // Validation successful
                            // Input into database
                            $queries->create(
                                'user_profile_wall_posts',
                                [
                                    'user_id' => $query->id,
                                    'author_id' => $user->data()->id,
                                    'time' => date('U'),
                                    'content' => Output::getClean(Input::get('post'))
                                ]
                            );

                            if ($query->id !== $user->data()->id) {
                                // Alert user
                                Alert::create(
                                    $query->id,
                                    'profile_post',
                                    [
                                        'path' => 'core',
                                        'file' => 'user',
                                        'term' => 'new_wall_post',
                                        'replace' => '{x}',
                                        'replace_with' => $user->getDisplayname()
                                    ],
                                    [
                                        'path' => 'core',
                                        'file' => 'user',
                                        'term' => 'new_wall_post',
                                        'replace' => '{x}',
                                        'replace_with' => $user->getDisplayname()
                                    ],
                                    URL::build('/profile/' . urlencode($profile_user->getDisplayname(true)) . '/#post-' . urlencode($queries->getLastId()))
                                );
                            }

                            $cache->setCache('profile_posts_widget');
                            $cache->eraseAll();

                            // Redirect to clear input
                            Redirect::to($profile_user->getProfileURL());
                        }

                        // Validation failed
                        $error = $validation->errors();
                    } else {
                        $error = $language->get('general', 'invalid_token');
                    }
                    break;

                case 'reply':
                    if (Token::check()) {
                        $validation = Validate::check($_POST, [
                            'reply' => [
                                Validate::REQUIRED => true,
                                Validate::MIN => 1,
                                Validate::MAX => 10000
                            ],
                            'post' => [
                                Validate::REQUIRED => true
                            ]
                        ])->message($language->get('user', 'invalid_wall_post'));

                        if ($validation->passed()) {
                            // Validation successful

                            // Ensure post exists
                            $post = $queries->getWhere('user_profile_wall_posts', ['id', '=', $_POST['post']]);
                            if (!count($post)) {
                                Redirect::to($profile_user->getProfileURL());
                            }

                            // Input into database
                            $queries->create(
                                'user_profile_wall_posts_replies',
                                [
                                    'post_id' => $_POST['post'],
                                    'author_id' => $user->data()->id,
                                    'time' => date('U'),
                                    'content' => Input::get('reply')
                                ]
                            );

                            if ($post[0]->author_id != $query->id && $query->id != $user->data()->id) {
                                Alert::create(
                                    $query->id,
                                    'profile_post',
                                    [
                                        'path' => 'core',
                                        'file' => 'user',
                                        'term' => 'new_wall_post',
                                        'replace' => '{x}',
                                        'replace_with' => $user->getDisplayname(),
                                    ],
                                    [
                                        'path' => 'core',
                                        'file' => 'user',
                                        'term' => 'new_wall_post',
                                        'replace' => '{x}',
                                        'replace_with' => $user->getDisplayname(),
                                    ],
                                    URL::build('/profile/' . urlencode($profile_user->getDisplayname(true)) . '/#post-' . urlencode($_POST['post']))
                                );
                            } else {
                                if ($post[0]->author_id != $user->data()->id) {
                                    // Alert post author
                                    if ($post[0]->author_id == $query->id) {
                                        Alert::create(
                                            $query->id,
                                            'profile_post_reply',
                                            [
                                                'path' => 'core',
                                                'file' => 'user',
                                                'term' => 'new_wall_post_reply_your_profile',
                                                'replace' => '{x}',
                                                'replace_with' => $user->getDisplayname(),
                                            ],
                                            [
                                                'path' => 'core',
                                                'file' => 'user',
                                                'term' => 'new_wall_post_reply_your_profile',
                                                'replace' => '{x}',
                                                'replace_with' => $user->getDisplayname()
                                            ],
                                            URL::build('/profile/' . urlencode($profile_user->getDisplayname(true)) . '/#post-' . urlencode($_POST['post']))
                                        );
                                    } else {
                                        Alert::create(
                                            $post[0]->author_id,
                                            'profile_post_reply',
                                            [
                                                'path' => 'core',
                                                'file' => 'user', '
                                                term' => 'new_wall_post_reply',
                                                'replace' => ['{x}', '{y}'],
                                                'replace_with' => [$user->getDisplayname(), $profile_user->getDisplayname()]
                                            ],
                                            [
                                                'path' => 'core',
                                                'file' => 'user',
                                                'term' => 'new_wall_post_reply',
                                                'replace' => ['{x}', '{y}'],
                                                'replace_with' => [$user->getDisplayname(), $profile_user->getDisplayname()]
                                            ],
                                            URL::build('/profile/' . urlencode($profile_user->getDisplayname(true)) . '/#post-' . urlencode($_POST['post']))
                                        );
                                    }
                                }
                            }

                            // Redirect to clear input
                            Redirect::to($profile_user->getProfileURL());
                        }

                        // Validation failed
                        $error = $validation->errors();
                    } else {
                        $error = $language->get('general', 'invalid_token');
                    }
                    break;

                case 'block':
                    if (Token::check()) {
                        if ($user->isBlocked($user->data()->id, $query->id)) {
                            // Unblock
                            $blocked_id = $queries->getWhere('blocked_users', ['user_id', '=', $user->data()->id]);
                            if (count($blocked_id)) {
                                foreach ($blocked_id as $id) {
                                    if ($id->user_blocked_id == $query->id) {
                                        $blocked_id = $id->id;
                                        break;
                                    }
                                }

                                if (is_numeric($blocked_id)) {
                                    $queries->delete('blocked_users', ['id', '=', $blocked_id]);
                                    $success = $language->get('user', 'user_unblocked');
                                }
                            }
                        } else {
                            // Block
                            $queries->create('blocked_users', [
                                'user_id' => $user->data()->id,
                                'user_blocked_id' => $query->id
                            ]);
                            $success = $language->get('user', 'user_blocked');
                        }
                    } else {
                        $error = $language->get('general', 'invalid_token');
                    }
                    break;

                case 'edit':
                    // Ensure user is mod or owner of post
                    if (Token::check()) {
                        if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
                            $post = $queries->getWhere('user_profile_wall_posts', ['id', '=', $_POST['post_id']]);
                            if (count($post)) {
                                $post = $post[0];
                                if ($user->canViewStaffCP() || $post->author_id == $user->data()->id) {
                                    if (isset($_POST['content']) && strlen($_POST['content']) < 10000 && strlen($_POST['content']) >= 1) {
                                        try {
                                            $queries->update('user_profile_wall_posts', $_POST['post_id'], [
                                                'content' => Output::getClean($_POST['content'])
                                            ]);
                                        } catch (Exception $e) {
                                            $error = $e->getMessage();
                                        }
                                    } else {
                                        $error = $language->get('user', 'invalid_wall_post');
                                    }
                                }
                            }
                        }
                    } else {
                        $error = $language->get('general', 'invalid_token');
                    }
                    break;

                case 'delete':
                    // Ensure user is mod or owner of post
                    if (Token::check()) {
                        if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
                            $post = $queries->getWhere('user_profile_wall_posts', ['id', '=', $_POST['post_id']]);
                            if (count($post)) {
                                $post = $post[0];
                                if ($user->canViewStaffCP() || $post->author_id == $user->data()->id) {
                                    try {
                                        $queries->delete('user_profile_wall_posts', ['id', '=', $_POST['post_id']]);
                                        $queries->delete('user_profile_wall_posts_replies', ['post_id', '=', $_POST['post_id']]);
                                    } catch (Exception $e) {
                                        $error = $e->getMessage();
                                    }
                                }
                            }
                        }
                    } else {
                        $error = $language->get('general', 'invalid_token');
                    }
                    break;

                case 'deleteReply':
                    // Ensure user is mod or owner of reply
                    if (Token::check()) {
                        if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
                            $post = $queries->getWhere('user_profile_wall_posts_replies', ['id', '=', $_POST['post_id']]);
                            if (count($post)) {
                                $post = $post[0];
                                if ($user->canViewStaffCP() || $post->author_id == $user->data()->id) {
                                    try {
                                        $queries->delete('user_profile_wall_posts_replies', ['id', '=', $_POST['post_id']]);
                                    } catch (Exception $e) {
                                        $error = $e->getMessage();
                                    }
                                }
                            }
                        }
                    } else {
                        $error = $language->get('general', 'invalid_token');
                    }
                    break;
            }
        }
    }

    if (isset($_GET['action']) && $user->isLoggedIn()) {
        switch ($_GET['action']) {
            case 'react':
                if (!isset($_GET['post']) || !is_numeric($_GET['post'])) {
                    // Post ID required
                    Redirect::to($profile_user->getProfileURL());
                }

                // Does the post exist?
                $post = $queries->getWhere('user_profile_wall_posts', ['id', '=', $_GET['post']]);
                if (!count($post)) {
                    Redirect::to($profile_user->getProfileURL());
                }

                // Can't like our own post
                if ($post[0]->author_id == $user->data()->id) {
                    Redirect::to($profile_user->getProfileURL());
                }

                // Liking or unliking?
                $post_likes = $queries->getWhere('user_profile_wall_posts_reactions', ['post_id', '=', $_GET['post']]);
                if (count($post_likes)) {
                    foreach ($post_likes as $like) {
                        if ($like->user_id == $user->data()->id) {
                            $has_liked = $like->id;
                            break;
                        }
                    }
                }

                if (isset($has_liked)) {
                    // Unlike
                    $queries->delete('user_profile_wall_posts_reactions', ['id', '=', $has_liked]);
                } else {
                    // Like
                    $queries->create('user_profile_wall_posts_reactions', [
                        'user_id' => $user->data()->id,
                        'post_id' => $_GET['post'],
                        'reaction_id' => 1,
                        'time' => date('U')
                    ]);
                }

                // Redirect
                Redirect::to($profile_user->getProfileURL());

            case 'reset_banner':
                if (Token::check($_POST['token'])) {
                    if ($user->hasPermission('modcp.profile_banner_reset')) {
                        $queries->update('users', $query->id, [
                            'banner' => null
                        ]);
                    }

                    Redirect::to($profile_user->getProfileURL());
                }

                $error = $language->get('general', 'invalid_token');

                break;
        }
    }

    // Get page
    if (isset($_GET['p'])) {
        if (!is_numeric($_GET['p'])) {
            Redirect::to($profile_user->getProfileURL());
        }

        if ($_GET['p'] == 1) {
            // Avoid bug in pagination class
            Redirect::to($profile_user->getProfileURL());
        }
        $p = $_GET['p'];
    } else {
        $p = 1;
    }

    // View count
    // Check if user is logged in and the viewer is not the owner of this profile.
    if (($user->isLoggedIn() && $user->data()->id != $query->id)
        // If no one is logged in check if they have accepted the cookies.
        || (!$user->isLoggedIn() && (defined('COOKIE_CHECK') && COOKIES_ALLOWED))
    ) {
        if (!Cookie::exists('nl-profile-' . $query->id)) {
            $queries->increment('users', $query->id, 'profile_views');
            Cookie::put('nl-profile-' . $query->id, 'true', 3600);
        }
    } else {
        if (!Session::exists('nl-profile-' . $query->id)) {
            $queries->increment('users', $query->id, 'profile_views');
            Session::put('nl-profile-' . $query->id, 'true');
        }
    }

    // Set Can view
    if ($profile_user->isPrivateProfile() && $user->canPrivateProfile()) {
        $smarty->assign([
            'PRIVATE_PROFILE' => $language->get('user', 'private_profile_page'),
            'CAN_VIEW' => false
        ]);
    } else {
        $smarty->assign([
            'CAN_VIEW' => true
        ]);
    }

    // Generate Smarty variables to pass to template
    if ($user->isLoggedIn()) {
        // Form token
        $smarty->assign([
            'TOKEN' => Token::get(),
            'LOGGED_IN' => true,
            'SUBMIT' => $language->get('general', 'submit'),
            'CANCEL' => $language->get('general', 'cancel'),
            'CAN_MODERATE' => $user->canViewStaffCP()
        ]);

        if ($user->hasPermission('profile.private.bypass')) {
            $smarty->assign([
                'CAN_VIEW' => true
            ]);
        }

        if ($user->data()->id == $query->id) {
            // Custom profile banners
            $banners = [];

            $image_path = implode(DIRECTORY_SEPARATOR, [ROOT_PATH, 'uploads', 'profile_images']);
            $images = scandir($image_path);

            // Only display jpeg, png, jpg, gif
            $allowed_exts = ['gif', 'png', 'jpg', 'jpeg'];

            foreach ($images as $image) {
                $ext = pathinfo($image, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed_exts)) {
                    continue;
                }

                $banners[] = [
                    'src' => ((defined('CONFIG_PATH')) ? CONFIG_PATH . '/' : '/') . 'uploads/profile_images/' . Output::getClean($image),
                    'name' => Output::getClean($image),
                    'active' => $user->data()->banner == $image
                ];
            }

            $image_path = implode(DIRECTORY_SEPARATOR, [ROOT_PATH, 'uploads', 'profile_images', $user->data()->id]);

            if (is_dir($image_path)) {
                $images = scandir($image_path);

                foreach ($images as $image) {
                    $ext = pathinfo($image, PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed_exts)) {
                        continue;
                    }

                    $banners[] = [
                        'src' => ((defined('CONFIG_PATH')) ? CONFIG_PATH . '/' : '/') . 'uploads/profile_images/' . Output::getClean($user->data()->id) . '/' . Output::getClean($image),
                        'name' => Output::getClean($user->data()->id) . '/' . Output::getClean($image),
                        'active' => $user->data()->banner == $image
                    ];
                }
            }

            $smarty->assign([
                'SELF' => true,
                'SETTINGS_LINK' => URL::build('/user/settings'),
                'CHANGE_BANNER' => $language->get('user', 'change_banner'),
                'BANNERS' => $banners,
                'CAN_VIEW' => true,
            ]);

            if ($user->hasPermission('usercp.profile_banner')) {
                $smarty->assign([
                    'UPLOAD_PROFILE_BANNER' => $language->get('user', 'upload_profile_banner'),
                    'PROFILE_BANNER' => $language->get('user', 'profile_banner'),
                    'BROWSE' => $language->get('general', 'browse'),
                    'UPLOAD' => $language->get('user', 'upload'),
                    'UPLOAD_BANNER_URL' => ((defined('CONFIG_PATH')) ? CONFIG_PATH . '/' : '/') . 'core/includes/image_upload.php'
                ]);
            }
        } else {
            $smarty->assign([
                'MESSAGE_LINK' => URL::build('/user/messaging/', 'action=new&amp;uid=' . urlencode($query->id)),
                'FOLLOW_LINK' => URL::build('/user/follow/', 'user=' . urlencode($query->id)),
                'CONFIRM' => $language->get('general', 'confirm'),
                'MOD_OR_ADMIN' => $profile_user->canViewStaffCP()
            ]);

            // Is the user blocked?
            if ($user->isBlocked($user->data()->id, $query->id)) {
                $smarty->assign([
                    'UNBLOCK_USER' => $language->get('user', 'unblock_user'),
                    'CONFIRM_UNBLOCK_USER' => $language->get('user', 'confirm_unblock_user')
                ]);
            } else {
                $smarty->assign([
                    'BLOCK_USER' => $language->get('user', 'block_user'),
                    'CONFIRM_BLOCK_USER' => $language->get('user', 'confirm_block_user')
                ]);
            }

            if ($user->hasPermission('modcp.profile_banner_reset')) {
                $smarty->assign([
                    'RESET_PROFILE_BANNER' => $language->get('moderator', 'reset_profile_banner'),
                    'RESET_PROFILE_BANNER_LINK' => URL::build('/profile/' . urlencode($query->username) . '/', 'action=reset_banner')
                ]);
            }
        }
    }

    $smarty->assign([
        'NICKNAME' => $profile_user->getDisplayname(true),
        'USERNAME' => $profile_user->getDisplayname(),
        'GROUPS' => (isset($query) ? $profile_user->getAllGroupHtml() : [Output::getPurified($group)]),
        'USERNAME_COLOUR' => $profile_user->getGroupStyle(),
        'USER_TITLE' => Output::getClean($query->user_title),
        'FOLLOW' => $language->get('user', 'follow'),
        'AVATAR' => $profile_user->getAvatar(500),
        'BANNER' => ((defined('CONFIG_PATH')) ? CONFIG_PATH . '/' : '/') . 'uploads/profile_images/' . (($query->banner) ? Output::getClean($query->banner) : 'profile.jpg'),
        'POST_ON_WALL' => str_replace('{x}', Output::getClean($profile_user->getDisplayname()), $language->get('user', 'post_on_wall')),
        'FEED' => $language->get('user', 'feed'),
        'ABOUT' => $language->get('user', 'about'),
        'REACTIONS_TITLE' => $language->get('user', 'likes'),
        //'REACTIONS' => $reactions,
        'CLOSE' => $language->get('general', 'close'),
        'REPLIES_TITLE' => $language->get('user', 'replies'),
        'NO_REPLIES' => $language->get('user', 'no_replies_yet'),
        'NEW_REPLY' => $language->get('user', 'new_reply'),
        'DELETE' => $language->get('general', 'delete'),
        'CONFIRM_DELETE' => $language->get('general', 'confirm_deletion'),
        'EDIT' => $language->get('general', 'edit'),
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'ERROR_TITLE' => $language->get('general', 'error'),
        'REPLY' => $language->get('user', 'reply'),
        'EDIT_POST' => $language->get('general', 'edit'),
        'VIEWER_ID' => $user->isLoggedIn() ? $user->data()->id : 0,
    ]);

    // Wall posts
    $wall_posts = [];
    $wall_posts_query = $queries->orderWhere('user_profile_wall_posts', 'user_id = ' . $query->id, 'time', 'DESC');

    if (count($wall_posts_query)) {
        // Pagination
        $paginator = new Paginator(
            $template_pagination ?? null,
            $template_pagination_left ?? null,
            $template_pagination_right ?? null
        );
        $results = $paginator->getLimited($wall_posts_query, 10, $p, count($wall_posts_query));
        $pagination = $paginator->generate(7, URL::build('/profile/' . urlencode($query->username) . '/'));

        $smarty->assign('PAGINATION', $pagination);

        // Display the correct number of posts
        foreach ($results->data as $nValue) {
            $post_user = $queries->getWhere('users', ['id', '=', $nValue->author_id]);

            if (!count($post_user)) {
                continue;
            }

            // Get reactions/replies
            $reactions = [];
            $replies = [];

            $reactions_query = $queries->getWhere('user_profile_wall_posts_reactions', ['post_id', '=', $nValue->id]);
            if (count($reactions_query)) {
                if (count($reactions_query) == 1) {
                    $reactions['count'] = $language->get('user', '1_like');
                } else {
                    $reactions['count'] = str_replace('{x}', count($reactions_query), $language->get('user', 'x_likes'));
                }

                foreach ($reactions_query as $reaction) {
                    // Get reaction name and icon
                    // TODO
                    /*
                    $reaction_name = $queries->getWhere('reactions', array('id', '=', $reaction->reaction_id));

                    if (!count($reaction_name) || $reaction_name[0]->enabled == 0) continue;
                    $reaction_html = $reaction_name[0]->html;
                    $reaction_name = Output::getClean($reaction_name[0]->name);
                    */

                    $target_user = new User($reaction->user_id);
                    $reactions['reactions'][] = [
                        'user_id' => Output::getClean($reaction->user_id),
                        'username' => $target_user->getDisplayname(true),
                        'nickname' => $target_user->getDisplayname(),
                        'style' => $target_user->getGroupStyle(),
                        'profile' => $target_user->getProfileURL(),
                        'avatar' => $target_user->getAvatar(500),
                        //'reaction_name' => $reaction_name,
                        //'reaction_html' => $reaction_html
                    ];
                }
            } else {
                $reactions['count'] = str_replace('{x}', 0, $language->get('user', 'x_likes'));
            }
            $reactions_query = null;

            $replies_query = $queries->orderWhere('user_profile_wall_posts_replies', 'post_id = ' . $nValue->id, 'time', 'ASC');
            if (count($replies_query)) {
                if (count($replies_query) == 1) {
                    $replies['count'] = $language->get('user', '1_reply');
                } else {
                    $replies['count'] = str_replace('{x}', count($replies_query), $language->get('user', 'x_replies'));
                }

                foreach ($replies_query as $reply) {
                    $target_user = new User($reply->author_id);
                    $replies['replies'][] = [
                        'user_id' => Output::getClean($reply->author_id),
                        'username' => $target_user->getDisplayname(true),
                        'nickname' => $target_user->getDisplayname(),
                        'style' => $target_user->getGroupStyle(),
                        'profile' => $target_user->getProfileURL(),
                        'avatar' => $target_user->getAvatar(500),
                        'time_friendly' => $timeago->inWords(date('Y-m-d H:i:s', $reply->time), $language->getTimeLanguage()),
                        'time_full' => date(DATE_FORMAT, $reply->time),
                        'content' => Output::getPurified($reply->content),
                        'self' => (($user->isLoggedIn() && $user->data()->id == $reply->author_id) ? 1 : 0),
                        'id' => $reply->id
                    ];
                }
            } else {
                $replies['count'] = str_replace('{x}', 0, $language->get('user', 'x_replies'));
            }
            $replies_query = null;

            $target_user = new User($post_user[0]->id);
            $wall_posts[] = [
                'id' => $nValue->id,
                'user_id' => Output::getClean($post_user[0]->id),
                'username' => $target_user->getDisplayname(true),
                'nickname' => $target_user->getDisplayname(),
                'profile' => $target_user->getProfileURL(),
                'user_style' => $target_user->getGroupStyle(),
                'avatar' => $target_user->getAvatar(500),
                'content' => Output::getPurified($nValue->content),
                'date_rough' => $timeago->inWords(date('Y-m-d H:i:s', $nValue->time), $language->getTimeLanguage()),
                'date' => date(DATE_FORMAT, $nValue->time),
                'reactions' => $reactions,
                'replies' => $replies,
                'self' => $user->isLoggedIn() && $user->data()->id == $nValue->author_id,
                'reactions_link' => ($user->isLoggedIn() && ($post_user[0]->id != $user->data()->id) ? URL::build('/profile/' . urlencode($query->username) . '/', 'action=react&amp;post=' . urlencode($nValue->id)) : '#')
            ];
        }
    } else {
        $smarty->assign('NO_WALL_POSTS', $language->get('user', 'no_wall_posts'));
    }

    $smarty->assign('WALL_POSTS', $wall_posts);

    if (isset($error)) {
        $smarty->assign('ERROR', $error);
    }

    if (isset($success)) {
        $smarty->assign('SUCCESS', $success);
    }

    // About tab
    $fields = [];

    // Get profile fields
    foreach ($profile_user->getProfileFields() as $id => $profile_field) {
        if (!$profile_field->value) {
            continue;
        }

        // Get field type
        switch ($profile_field->type) {
            case Fields::TEXT:
                $type = 'text';
                break;
            case Fields::TEXTAREA:
                $type = 'textarea';
                break;
            case Fields::DATE:
                $type = 'date';
                break;
        }

        $fields[] = [
            'title' => Output::getClean($profile_field->name),
            'type' => $type,
            'value' => Output::getClean($profile_field->value)
        ];
    }

    // User Integrations
    $user_integrations = [];
    foreach ($profile_user->getIntegrations() as $key => $integrationUser) {
        if ($integrationUser->data()->username != null && $integrationUser->data()->show_publicly) {
            $fields[] = [
                'title' => Output::getClean($key),
                'type' => 'text',
                'value' => Output::getClean($integrationUser->data()->username)
            ];

            $user_integrations[$key] = [
                'username' => Output::getClean($integrationUser->data()->username),
                'identifier' => Output::getClean($integrationUser->data()->identifier)
            ];
        }
    }
    $smarty->assign('INTEGRATIONS', $user_integrations);

    if (!count($fields)) {
        $smarty->assign('NO_ABOUT_FIELDS', $language->get('user', 'no_about_fields'));
    }

    $profile_placeholders = $profile_user->getProfilePlaceholders();
    foreach ($profile_placeholders as $profile_placeholder) {
        $fields[] = [
            'title' => $profile_placeholder->friendly_name,
            'type' => 'text',
            'value' => $profile_placeholder->value,
            'tooltip' => $language->get('admin', 'placeholders_last_updated') . ': ' . $timeago->inWords(date('Y-m-d H:i:s', $profile_placeholder->last_updated), $language->getTimeLanguage()),
        ];
    }

    // Add date registered and last seen
    $fields['registered'] = [
        'title' => $language->get('user', 'registered'),
        'type' => 'text',
        'value' => $timeago->inWords(date('Y-m-d H:i:s', $query->joined), $language->getTimeLanguage()),
        'tooltip' => date(DATE_FORMAT, $query->joined)
    ];
    $fields['last_seen'] = [
        'title' => $language->get('user', 'last_seen'),
        'type' => 'text',
        'value' => $timeago->inWords(date('Y-m-d H:i:s', $query->last_online), $language->getTimeLanguage()),
        'tooltip' => date(DATE_FORMAT, $query->last_online)
    ];

    // Add Profile views
    $fields['profile_views'] = [
        'title' => $language->get('user', 'views'),
        'type' => 'text',
        'value' => $query->profile_views
    ];

    $smarty->assign('ABOUT_FIELDS', $fields);

    // Custom tabs
    $tabs = [];
    if (isset($profile_tabs) && count($profile_tabs)) {
        foreach ($profile_tabs as $key => $tab) {
            $tabs[$key] = ['title' => $tab['title'], 'include' => $tab['smarty_template']];
            if (is_file($tab['require'])) {
                require($tab['require']);
            }
        }
    }

    // Assign profile tabs
    $smarty->assign('TABS', $tabs);

    if (isset($directories[1]) &&
        !empty($directories[1]) &&
        !isset($_GET['error']) &&
        $user->isLoggedIn() &&
        $user->data()->username == $profile) {
        // Script for banner selector
        $template->addJSFiles(
            [
                (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/image-picker/image-picker.min.js' => []
            ]
        );
    }

    // Load modules + template
    Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

    $page_load = microtime(true) - $start;
    define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

    $template->onPageLoad();

    $smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
    $smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));

    require(ROOT_PATH . '/core/templates/navbar.php');
    require(ROOT_PATH . '/core/templates/footer.php');

    // Display template
    $template->displayTemplate('profile.tpl', $smarty);
} else {
    if (isset($_GET['error'])) {
        // User not exist
        $smarty->assign([
            'BACK' => $language->get('general', 'back'),
            'HOME' => $language->get('general', 'home'),
            'NOT_FOUND' => $language->get('user', 'couldnt_find_that_user')
        ]);
        // Load modules + template
        Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

        $page_load = microtime(true) - $start;
        define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

        $template->onPageLoad();

        $smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
        $smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));

        require(ROOT_PATH . '/core/templates/navbar.php');
        require(ROOT_PATH . '/core/templates/footer.php');

        // Display template
        $template->displayTemplate('user_not_exist.tpl', $smarty);

        // Search for user
        // TODO
    }
}
