$(function() {
    let loginForm1F = $('#login_form_1f');
    let loginForm2F = $('#login_form_2f');
    let registrationForm = $('#register_form');
    let restorePassRequireForm = $('#restore_req_form');
    let restorePassForm = $('#restore_pass_form');

    if (loginForm1F.length) {
        loginForm1F.on("submit", function(e) { authAction1F(e, loginForm1F.serialize()); });
    }

    if (loginForm2F.length) {
        loginForm2F.on("submit", function (e) { authAction2F(e, loginForm2F.serialize()); });
    }

    if (registrationForm.length) {
       registrationForm.on("submit", function(e) {
           registerAction(e, registrationForm.serialize());
       });
    }

    if (restorePassRequireForm.length) {
        restorePassRequireForm.on("submit", function(e) {
           restoreRequireAction(e, restorePassRequireForm.serialize());
        });
    }

    if (restorePassForm.length) {
        let url = new URL(window.location.href);
        restorePassForm.children('.login').val(url.searchParams.get('uid'));
        restorePassForm.children('.key').val(url.searchParams.get('key'));

        restorePassForm.on("submit", function(e) {
            restorePasswordAction(e, restorePassForm.serialize());
        });
    }
});

function authAction1F(e, formData, )
{
    e.preventDefault();

    $.ajax({
        url: "login_act.php",
        type: "post",
        data: "type=login1F&" + formData,
        mimeType: "multipart/form-data",
        cache: false,
        processData: false,
        success: function (data) {
            let text = "";
            switch (data) {
                case '2FRequired':
                    text = "<b>2F required</b>";
                    break;
                case 'userLoggedIn':
                    text = "<b>Success</b>";
                    break;
                case 'userNotFound':
                    text = "<b>User not found</b>";
                    break;
                case 'userNotVerified':
                    text = "<b>User not verified</b>";
                    break;
                case 'userWrongPassword':
                    text = "<b>User password incorrect</b>";
                    break;
                default:
                    text = "<b>Error</b>";
            }

            $("#result").html(text);
        }
    });
}

function authAction2F(e, formData)
{
    e.preventDefault();

    $.ajax({
        url: "login_act.php",
        type: "post",
        data: "type=login2F&" + formData,
        mimeType: "multipart/form-data",
        cache: false,
        processData: false,
        success: function (data)
        {
            let text = "";
            switch(data)
            {
                case 'userLoggedIn':
                    text = "<b>Done</b>";
                    break;
                case 'userWrongKey':
                    text = "<b>Wrong key</b>";
                    break;
                default:
                    text = "<b>Error</b>";
            }

            $("#result").html(text);
        }
    });
}

function registerAction(e, formData)
{
    e.preventDefault();

    $.ajax({
        url: "login_act.php",
        type: "post",
        data: "type=register&" + formData,
        mimeType: "multipart/form-data",
        cache: false,
        processData: false,
        success: function (data) {
            let text = "";
            switch (data) {
                case 'userRegisteredNo2F':
                    text = "<b></b>";
                    break;
                case 'userRegistered':
                    text = "<b>Success</b>";
                    break;
                case 'userRegistrationFailed':
                    text = "<b>User not found</b>";
                    break;
                default:
                    text = "<b>Error</b>";
            }

            $("#result").html(text);
        }
    });
}

function restoreRequireAction(e, formData)
{
    e.preventDefault();

    $.ajax({
        url: "passw.php",
        type: "post",
        data: "type=restorePassRequire&" + formData,
        mimeType: "multipart/form-data",
        cache: false,
        processData: false,
        success: function(data)
        {
            let text = "";
            switch(data)
            {
                case 'restoreKeySent':
                    text = "<b>Success</b>";
                    break;
                case 'userNotFound':
                    text = "<b>User not found</b>";
                    break;
                case 'emailSendingFailed':
                    text = "<b>Email not sent, something wrong</b>";
                    break;
                case 'emailDisabled':
                    text = "<b>User password incorrect</b>";
                    break;
                default:
                    text = "<b>Error</b>";
            }

            $("#result").html(text);
        }
    });
}

function restorePasswordAction(e, formData)
{
    e.preventDefault();

    $.ajax({
        url: "passw.php",
        type: "post",
        data: "type=restorePassword&" + formData,
        mimeType: "multipart/form-data",
        cache: false,
        processData: false,
        success: function(data)
        {
            let text = "";
            switch(data)
            {
                case 'passwordChanged':
                    text = "<b>Success</b>";
                    break;
                case 'userNotFound':
                    text = "<b>User not found</b>";
                    break;
                case 'passwordChangeFailed':
                    text = "<b>Password not changed, something wrong</b>";
                    break;
                case 'restoreWrongKey':
                    text = "<b>Wrong key</b>";
                    break;
                default:
                    text = "<b>Error</b>";
            }

            $("#result").html(text);
        }
    });
}