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
            redirect_with_message("auth_login.php", "w", "Email already used.");
    }

    function insert_new_user($email, $password){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $password = sanitize_string($password);
        $password = mysqli_real_escape_string($connection, $password);

        // TODO make in unique transaction
        
        $sql_statement = "insert into auctions_user(email, pw) values('$email', md5('$password'))";

        try{
            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("Problems while registering new user (phase 1), please register again.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( $success ){
            $sql_statement = "insert into auctions_thr(email, thr_value, thr_timestamp) values('$email', 0, now())";

            try{
                if ( !mysqli_query($connection, $sql_statement) )
                    throw new Exception("Problems while registering new user (phase 2), please register again.");
            }catch (Exception $e){
                $success = false;
                $err_msg = $e->getMessage();
            }

        }

        mysqli_close($connection);

        if ( !$success )
            redirect_with_message("auth_login.php", "d", $err_msg);
    }

    function get_max_bid(){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = 
            "select * from auctions_thr order by thr_value desc, thr_timestamp";

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

        //sleep(60);

        try {
            // preparing query
            $sql_statement="update auctions_thr set thr_value = '$thr_value' where email = '$username'";
            
            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("Problems while updating your thr.");

        } catch (Exception $e) {
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }
/*
    function get_points_avg(){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = "select avg(c_points) as points_avg from c_comment";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving points mean.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $row = mysqli_fetch_assoc($result);

        $points_avg = $row['points_avg'];

        mysqli_free_result($result);
        mysqli_close($connection);

        return round($points_avg, 1);
    }

    function insert_comment($username, $comment, $points){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $comment = sanitize_string($comment);
        $comment = mysqli_real_escape_string($connection, $comment);

        $points = sanitize_string($points);
        $points = mysqli_real_escape_string($connection, $points);

        try {
            // inserting comment
            $sql_statement = "insert into c_comment(email, c_text, c_points) values('$username', '$comment', '$points')";
            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("You already inserted a comment.");

        } catch (Exception $e) {
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }

    function remove_comment($username){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        try {
            // deleting comment
            $sql_statement = "delete from c_comment where email='$username'";
            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("Problems while removing your comment.");

        } catch (Exception $e) {
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }

    function get_comments(){
        $rows = Array();
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = "select * from c_comment";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving comments.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        while ($row = mysqli_fetch_assoc($result))
            $rows[] = $row;

        mysqli_free_result($result);
        mysqli_close($connection);

        return $rows;
    }

    function get_comment_judge($username, $sign){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        if ( $sign == "plus" )
            $sql_statement = "select sum(plus_count) as s from c_judge where c_comment = '$username'";
        else
            $sql_statement = "select sum(minus_count) as s from c_judge where c_comment = '$username'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while counting judgements.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $row = mysqli_fetch_assoc($result);

        $s = $row['s'];

        mysqli_free_result($result);
        mysqli_close($connection);

        return $s;
    }

    function get_comment_judge_summary($username){
        $s = get_comment_judge($username, "plus") - get_comment_judge($username, "minus");
        if ( $s > 0)
            $s = "+".$s;
        return $s;
    }

    function get_user_judge_on_comment($username, $c_email){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $c_email = sanitize_string($c_email);
        $c_email = mysqli_real_escape_string($connection, $c_email);

        $sql_statement = "select c_judge_count as c from c_judge where email = '$username' and c_comment = '$c_email'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving judge count.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $row = mysqli_fetch_assoc($result);

        $c = $row['c'];

        mysqli_free_result($result);
        mysqli_close($connection);

        return $c;
    }

    function judgment_exists($username, $c_email)
    {
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $c_email = sanitize_string($c_email);
        $c_email = mysqli_real_escape_string($connection, $c_email);

        $sql_statement = "select count(*) as n from c_judge where email = '$username' and c_comment = '$c_email'";

        try {
            if (!($result = mysqli_query($connection, $sql_statement)))
                throw new Exception("Problems while checking if judgment already exists.");
        } catch (Exception $e) {
            $success = false;
            $err_msg = $e->getMessage();
        }

        if (!$success)
            redirect_with_message("index.php", "d", $err_msg);

        $row = mysqli_fetch_assoc($result);

        $n = $row['n'];

        mysqli_free_result($result);
        mysqli_close($connection);

        if ($n == "0")
            return False;
        else
            return True;
    }

    function insert_new_judgment($username, $c_email, $sign){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $c_email = sanitize_string($c_email);
        $c_email = mysqli_real_escape_string($connection, $c_email);

        $sign = sanitize_string($sign);
        $sign = mysqli_real_escape_string($connection, $sign);

        if ( $sign == "plus" ) {
            $p = 1;
            $m = 0;
        }
        else {
            $p = 0;
            $m = 1;
        }

        try {
            // insert
            $sql_statement = "insert into c_judge(email, c_comment, plus_count, minus_count) 
                          values('$username', '$c_email', $p, $m)";
            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("Problems while inserting new judgment.");

        } catch (Exception $e) {
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }

    function update_judgment($username, $c_email, $sign){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $c_email = sanitize_string($c_email);
        $c_email = mysqli_real_escape_string($connection, $c_email);

        $sign = sanitize_string($sign);
        $sign = mysqli_real_escape_string($connection, $sign);

        try {
            // preparing increment
            if ( $sign == "plus" )
                $sql_statement = "update c_judge
                    set plus_count = (plus_count + 1), c_judge_count = (c_judge_count + 1)  
                    where email = '$username' and c_comment = '$c_email'";
            else
                $sql_statement = "update c_judge
                    set minus_count = (minus_count + 1), c_judge_count = (c_judge_count + 1)  
                    where email = '$username' and c_comment = '$c_email'";

            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("You have exceeded 3 judgment for this comment.".$sql_statement);

        } catch (Exception $e) {
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }

    function insert_judgment($username, $c_email, $sign){
        if ( judgment_exists($username, $c_email) == true) {
            if ( get_user_judge_on_comment($username, $c_email) >= 3 )
                redirect_with_message("index.php", "d", "You already judge this comment 3 times. Please judge other comments");
            update_judgment($username, $c_email, $sign);
        }
        else
            insert_new_judgment($username, $c_email, $sign);
    }

?>
*/