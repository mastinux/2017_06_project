function removeElementById(id) {
    var element = document.getElementById(id);
    element.parentNode.removeChild(element);
}

function printMessage(type, msg) {
    var types = ['info', 'success', 'warning', 'danger'];
    for (var i = 0; i < types.length; i++)
        if (document.getElementById(types[i] + "-msg"))
            removeElementById(types[i] + "-msg");

    var navbar = document.getElementById("navbar");

    var div = document.createElement("div");
    div.setAttribute("id", type + "-msg");
    div.setAttribute("class", "col-lg-12");
    navbar.parentNode.insertBefore(div, navbar.nextElementSibling);

    var textDiv = document.createElement("div");
    textDiv.setAttribute("id", type + "-msg");
    textDiv.setAttribute("class", "alert alert-" + type + " alert-dismissible");
    textDiv.setAttribute("role", "alert");
    textDiv.innerHTML = msg;

    div.appendChild(textDiv);

    var button = document.createElement("button");
    button.setAttribute("type", "button");
    button.setAttribute("class", "close");
    button.setAttribute("data-dismiss", "alert");
    button.setAttribute("aria-label", "Close");

    textDiv.appendChild(button);

    var span = document.createElement("span");
    span.setAttribute("aria-hidden", "true");
    span.innerHTML = "&times;";

    button.appendChild(span);
}

function printCookieDisabledMessage() {
    printMessage('warning', 'Cookies disabled, to use this site you have to enable them.')
}

function check_thr(){
    var new_value = parseFloat(document.getElementById("user_input").value);
    var max_bid = parseFloat(document.getElementById("max_bid").value);

    if(new_value * 1000 % 10 != 0){
        printMessage("warning", "THR must be a multiple of 0.01.");
        return false;
    }

    if (new_value <= max_bid){
        console.log(new_value, " is lower than ", max_bid);
        printMessage("warning", "Your new THR must be greater than max BID.");
        new_value = max_bid + 0.01;
        document.getElementById("user_input").value = new_value;

        return false;
    }

    return true;
}