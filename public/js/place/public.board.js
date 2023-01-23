
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
                },
                onDataLoaded:function(grid, data){
                    this.updateWaitTimes();
                }
            },
            fields: [
                { name: "customer", title:"Customer", type: "text",  validate: "required" ,
                        itemTemplate: function(value, item) {  
                                return "" 
                                    + "<span class=\"wlGridCustName\">" +value.firstname + " " + value.lastname  +"</span>";
                            },
                            editing : false,
                    
                },

                { name: "customer", title:"Phone", type: "text",  validate: "required" ,
                        itemTemplate: function(value, item) {  
                            return "" 
                            + "<span class=\"wlGridPhone\"> <span class=\"material-symbols-outlined material-symbols-small\">smartphone</span> " +value.phone +" </span>";
                                           
                        },
                    editing : false
                 },
               
                { name: "guests", title:"Guests", type: "text",  validate: "required" ,
                        itemTemplate: function(value, item) {  
                            return "" 
                                + "<span class=\"\"><span class=\"material-symbols-outlined material-symbols-small\">event_seat</span> Adults :" + item.adults + ", Kids : " + (item.children==null ? "0" : item.children)  + "</span>";
                                
                        },
                    editing : false
                 },
                 { name: "details", title:"Reserved", type: "text",  validate: "required",
                        itemTemplate: function(value, item) {  
                            return "" 
                                + "<div id='wlResDateCur' class='wlResDateCur' " 
                                + " data-date='" + new Date(item.reservationDt.date + " " + item.reservationDt.timezone).toLocaleString('default') +"'"
                                + " data-status='" + item.status +  "'"
                                + " data-resid='" + item.reservationid + "'>"
                                + "</div>" 
                                + "<span class=\"wlGridResDt\"><span class=\"material-symbols-outlined material-symbols-small\">schedule</span> " + new Date(item.reservationDt.date +" " + item.reservationDt.timezone).toLocaleString('default')  
                                + "</span>" },
                    editing : false
                 },
                 { name: "details", title:"Details", type: "text",  validate: "required",
                 itemTemplate: function(value, item) {  
                            return "" 
                                +  "<span class=\"material-symbols-outlined material-symbols-small\">fingerprint</span> [ID: " + item.reservationid+" ]";
                    },
                    editing : false
                },
              ]
    });
}

var jsGridWait = getPlaceReservationGrid("#jsGridWait","jsGridWait");
var jsGridCall = getPlaceReservationGrid("#jsGridCall","jsGridCall");

// $("#jsGridWait").jsGrid("loadData").done(function() {
//     this.updateWaitTimes();
// });
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