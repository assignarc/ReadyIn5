
/* Grid 
    @param divName - Name of the Div where grid is added
    @param storeName - Store name to store data. 
    @param updateGrids - Names of grid to be updated. 
*/
function getPlaceReservationGrid(divName,storeName, updateGrids){
    return new jsGrid.Grid($(divName), 
        {
            width: $(divName).attr("wlWidth"),
            inserting: false,
            editing: true,
            sorting: true,
            paging: true,
            pageSize: $(divName).attr("wlPageSize"),
            pageIndex: 1,
            wlUpdateGrids : updateGrids,
            autoload: true,
            controller:{
                loadData: function() {
                    var d = $.Deferred();
                    $.ajax({
                        url: ($(divName).attr("wlBaseUrl") + $(divName).attr("wlFromStatus")).replace("{wlPlaceSlug}",wlPlaceSlug) ,
                        dataType: "json",
                   }).done(function(response) {
                        showNotification("info", divName.replace("#jsGrid","") + " list loaded" );
                        d.resolve(response.details.reservations);
                    }).fail(function(jqXHR,textStatus, errorThrow){
                        showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
                    });
                    return d.promise();
                },
                insertItem: function(insertingClient) {
                    this.clients.push(insertingClient);
                },
                updateItem: function(updatingReservation) { 
                    console.log(updatingReservation);
                },
                deleteItem: function(deletingClient) {
                    var clientIndex = $.inArray(deletingClient, this.clients);
                }
            },
            fields: [
                { name: "customer", title:"Customer", type: "text",  validate: "required" , width:200,
                        itemTemplate: function(value, item) {  
                                return "" 
                                + "<div class='two-columns'><div class='col'>" 
                                    + "<span class=\"wlGridCustName\">" +value.firstname + " " + value.lastname  +"</span>"
                                    + "<span class=\"wlGridPhone\"><span class=\"material-symbols-outlined material-symbols-small\">smartphone</span>  " + value.phone +" </span>"
                                    + "<span class=\"wlGridGuestCount\"><span class=\"material-symbols-outlined material-symbols-small\">event_seat</span> Adults :" + item.adults + ", Kids : " + (item.children==null ? "0" : item.children)  + "</span>" 
                                + "</div>"
                                + "<div class='col'>"
                                    + "<div id='wlResDateCur' class='wlResDateCur' " 
                                    + " data-date='" + new Date(item.reservationDt.date + " " + item.reservationDt.timezone).toLocaleString('default') +"'"
                                    + " data-status='" + item.status +  "'"
                                    + " data-resid='" + item.reservationid + "'>"
                                    + "</div>" 
                                    + "<span class=\"wlGridResDt\"><span class=\"material-symbols-outlined material-symbols-small\">schedule</span> " + new Date(item.reservationDt.date +" " + item.reservationDt.timezone).toLocaleString('default')  
                                    +       "<br><div class=\"wlStatus-"+item.status + "\">" + getStatusIconSpan(item.status,true)  + "</div>"
                                    +       "<span class=\"material-symbols-outlined material-symbols-small\">fingerprint</span> [ID: " + item.reservationid+" ]"
                                    + "</span>"
                                + "</div>";
                               
                            },
                            editing : false,
                        sorter: function(value1, value2) {
                            return (value1.firstname + " " + value2.lastname).localeCompare(value2.firstname + " " + value2.lastname);
                        },
                },
                { title:"Action",width:70,type:"statuschange", wlToStatus:$(divName).attr("wlToStatus") , wlUpdateGrids : updateGrids },
            ]
    });
}

var jsGridWait = getPlaceReservationGrid("#jsGridWait","jsGridWait", ["jsGridWait","jsGridCall"]);
var jsGridCall = getPlaceReservationGrid("#jsGridCall","jsGridCall", ["jsGridCall","jsGridSeat"]);
var jsGridSeat = getPlaceReservationGrid("#jsGridSeat","jsGridSeat", ["jsGridSeat","jsGridServe"]);
var jsGridServe = getPlaceReservationGrid("#jsGridServe","jsGridServe", ["jsGridServe","jsGridNoShow"]);
var jsGridNoShow = getPlaceReservationGrid("#jsGridNoShow","jsGridNoShow", ["jsGridNoShow","jsGridWait"]);

$("#jsGridWait").jsGrid("loadData").done(function() {
    this.updateWaitTimes();
});
function updateWaitTimes(){
    $(".wlResDateCur").each(function(i, div) {
        $(div).countdown(new Date($(div).attr("data-date")), {elapse: true}).on('update.countdown', function(event) {
            if($(div).attr("data-status")=="WAIT"){
                $(div).html(event.strftime('<b>%H:%M:%S</b>'));
                $("<br>").appendTo($(div));
            }
        });
        
    });
}