
$("#tabs").tabs();

$("#jsGridPast").jsGrid({
    width: "96%",
    inserting: false,
    editing: false,
    sorting: true,
    paging: true,
    pageSize: 3,
    pageIndex: 1,
    pagerFormat: "Page ( {pageIndex} of {pageCount} ) : {prev} {next}",
    autoload: true,
    noDataContent: "No past reservation found.",
    loadMessage: "Please, wait while loading past reservations",
    controller: {
        loadData: function() {
            var d = $.Deferred();
            $.ajax({
                url: "/svc/customer/reservations/past",
                dataType: "json"
            }).done(function(response) {
                d.resolve(response.details.reservations);
                showNotification("info",response.text);
            }).fail(function(jqXHR,textStatus, errorThrow){
                $("#sendcode").show();
                $("#sendcode").text("Resend");
                showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
            });
            return d.promise();
        }
    },

    fields: [
        { name: "place", title:"Place", type: "text", validate: "required",
            itemTemplate:function(value, item){
                return "<a href='/place/" + value.slug + "/info'>" + value.name + "</a>";
                
            }
         },
         { name: "place", title:"Party Size", type: "text", validate: "required",
            itemTemplate:function(value, item){
                return "Adults :" + item.adults + " | Kids : " + (item.children ? item.children: 0) + 
                        "<div class=\"wlResDate\">" 
                                + new Date(item.reservationDt.date +" " + item.reservationDt.timezone).toLocaleString('default') +
                        "</div>" ;
            }
         },
         { name: "place", title:"Reservation", type: "text", align:"center", validate: "required",
            itemTemplate:function(value, item){
                //console.log("[ ID: " + item.reservationid + " ]");
                return  "<div class=\"wlStatus-"+item.status + "\">" + 
                                getStatusIconSpan(item.status,true)  + 
                        "</div>" + 
                        "{ID:" + item.reservationid + "}";
            }
         },
    ]
});

$("#jsGridCurrent").jsGrid({
    width: "96%",
   // height: "400px",
    inserting: false,
    selecting:false,
    editing: false,
    sorting: true,
    paging: true,
    pageSize: 2,
    pageIndex: 1,
    pagerFormat: "Page ( {pageIndex} of {pageCount} ) : {prev} {next}",
    autoload: false,
    noDataContent: "Not waiting on any reservation! ",
    loadMessage: "Please, wait...",
    controller: {
        loadData: function() {
            var d = $.Deferred();
            $.ajax({
                url: "/svc/customer/reservations/current",
                dataType: "json"
            }).done(function(response) {
                showNotification("info",response.text);
                d.resolve(response.details.reservations);
            }).fail(function(jqXHR,textStatus, errorThrow){
                $("#sendcode").show();
                $("#sendcode").text("Resend");
                showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
            });
            return d.promise();
        },
       
    },
   
    //TO-DO : Create Trimmed Snippet version of Place Info that can be discplyed anywhere. 

    fields: [
        {
            title:"Status", type: "text", validate: "required", width:"35px",
            itemTemplate:function(value, item){
                var ret ="";
                switch(item.status){
                    case "WAIT":
                        ret = "<div id='wlStatus' class='wlStatus-"+ item.status +"'>" + getStatusIconSpan(item.status, true) + "</div>" 
                                + $("#hourGlassTemplate").html() 
                                + "<div id='wlResDateCur' class='wlResDateCur' " 
                                + " data-date='" + new Date(item.reservationDt.date + " " + item.reservationDt.timezone).toLocaleString('default') +"'"
                                + " data-status='" + item.status +  "'"
                                + " data-resid='" + item.reservationid + "'"
                                + "></div>";
                        break;
                    default:
                        ret = "Reservation is <div id='wlStatus' class='wlStatus-"+ item.status +"'>" + getStatusIconSpan(item.status, true) + "</div>" 
                            + "<div id='wlResDateCur' class='wlResDateCur' " 
                            + " data-date='" + new Date(item.reservationDt.date + " " + item.reservationDt.timezone).toLocaleString('default') +"'"
                            + " data-status='" + item.status +  "'"
                            + " data-resid='" + item.reservationid + "'"
                            + "></div>";
                    break;
                }
                return ret;

            }
        },

        { 
            name: "place", title:"Reservation", type: "text", validate: "required",
            itemTemplate:function(value, item){
                return  "<div class='wlPlaceInfo' id='wlPlaceInfo'>" + 
                        // "    <div class='placeImage'>" + 
                        // "        <img class='placeLogo' src='"+"/images/ri5/default-place-logo.png"+"' id='wlPlaceLogo'>" + 
                        // "    </div>" + 
                        "    <div class='placeMeta'>" + 
                        "        <span class='material-symbols-outlined'>storefront</span>" + 
                        "        <label id='wlPlaceName' class='wlPlaceName'>"+ item.place.name +"</label>" + 
                        "        <br>" + 
                        "        <a href='https://www.google.com/maps/place/?q=place_id:"+item.place.slug+"' id='wlPlaceAddressLink' title='Google Maps'>" + 
                        "          <span class='material-symbols-outlined'>map</span>" + 
                        "        </a>" + 
                        "        <a href='/place/"+item.place.slug+"/info' id='wlPlaceInfoLink' title='Place Information'>" + 
                        "            <span class='material-symbols-outlined'>gps_fixed</span>" + 
                        "        </a>" + 
                        "        <!--<br><label id='wlPlaceAddress'></label><br>-->" + 
                        "        <span class='material-symbols-outlined'>deskphone</span>&nbsp;" + 
                        "            <a href='' id='wlPlacePhoneLink' src='tel:"+item.place.phone+"'><label id='wlPlacePhone'>"+item.place.phone+"</label>" + 
                        "        </a>" + 
                        "        <br><span class='material-symbols-outlined'>event_seat</span>&nbsp;" + 
                        "        Adults : <span id='wLAdults'>"+item.adults+"</span> | Kids : <span id='wlKids'>"+(item.children ? item.children: 0)+"</span>" + 
                        "        <br><span class='material-symbols-outlined'>schedule</span>&nbsp;" + 
                        "        <span id='wlResDate' class='wlResDate'>"+new Date(item.reservationDt.date +" " + item.reservationDt.timezone).toLocaleString('default')+"</span>" + 
                        "        <br><span class='material-symbols-outlined'>fingerprint</span>&nbsp;[ID:" + item.reservationid + "]"
                        "    </div>" + 
                        "</div>";
                //return $("#placeTemplate").html();
            }
         },
         /*
         { name: "place", title:"Place", type: "text", validate: "required",
            itemTemplate:function(value, item){
                return "<a href='/place/" + value.slug + "/info'>" + value.name + "</a>";
                
            }
         }
         { name: "place", title:"Party Size", type: "text", validate: "required",
            itemTemplate:function(value, item){
                return "Adults :" + item.adults + " | Kids : " + (item.children ? item.children: 0) +"<br><div class=\"wlStatus-"+item.status + "\">" + item.status + "</div>";
                
            }
         },
         { name: "place", title:"Reservation", type: "text", validate: "required",
            itemTemplate:function(value, item){
                return  "<div class=\"wlResDate\">" + new Date(item.reservationDt.date +" " + item.reservationDt.timezone).toLocaleString('default') + "</div>"  + 
                        "<div class=\"wlResDateCur\" data=\""+ new Date(item.reservationDt.date +" " + item.reservationDt.timezone).toLocaleString('default') +"\">" + 
                        "<div>" ;
            }
         }
         */
    ]
});
$("#jsGridCurrent").jsGrid("loadData").done(function() {
    $(".wlResDateCur").each(function(i, div) {
        $(div).countdown(new Date($(div).attr("data-date")), {elapse: true}).on('update.countdown', function(event) {
            if($(div).attr("data-status")=="WAIT"){
                $(div).html(event.strftime('<b>%H:%M:%S</b>'));
                // $("<div>").html(event.strftime('<b>%H:%M:%S</b>')).appendTo($(div));
                $("<br>").appendTo($(div));
                $("<button>", {
                    html : "<span class=\"material-symbols-outlined\">cancel</span><span class='wlButtonTxt'>Cancel</span>",
                    onmousedown : "processButton('cancel',"+ $(div).attr("data-resid")  +")",
                    title: "Canel reservation"
                }).appendTo($(div));
            }
        });
    });
    checkForUpdates();
});

