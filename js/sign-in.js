$(document).ready(function() {
    $('input').on('keydown', function(e) {
        if(e.keyCode === 13) {
            $('#sign-in').click();
        }
    });
    $('#sign-in').on('click', function() {
        const email = $('#email').val();
        const password = $('#password').val();
        if (email.length === 0 || password.length === 0) {
            toastr.warning('Email and password are all required.', 'Warning');
            return;
        }
        
        $.post(BASE_URL + 'controller/signInController.php', {
            email,
            password
        }, (res) => {
            switch (res) {
                case 'success':
                    window.location.href = BASE_URL + 'home.php';
                    break;
                case 'not_allowed':
                    toastr.warning('Please get the permission.', 'Warning!');
                    break;
                default:
                    toastr.warning('Please type your email and password correctly.', 'Warning!');
                    break;
            }
        })
    });
});