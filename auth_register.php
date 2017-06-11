<?php
    include 'global_settings.php';
    include 'functions.php';
    include 'functions_database.php';

    set_https();
    check_enabled_cookies();

    //$success = true;
    //$err_msg = "";

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET': {
            redirect_with_message("auth_login.php", "w", "Register action must be a post method.");
            break;
        }
        case 'POST':{
            if ( !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['password-repeated']) )
                redirect_with_message("auth_login.php", "w", "Email or password not set in registration form.");
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password_repeated = $_POST['password-repeated'];
            break;
        }
    }

    if ( $email == "" || $password == "" || $password_repeated == "" ) {
        // request does not contain email or password or password_repeated
        redirect_with_message("auth_login.php", "w", "Email or password not inserted in registration form.");
    }
    else{
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // checking unique email


            //redirect_with_message("index.php", "i", "checking " . $email . ".");
            check_new_user($email);
            //redirect_with_message("index.php", "i", $email . " checked.");

            if ( strcmp($password, $password_repeated) == 0){
                if( !preg_match("/[A-Za-z]+[0-9]+/", $password) ){
                    redirect_with_message("auth_login.php", "w", "Password must contain at least one character and one number.");
                }
                else{
                    // valid email and password

                    insert_new_user($email, $password);

                    session_start();
                    $_SESSION['231826_user'] = $email;
                    $_SESSION['231826_time'] = time();
                    redirect_with_message("index.php", "s", "Registered and logged in as " . $email . ".");
                }
            }
            else{
                // passwords mismatch
                redirect_with_message("auth_login.php", "d", "Passwords inserted do not match.");
            }
        }
        else{
            // invalid email
            redirect_with_message("auth_login.php", "d", "Invalid email inserted in registration form.");
        }
    }
?>