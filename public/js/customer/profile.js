$(function(){
    //if(window.loggedIn){
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
            $("#addressline1").val(response.details.customer.addressline1);
            $("#addressline2").val(response.details.customer.addressline2);
            $("#addressline3").val(response.details.customer.addressline3);
            $("#city").val(response.details.customer.city);
            $("#state").val(response.details.customer.state);
            $("#country").val(response.details.customer.country);
            $("#postalcode").val(response.details.customer.postalcode);

            wlStore.store("token",response.details.token);

        }).fail(function(jqXHR,textStatus, errorThrow){
            showNotification("error",JSON.parse(jqXHR.responseText).text);
        });
    //}
});

$("#detailsForm").validate({
    rules: {
        custfirstname: "required",
        custlastname: "required",
        addressline1: "required",
        city: "required",
        state: "required",
        country: "required",
        postalcode: "required",
    },
    messages: {
        custfirstname: "Please enter first name",
        custlastname: "Please enter last name",
        addressline1: "Please enter address",
        city: "Please enter City",
        state: "Please enter State",
        country: "Please enter Country",
        postalcode: "Please enter Postal Code",
        phone: "Please enter Phone",
    }
});
$("#detailsForm").on("submit", function(event){
    event.preventDefault();
    formData = {
        "firstname":$("#custfirstname").val(),
        "lastname":$("#custlastname").val(),
        "phone":$("#custphone").val(),
        "contactMethod": $('input[name="custMethod"]:checked').val(),
        "addressline1": $("#addressline1").val(),
        "addressline2": $("#addressline2").val(),
        "addressline3": $("#addressline3").val(),
        "city": $("#city").val(),
        "state": $("#state").val(),
        "country": $("#country").val(),
        "postalcode": $("#postalcode").val(),
        "full":"1",
        "token" : wlStore.retrieve("token")

    };
    $.ajax({
        url: "/svc/customer/profile" ,
        contentType: "json",
        method:'POST',
        data: JSON.stringify(formData),
    }).done(function(response) {
        showNotification("info",response.text);
    }).fail(function(jqXHR,textStatus, errorThrow){
        showNotification("error",JSON.parse(jqXHR.responseText).text);
    });
    return false;
});

