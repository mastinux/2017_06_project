<?php
    include 'functions.php';
    include 'functions_database.php';

    session_start();
    if ( $username = user_logged_in() ){
        include 'auth_sessions.php';
        set_https();
    }
    else{
        redirect_with_message('index.php', 'w', 'You must be logged in to update your thr.');
    }

    check_enabled_cookies();

    $success = true;
    $err_msg = "";

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET': {
            redirect_with_message("index.php", "w", "Update thr must be a post method.");
            break;
        }
        case 'POST': {
            if ( !isset($_POST['thr']) )
                redirect_with_message("index.php", "w", "Thr value not set in form.");
            $thr_value = $_POST['thr'];
            break;
        }
    }

    if ( $thr_value < get_max_bid()[0] )
        redirect_with_message('index.php', 'w', 'Thr must be greather than current max thr.');

    $user_thr_value = get_user_thr($username); 

    if ( is_numeric($user_thr_value) ){
        if ( $thr_value < $user_thr_value )
            redirect_with_message('index.php', 'w', 'Thr must be greather than your current thr.');
    }
    else{
        if ( $thr_value < MIN_THR )
            redirect_with_message('index.php', 'w', 'Thr must be greather than current max thr.');
    }

    update_user_thr($username, $thr_value);

    redirect_with_message("index.php", "s", "Thr updated successfully.")
?>