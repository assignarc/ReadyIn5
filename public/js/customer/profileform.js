$(function(){
    if(window.loggedIn){
        $.ajax({
            url: "/svc/customer/info" ,
            contentType: "json",
            method:'GET',
        }).done(function(response) {
            showNotification("info",response.text);
            $("#custphone").val(response.details.customer.phone);
            $("input[name=custMethod][value='"+response.details.customer.contactMethod+"']").prop("checked",true);
            $("#custfirstname").val(response.details.customer.firstname);
            $("#custlastname").val(response.details.customer.lastname);
            $("#custName").text(response.details.customer.firstname + " " + response.details.customer.lastname);
            $("#wlAccordion").accordion( "option", "active", 1 );
        }).fail(function(jqXHR,textStatus, errorThrow){
            showNotification("error",JSON.parse(jqXHR.responseText).text);
        });
    }
});
$("#detailsForm").validate({
    rules: {
        custfirstname: "required",
        custlastname: "required"
    },
    messages: {
        custfirstname: "Please enter first name",
        custlastname: "Please enter last name",
    }
});
$("#detailsForm").on("submit", function(event){
    event.preventDefault();
    if(!$("#detailsForm").valid()){
        showNotification("error","Please fix the errors before submit");
        return;
    }
    
    formData = {
        "firstname":$("#custfirstname").val(),
        "lastname":$("#custlastname").val(),
        "phone":$("#custphone").val(),
        "contactMethod": $("input[name='custMethod']:checked").val()
    };
    $.ajax({
        url: "/svc/customer/profile" ,
        contentType: "json",
        method:'POST',
        data: JSON.stringify(formData),
    }).done(function(response) {
        showNotification("info",response.text);
        $("#wlAccordion").accordion( "option", "active", 2);
    }).fail(function(jqXHR,textStatus, errorThrow){
        showNotification("error",JSON.parse(jqXHR.responseText).text);
    });
    return false;
});