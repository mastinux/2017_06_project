<?php
    include 'global_settings.php';

    function redirect_https(){
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }

    function set_https(){
        if( isset($_SERVER["HTTPS"]) ) {
            if ( $_SERVER["HTTPS"] != "on" ) {
                redirect_https();
            }
        }
        else
            redirect_https();
    }

    function unset_https(){
        if(isset($_SERVER["HTTPS"])){
            if($_SERVER["HTTPS"] == "on") {
                header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
                exit();
            }
        }
    }

    function remove_cookie($name){
        setcookie($name, "", time()-60*60);
    }

    function check_enabled_cookies(){
        if ( empty($_SERVER['QUERY_STRING']) ) {
            if (!isset($_COOKIE['test'])) {
                setcookie('test', 'test', time() + 60 * 60);
                redirect_with_message("cookie_test.php", "i", "Testing cookies.");
            }
        }
    }

    function redirect_with_message($page, $message_type, $message){
        header("HTTP/1.1 307 temporary redirect");
        $head = "Location: ".$page;

        if (!((empty($message_type) || empty($message))))
            $head = $head."?".$message_type."msg=".urlencode($message);

        header($head);
        exit;
    }

    function user_logged_in() {
        if (isset($_SESSION['231826_user'])) {
            return $_SESSION['231826_user'];
        } else {
            return false;
        }
    }

    function destroy_user_session() {
        $_SESSION=array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-3600*24,
                $params["path"],$params["domain"],
                $params["secure"], $params["httponly"]);
        }
        session_destroy(); // destroy session
    }
?>