function checkForUpdates(){
    $(".wlResDateCur").each(function(i, div){
        if($(div).attr("data-status")=="WAIT" && window.updateStatus==true){
            window.updateStatus=false;
            $.ajax({
                url: "/svc/reservation/"+ $(div).attr("data-resid") +"/status",
                dataType: "json",
                method:"GET",
            }).done(function(response) {
                //console.log(new Date().toLocaleString('default') + " - " + $(div).attr("data-resid") + "-" + window.updateStatus + " - " + response.text);
                showNotification("info",response.text +  getStatusIconSpan(response.details.status, true));
                window.updateInterval = response.details.nextcheck;
                if($(div).attr("data-status")!=response.details.status){
                    $.confirm({
                        title: 'Reservation changed',
                        content: 'Reservation status changed to : ' + getStatusIconSpan(response.details.status, true) ,
                        buttons: {
                            OK: function () {
                                showNotification("info","Reloading reservations");
                                window.updateStatus = false;
                                return;
                            },
                        }
                    });
                    showNotification("info","The reservations reloading");
                    $("#jsGridCurrent").jsGrid("loadData");
                }
                window.updateStatus=true;
            }).fail(function(jqXHR,textStatus, errorThrow){
                showNotification("error", JSON.parse(jqXHR.responseText).text);
                window.updateInterval = window.updateInterval + 1000;
                window.updateStatus=true;
             });
        }
    });
}

setInterval(function(){
    checkForUpdates(); // this will run after every 5 seconds
}, window.updateInterval);

function processButton(typeStep,data){
     switch(typeStep) {
        case "cancel":
            $.confirm({
                title: 'Cancel reservation?',
                content: 'If you cancel, you will loose place in line.',
                buttons: {
                    confirm: function () {
                        showNotification("info","Reservation cancellation requested.");
                        var formData={
                            type: typeStep ,
                            reservationId: data,
                            loginType:"customer"
                        };
                        $.ajax({
                            url: "/svc/reservation/cancel" ,
                            contentType: "json",
                            method:'POST',
                            data: JSON.stringify(formData),
                        }).done(function(response) {
                            showNotification("info",response.text);
                            $("#jsGridCurrent").jsGrid("loadData");
                        }).fail(function(jqXHR,textStatus, errorThrow){
                            showNotification("error",JSON.parse(jqXHR.responseText).text + " |  phone : " + $("#phone").val() );
                        });
                    },
                    cancel: function () {
                        showNotification("info","Reservation not cancelled.");
                    },
                }
            });
            break;
       
        default:
            // code block
    }  
}