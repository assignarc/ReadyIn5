/* Globals Vars*/
window.updateStatus=true;
window.updateInterval=30000;
/* */
var wlStore = {
    store : function(key, value){
        localStorage.setItem(key,JSON.stringify(value));
    },
    retrieve : function(key, value){
        return localStorage.getItem(key);
}   
}; 


function showNotification(type, message){
    showMessage(type,message,"wlNotifications", 5000);
} 
function showMessage(type, message, appendToDiv, fadeTimeout){
    var icon;
    var timeout;
    switch (type) {
        case "error":
            icon="stop_circle";
            timeout = fadeTimeout *3;
            break;
        case "warn":
            icon="warning";
            timeout = fadeTimeout *2;
            break;
        case "notice":
        case "info":
        case "success":
            timeout = fadeTimeout *1;
            icon = "info"; 
            break;
        default:
            timeout = fadeTimeout;
            icon=type;
    }
    var wlNotification = jQuery("<div>", {style: "display:none"});
    $("<span>", {class:"material-symbols-outlined",text:icon}).appendTo(wlNotification);
    $("<span>", {html:message,}).appendTo(wlNotification);
    wlNotification.appendTo($("#" + appendToDiv));
    wlNotification.show().delay(timeout).fadeOut();
} 
function showAlert(type,message){
    $('#dialog').html(message);
    $('#dialog').attr("title", type);
    $('#dialog').dialog({
        autoOpen: true,
        show: "blind",
        hide: "explode",
        modal: true,
        open: function(event, ui) {
            setTimeout(function(){
                $('#dialog').dialog('close');                
            }, 3000);
        },
        buttons: {
            Ok: function() {
              $( this ).dialog( "close" );
            }
          }
    });
}
/*Document Functions*/
$(document).ready(function () {
    $(".cross").hide();
    $(".menu").hide();
    $(".hamburger").click(function () {
        $(".menu").slideToggle("slow", function () {
            $(".hamburger").hide();
            $(".cross").show();
        });
    });
    $(".cross").click(function () {
        $(".menu").slideToggle("slow", function () {
            $(".cross").hide();
            $(".hamburger").show();
        });
    });
    $("#tabs").tabs();
});

$(document).on("ajaxStart", function() {
   $("#progressbar").show();
});
$(document).on("ajaxStop", function() {
    $("#progressbar").hide();
});

function getStatusIconSpan(wlToStatus, includeTextSpan = false){
    if(includeTextSpan)
        return  "<span class='material-symbols-outlined material-symbols-small'>" + getStatusIcon(wlToStatus) + "</span>" + 
                "<span class=\"wlButtonTxt\">&nbsp;" + wlStatusText[wlToStatus] +  "</span>"      
    else
        return "<span class='material-symbols-outlined material-symbols-small'>&nbsp;" + getStatusIcon(wlToStatus) + "</span>"; 
}
function getStatusIcon(wlToStatus){
    var wlToStatusIcon ="";
    switch (wlToStatus) {
        case "WAIT":
            wlToStatusIcon = "hourglass_bottom";
            break;
        case "CALL":
            wlToStatusIcon = "smartphone";
            break;
        case "SEAT":
            wlToStatusIcon = "airline_seat_recline_normal";
            break;
        case "SERVE":
            wlToStatusIcon = "restaurant_menu";
            break;
        case "NOSHOW":
            wlToStatusIcon = "visibility_off";
            break;
        case "EXPIRE":
        case "EXPIRED":
            wlToStatusIcon = "alarm";
            break;
        case "CANCEL":
            wlToStatusIcon = "delete_forever";
            break;
        case "COMPLETE":
            wlToStatusIcon = "done_all";
            break;
        case "":
            wlToStatusIcon = "hourglass_disabled";
            break;
        default:
            break;
    }
    return wlToStatusIcon;
}
/* Phone Field functions */

function initializePhoneField(field,variable,button){
    window[variable] = window.intlTelInput(document.querySelector("#"+field), {
                    initialCountry: "auto",
                    autoPlaceholder: "aggressive",
                    preferredCountries: ["us", "in"],
                    initialCountry: "auto",
                    loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.10.7/build/js/utils.js"),
        });
    //If button is passed, use it to set up event. 
    if(!button || button!=''){
        $('#'+button).on("mousedown", function() {
            $('#'+field).val(window[variable].getNumber());
        });
    }
    
}


/*wlStatus Codes */ 

var wlStatusText = 
{ 
    "WAIT": "Reserved",
    "CALL" : "Ready",
    "SEAT" : "Seated",
    "SERVE" : "Served",
    "EXPIRE" : "Expired",
    "EXPIRED" : "Expired",
    "CANCEL" : "Canceled",
    "NOSHOW" : "No show",
    "COMPLETE" : "COMPLETE",
    "" : "Unknown"
}

statusCodes = [

    { Name: "Reserved", status: "WAIT" },
    { Name: "CALL", status: "CALL" },
    { Name: "SEAT", status: "SEAT" },
    { Name: "SERV", status: "SERVE" },
    { Name: "NOSHOW", status: "NOSHOW" },
    { Name: "EXPR", status: "EXPIRED" }
];