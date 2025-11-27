$(function() {
    //window.loggedIn=false;
} );
var phoneInput;
function processButton(typeStep){
    initializePhoneField("phone","phoneInput",null);
    switch(typeStep) {
        case "sendcode":
            var formData={
                type: typeStep ,
                phone: $("#phone").val(),
                logintype:$("#logintype").val(),
                method: $('input[name="method"]:checked').val(),
            }
            $.ajax({
                url: "/svc/place/otp" ,
                contentType: "json",
                method:'POST',
                data: JSON.stringify(formData),
            }).done(function(response) {
                $("#otpsection").show();
                $("#verify").show();
                $("#sendcode").hide();
                $("#methodToggle").hide();
                $(".tab-content").attr("style","");
                $("#phone").prop("readonly","readonly");
                showNotification("info",response.text);
                processPhoneInput();
            }).fail(function(jqXHR,textStatus, errorThrow){
                showNotification("error",JSON.parse(jqXHR.responseText).text + " |  phone : " + $("#phone").val() );
            });
            break;
        case "verify":
            var formData={
                type: typeStep ,
                phone: $("#phone").val(),
                otp: $("#otp").val(),
                logintype:$('input[name="logintype"]:checked').val(),
                method: $('input[name="method"]:checked').val(),
            }
            $.ajax({
                url: "/svc/place/otp" ,
                contentType: "json",
                method:'POST',
                data: JSON.stringify(formData),
            }).done(function(response,textStatus, xhr) {
                $("#phone").addClass("wlReadonly");
                $("#otpsection").hide();
                $("#verify").hide();
                $("#sendcode").hide();
                showNotification("info",response.text);
                console.log(xhr.getResponseHeader("token"));
                window.loggedIn=true;
                $("#nextsteps").show();
                showNotification("info","Loading dashboard");
                $(location).attr('href',"/place/manager");
               
            }).fail(function(jqXHR,textStatus, errorThrow){
                $("#sendcode").show();
                $("#sendcode").text("Resend");
                showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
            });
            break;
        case 'placeinfo':
            $(location).attr('href',"/place/" + wlPlaceSlug + "/info");
            break;
        case 'placeprofile':
            $(location).attr('href',"/place/" +wlPlaceSlug +"/profile");
            break;
        case 'placereservations':
            $(location).attr('href',"/place/" +wlPlaceSlug+"/reservations");
            break;
        default:
            // code block
        }  
}



