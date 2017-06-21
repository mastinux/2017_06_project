function checkEmail(email) { // valid email
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function checkPassword(password){ // contains at least one character and one number
    var re = /[A-Za-z]+[0-9]+/;
    return re.test(password);
}

function register(){
    var email = document.getElementById("new-email").value;
    var password = document.getElementById("new-password").value;
    var repeated_password = document.getElementById("new-password-repeated").value;

    console.log("entered register()");

    // checking empty values
    if ( !email || !password || !repeated_password ){
        console.log("Email or password not inserted in registration form.");
        printMessage("warning", "Email or password not inserted in registration form.");
        return false;
    }

    // checking email
    if ( !checkEmail(email) ){
        console.log("Invalid email.");
        printMessage("danger", "Invalid email inserted in registration form.");
        return false;
    }

    // checking match between password and repeated_password
    if (password != repeated_password) {
        console.log("Password does not match.");
        printMessage("danger", "Passwords inserted do not match in registration form.");
        return false;
    }

    // checking password
    if ( !checkPassword(password) ){
        console.log("Password does not contain at least one character and one number.");
        printMessage("danger", "Password must contain at least one character and one number.");
        return false;
    }

    return true;
}

function login() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    // checking empty values
    if ( !username || !password ){
        console.log("Email or password not inserted in login form.");
        printMessage("warning", "Username or password not inserted in login form.");
        return false;
    }

    // checking email
    if ( !checkEmail(username) ){
        console.log("Invalid email.");
        printMessage("danger", "Invalid email inserted in login form.");
        return false;
    }

    return true;
}