<?php

    function sanitize_string($var) {
        $var = strip_tags($var);
        $var = htmlentities($var);
        $var = stripcslashes($var);
        return $var;
    }

    function connect_to_database() {
        $success = true;
        $err_msg = "";

        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

        try{
            if ( mysqli_connect_error() )
                throw new Exception("Error during connection to DB.\n" . mysqli_connect_errno() . " - " . mysqli_connect_error());
        }
        catch(Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success )
            redirect_with_message("index.php", "d", $err_msg);

        return $connection;
    }

    function check_new_user($email){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();
        
        $email = sanitize_string($email);
        $email = mysqli_real_escape_string($connection, $email);

        $sql_statement = "select * from auctions_user where email = '$email'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while checking new user, please register again.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        $rows = $result->num_rows;

        mysqli_free_result($result);
        mysqli_close($connection);

        if ( !$success )
            redirect_with_message("auth_login.php", "d", $err_msg);

        if ( $rows == 1 )
            redirect_with_message("auth_login.php", "w", "Email already registered on this site.");
    }
    
    function insert_new_user($email, $password){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        try {
            mysqli_autocommit($connection,false);

            $sql_statement = "insert into auctions_user(email, pw) values('$email', md5('$password'))";

            if ( !mysqli_query($connection, $sql_statement) )
                    throw new Exception("Problems while registering new user (phase 1), please register again.");

            $sql_statement = "insert into auctions_thr(email, thr_value) values('$email', 0)";

            if ( !mysqli_query($connection, $sql_statement) )
                    throw new Exception("Problems while registering new user (phase 2), please register again.");

            if ( !mysqli_commit($connection) )
                throw new Exception("Commit failed.");
        }
        catch (Exception $e){
            mysqli_rollback($connection);

            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }

    function get_max_bid(){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = "select * from auctions_thr order by thr_value desc, thr_timestamp";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving max bid.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $num_rows = $result->num_rows;

        if ( $num_rows == 0 ){
            $max_thr = MIN_THR;
            $thr_user_email = "No one has already bidden";
        }
        else if ( $num_rows == 1 ){
            $row = mysqli_fetch_assoc($result);

            if ( $row['thr_value'] == 0 ){
                $max_thr = MIN_THR;
                $thr_user_email = "No one has already bidden";
            }
            else{
                $max_thr = MIN_THR;
                $thr_user_email = $row['email'];
            }
        }
        else{
            $i = 0;
            $first_row = "";
            $second_row = "";

            while($row = mysqli_fetch_assoc($result)){
                if ( $i == 0)
                    $first_row = $row;

                if ( $i == 1){
                    $second_row = $row;
                    break;
                }

                $i++;
            }

            if( $first_row['thr_value'] == 0 ){
                $max_thr = MIN_THR;
                $thr_user_email = "No one has already bidden";
            }
            else{
                if ( $second_row['thr_value'] == 0 ){
                    $max_thr = MIN_THR;
                    $thr_user_email = $first_row['email'];
                }
                else{
                    if ($first_row['thr_value'] == $second_row['thr_value'])
                        $max_thr = $first_row['thr_value'];
                    else
                        $max_thr = $second_row['thr_value'] + 0.01;
                    $thr_user_email = $first_row['email'];
                }
            }

        }

        mysqli_free_result($result);
        mysqli_close($connection);

        return Array($max_thr, $thr_user_email);
    }

    function get_user_thr($username){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);
        
        $sql_statement = "select thr_value from auctions_thr where email = '$username'";
        
        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving user thr.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $row = mysqli_fetch_assoc($result);

        $res = $row['thr_value'];

        if ( $res == 0){
            $res = "You have not set your THR yet";
        }

        mysqli_free_result($result);
        mysqli_close($connection);

        return $res;
    }

    function update_user_thr($username, $thr_value){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $thr_value = sanitize_string($thr_value);
        $thr_value = mysqli_real_escape_string($connection, $thr_value);

        try {
            mysqli_autocommit($connection,false);

            $sql_statement = "select thr_value from auctions_thr for update";

            if ( !mysqli_query($connection, $sql_statement) )
                    throw new Exception("Problems while selecting thr for update." . mysqli_error($connection));

            $sql_statement = "update auctions_thr set thr_value = '$thr_value' where email = '$username'";

            if ( !mysqli_query($connection, $sql_statement) )
                    throw new Exception("Problems while updating your thr." . mysqli_error($connection));

            if ( !mysqli_commit($connection) )
                throw new Exception("Commit failed.");
        }
        catch (Exception $e){
            mysqli_rollback($connection);

            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }
