$(function() {
    //window.loggedIn=false;

} );
function processButton(typeStep){
    processPhoneInput();
    switch(typeStep) {
        case "sendcode":
            var formData={
                type: typeStep ,
                phone: $("#phone").val(),
                logintype:$('input[name="logintype"]:checked').val(),
                method: $('input[name="method"]:checked').val(),
            }
            $.ajax({
                url: "/svc/customer/otp" ,
                contentType: "json",
                method:'POST',
                data: JSON.stringify(formData),
            }).done(function(response) {
                $("#otpsection").show();
                $("#verify").show();
                $("#sendcode").hide();
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
            };
            $.ajax({
                url: "/svc/customer/otp" ,
                contentType: "json",
                method:'POST',
                data: JSON.stringify(formData),
            }).done(function(response) {
               
                $("#phone").addClass("wlReadonly");
                $("#otpsection").hide();
                $("#verify").hide();
                $("#sendcode").hide();

                $("#customerName").text(response.details.customer.firstname + " " + response.details.customer.lastname);
                $("#customerPhone").text(response.details.customer.phone);

                showNotification("info",response.text);
                showNotification("info", "Welcome back " + response.details.customer.firstname + " " + response.details.customer.lastname );
                processPhoneInput();
                window.loggedIn=true;
               
                $("#loginForm").hide();
                $("#nextsteps").show();
               
            }).fail(function(jqXHR,textStatus, errorThrow){
                $("#sendcode").show();
                $("#sendcode").html("<span class='material-symbols-outlined'>replay</span>&nbsp;Resend");
                showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
            });
            break;
        case 'customerprofile':
            $(location).attr('href',"/customer/profile");
            break;
        case 'customerreservations':
            $(location).attr('href',"/customer/reservations");
            break;
        default:
            // code block
        }  
}

