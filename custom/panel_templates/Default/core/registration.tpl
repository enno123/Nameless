{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$REGISTRATION}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$CONFIGURATION}</li>
                        <li class="breadcrumb-item active">{$REGISTRATION}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form id="enableRegistration" action="" method="post">
                            <div class="form-group custom-control custom-switch">
                                <input type="hidden" name="enable_registration" value="0">
                                <input name="enable_registration"
                                       id="InputEnableRegistration"
                                       type="checkbox"
                                       class="custom-control-input js-check-change"
                                       {if $REGISTRATION_ENABLED eq 1} checked{/if}
                                       value="1">
                                <label class="custom-control-label" for="InputEnableRegistration">
                                    {$ENABLE_REGISTRATION}
                                </label>
                            </div>
                            <input type="hidden" name="token" value="{$TOKEN}">
                        </form>

                        <form action="" method="post">
                            <div class="form-group custom-control custom-switch">
                                <input name="verification"
                                       id="verification"
                                       type="checkbox"
                                       class="custom-control-input"
                                       {if $EMAIL_VERIFICATION_VALUE eq 1} checked{/if}>
                                <label class="custom-control-label" for="verification">
                                    {$EMAIL_VERIFICATION}
                                </label>
                            </div>
                            <div class="form-group custom-control custom-switch">
                                <input id="InputEnableRecaptcha"
                                       name="enable_recaptcha"
                                       type="checkbox"
                                       class="custom-control-input"
                                       value="1"
                                       {if $CAPTCHA_GENERAL_VALUE eq 'true' } checked{/if}>
                                <label class="custom-control-label" for="InputEnableRecaptcha">
                                    {$CAPTCHA_GENERAL}
                                </label>
                            </div>
                            <div class="form-group custom-control custom-switch">
                                <input id="InputEnableRecaptchaLogin"
                                       name="enable_recaptcha_login"
                                       type="checkbox"
                                       class="custom-control-input"
                                       value="1" {if $CAPTCHA_LOGIN_VALUE eq 'true' } checked{/if} />
                                <label class="custom-control-label" for="InputEnableRecaptchaLogin">
                                    {$CAPTCHA_LOGIN}
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="InputCaptchaType">{$CAPTCHA_TYPE}</label>
                                <select name="captcha_type" id="InputCaptchaType" class="form-control">
                                    {foreach from=$CAPTCHA_OPTIONS item=option}
                                        <option value="{$option.value}" {if $option.active} selected{/if}>
                                            {$option.value}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="InputRecaptcha">{$CAPTCHA_SITE_KEY}</label>
                                <input type="text" name="recaptcha" class="form-control" id="InputRecaptcha"
                                       placeholder="{$CAPTCHA_SITE_KEY}" value="{$CAPTCHA_SITE_KEY_VALUE}">
                            </div>
                            <div class="form-group">
                                <label for="InputRecaptchaSecret">{$CAPTCHA_SECRET_KEY}</label>
                                <input type="text" name="recaptcha_secret" class="form-control"
                                       id="InputRecaptchaSecret" placeholder="{$CAPTCHA_SECRET_KEY}"
                                       value="{$CAPTCHA_SECRET_KEY_VALUE}">
                            </div>
                            <div class="form-group">
                                <label for="InputRegistrationDisabledMessage">{$REGISTRATION_DISABLED_MESSAGE}</label>
                                <textarea style="width:100%" rows="10" name="message"
                                          id="InputRegistrationDisabledMessage">{$REGISTRATION_DISABLED_MESSAGE_VALUE}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="InputValidationPromoteGroup">{$VALIDATE_PROMOTE_GROUP}</label> <span
                                        class="badge badge-info" data-toggle="popover" data-title="{$INFO}"
                                        data-content="{$VALIDATE_PROMOTE_GROUP_INFO}"><i
                                            class="fa fa-question"></i></span>
                                <select class="form-control" id="InputValidationPromoteGroup" name="promote_group">
                                    {foreach from=$GROUPS item=group}
                                        <option value="{$group->id}" {if $group->id eq $VALIDATION_GROUP} selected{/if}>{$group->name|escape}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>

                    </div>
                </div>

                <h5>{$OAUTH}</h5>
                <div class="card shadow border-left-primary">
                    <div class="card-body">
                        <h5><i class="icon fa fa-info-circle"></i> {$INFO}</h5>
                        {$OAUTH_INFO}
                    </div>
                </div>
                <br />
                <form action="" method="post">
                    <div class="row">
                        <div class="col">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="form-group custom-control custom-switch text-center">
                                        <input id="enable-discord" name="enable-discord" type="checkbox" class="custom-control-input" value="1" {if $DISCORD_OAUTH_ENABLED && $DISCORD_OAUTH_SETUP} checked{/if} />
                                        <label for="enable-discord" id="enable-discord" class="custom-control-label">
                                            Discord <i class="fab fa-discord fa-1x align-middle"></i>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="client-id-discord">Client ID</label>
                                        <input type="text" name="client-id-discord" class="form-control" id="client-id-discord" placeholder="{$CLIENT_ID}" value="{$DISCORD_CLIENT_ID}">
                                    </div>

                                    <div class="form-group">
                                        <label for="client-secret-discord">Client Secret</label>
                                        <input type="password" name="client-secret-discord" class="form-control" id="client-secret-discord" placeholder="{$CLIENT_SECRET}" value="{$DISCORD_CLIENT_SECRET}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="form-group custom-control custom-switch text-center">
                                        <input id="enable-google" name="enable-google" type="checkbox" class="custom-control-input" {if $GOOGLE_OAUTH_ENABLED && $GOOGLE_OAUTH_SETUP} checked{/if} />
                                        <label for="enable-google" id="enable-google" class="custom-control-label">
                                            Google <i class="fab fa-google fa-1x align-middle"></i>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="client-id-google">Client ID</label>
                                        <input type="text" name="client-id-google" class="form-control" id="client-id-google" placeholder="{$CLIENT_ID}" value="{$GOOGLE_CLIENT_ID}">
                                    </div>

                                    <div class="form-group">
                                        <label for="client-secret-google">Client Secret</label>
                                        <input type="password" name="client-secret-google" class="form-control" id="client-secret-google" placeholder="{$CLIENT_SECRET}" value="{$GOOGLE_CLIENT_SECRET}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="action" value="oauth">
                        <input type="hidden" name="token" value="{$TOKEN}">
                        <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                    </div>
                </form>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

</body>

</html>
