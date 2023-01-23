$(function() {
    //window.loggedIn=false;
    $( "#wlAccordion" ).accordion({
        collapsible: true,
        active: false ,
        heightStyle: "content",
        beforeActivate: function(event, ui) {
            if(ui.newHeader.prop("id")=="profilePanel" && !window.loggedIn){
                showNotification("error", "Login first");  
                return false;
            }
            if(ui.newHeader.prop("id")=="reservePanel" && (!window.loggedIn || $("#custfirstname").val()=="" || $("#custlastname").val()=="")){
                showNotification("error", "Profile information required, before reserving a table");  
                return false;
            }
         },
    });
    $("#wlAccordion").accordion("option", "active", 0 );
} );
function processButton(typeStep){
    processPhoneInput();
    switch(typeStep) {
        case "sendcode":
            var formData={
                type: typeStep ,
                phone: $("#phone").val(),
                method: $('input[name="method"]:checked').val(),
                loginType:"customer",
                redirectUrl: window.location.pathname,
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
                loginType:"customer",
            }
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
                showNotification("info",response.text);
                processPhoneInput();
                window.loggedIn=true;
                if(!response.hasOwnProperty("details.customer.firstname")){
                    $("#custphone").val(response.details.customer.phone)
                    $("#custfirstname").val(response.details.customer.firstname);
                    $("#custlastname").val(response.details.customer.lastname);
                }
                $("#wlAccordion").accordion( "option", "active", 1 );
            }).fail(function(jqXHR,textStatus, errorThrow){
                $("#sendcode").show();
               // $("#sendcode").text("Resend");
                $("#sendcode").html("<span class=\"material-symbols-outlined\">replay</span>&nbsp;Resend");
                showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
            });
            break;
        default:
            // code block
        }  
}

