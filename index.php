<?php

    include 'functions.php';
    include 'functions_database.php';
    include 'functions_messages.php';
    session_start();
    check_enabled_cookies();

    if ( $username = user_logged_in() ){
        include 'auth_sessions.php';
        set_https();
    }
    else{
        unset_https();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Single Object Auction</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="bootstrap/html5shiv.min.js"></script>
    <script type="text/javascript" src="bootstrap/respond.min.js"></script>
    <![endif]-->

    <link href="site_style.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="site_functions.js"></script>
<!--
    <script type="text/javascript" src="z_shares_functions.js"></script>
-->
</head>

<body>

    <?php
        include 'navbar.php';
        include 'no_script_messages.html';
        manage_messages();
        list($max_thr, $max_thr_email) = get_max_bid();
        $user_max_thr = get_user_thr($username);
    ?>

    <div class="col-lg-12">

        <div class="col-lg-4">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php if ($username){?>
                        Your Account
                    <?php }else{?>
                        Log in or Register
                    <?php }?>
                </div>
                <div class="panel-body">
                    <?php
                        if ( !$username ) {
                    ?>
                        <form method="get" action="auth_login.php" class="navbar-form navbar-left">
                            <a href="auth_login.php">
                                <button type="button" class="btn btn-default">
                                    <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Login
                                </button>
                            </a>
                        </form>
                    <?php
                        }
                        else{
                    ?>
                        <form class="navbar-form navbar-left">
                            <a href="auth_logout.php">
                                <button type="button" class="btn btn-default">
                                    <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
                                    Logout
                                </button>
                            </a>
                        </form>
                    <?php
                        }
                    ?>
                </div>

                <?php if ($username) {?>
                <ul class="list-group">
                    <li class="list-group-item">
                        Username: <?php echo $username;?>
                    </li>
                </ul>
                <?php }?>
            </div>
            <?php if($username){ ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Update your THR
                    </div>
                    <div class="panel-body">
                        <p>Your current THR: <?php echo $user_max_thr; ?></p>

                        <?php 
                            if( is_numeric($user_max_thr) ){
                                if ($username == $max_thr_email){ 
                        ?>
                                <div class="alert alert-success" role="alert">You are the best bidder.</div>
                        <?php 
                                }else{
                        ?>
                            <div class="alert alert-danger" role="alert">Your THR has been overcome.</div>
                        <?php
                                }
                            }
                            else{
                        ?>
                            <div class="alert alert-info" role="alert">Set your first THR.</div>
                        <?php        
                            }
                        ?>

                        <form method="post" action="thr_update.php" onsubmit="return check_thr()">
                            <!-- TODO check greater than current thr value by id -->
                            <div class="input-group">
                                <span class="input-group-addon">€</span>

                                <input id="user_input" type="number" name="thr" min="<?php echo $max_bid ?>" value="<?php echo ($max_thr + 0.01) ?>" step="0.01" class="form-control text-right">

                                <div class="input-group-btn">
                                   <button type="submit" class="btn btn-default">Submit</button> 
                                </div>
                                
                            </div>
                        </form>

                    </div>
                </div>
            <? } ?>
        </div>

        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Highest BID</h3>
                </div>
                <div class="panel-body">
                    <p>BID: <?php echo $max_thr; ?></p>
                    <input id="max_bid" hidden="true" value="<?php echo $max_thr; ?>">

                    <p>User: <?php echo $max_thr_email; ?> </p>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        if (navigator.cookieEnabled == false) {
            // preventing site usage
            removeElementById('left-panel');
            removeElementById('right-panels');
            // printCookieDisabledMessage();
        }
    </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="bootstrap/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
