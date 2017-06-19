<?php
    include 'functions.php';
    include 'functions_database.php';

    session_start();
    if ( $username = user_logged_in() ){
        include 'auth_sessions.php';
        set_https();
    }
    else{
        redirect_with_message('index.php', 'w', 'You must be logged in to update your THR.');
    }

    check_enabled_cookies();

    $success = true;
    $err_msg = "";

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET': {
            redirect_with_message("index.php", "w", "Update THR must be a post method.");
            break;
        }
        case 'POST': {
            if ( !isset($_POST['thr']) )
                redirect_with_message("index.php", "w", "THR value not set in form.");
            $thr_value = $_POST['thr'];
            break;
        }
    }

    if ( $thr_value <= get_max_bid()[0] )
        redirect_with_message('index.php', 'w', 'Your new THR must be greater than max BID.');

    if ( strlen(substr(strrchr($thr_value, "."), 1)) > 2 )
        redirect_with_message('index.php', 'w', 'THR must be a multiple of 0.01.');

    update_user_thr($username, $thr_value);

    redirect_with_message("index.php", "s", "THR updated successfully.");
?>