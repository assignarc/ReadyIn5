$("#reservationForm").validate({
    rules: {
        adults: "required",
        children: "required"
    },
    messages: {
        adults: "Please enter more than 1 adult.",
        children: "Children can be zero or more.",
    }
});
$("#viewReservation").on("click",function(event) {
    window.location.href ="/customer/reservations";
});
$("#reservationForm").on("submit", function(event){
    event.preventDefault();
    formData = {
        "adults":$("#adults").val(),
        "children":$("#children").val(),
        "specialnotes" : $("#specialNotes").val(),
        "placeslug": wlPlaceSlug,
        "queueid": $("#queue").val(),
        "phone":$("#custphone").val(),
    };
    $.ajax({
        url: "/svc/place/" + wlPlaceSlug + "/reservation" ,
        contentType: "json",
        method:'POST',
        data: JSON.stringify(formData),
    }).done(function(response) {
        showNotification("info",response.text);
        $("#saveReservation").hide();
        $("#viewReservation").show();
    }).fail(function(jqXHR,textStatus, errorThrow){
        showNotification("error",JSON.parse(jqXHR.responseText).text);
        $("#viewReservation").show();
    });
    return false;
});