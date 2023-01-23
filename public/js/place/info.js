/*Profile*/
  

/*Schedules*/

    $("#jsSchedule").jsGrid({
        width: "96%",
        inserting: false,
        editing: false,
        sorting: false,
        paging: true,
        pageSize: 7,
        pageIndex: 1,
        autoload: true,
        
        controller: {
            loadData: function() {
                var d = $.Deferred();
                $.ajax({
                    url: "/svc/place/"+wlPlaceSlug+"/profile/schedule",
                    method:"GET",
                    dataType: "json"
                }).done(function(response) {
                    showNotification("info","Schedules loaded!");
                    d.resolve(response.details.schedule);
                }).fail(function(jqXHR,textStatus, errorThrow){
                    showNotification("error",JSON.parse(jqXHR.responseText).text);
                });
                return d.promise();
            },
        // onItemUpdated: function(args) { console.log(args)},
        },
       
        fields: [
            { name: "day", title:"Day",readOnly:true, width: 10, headercss: "wLRestScheduleHeader"},
            { name: "openTime", title:"Open" ,readOnly:true, type: "time",width:10, align:"right", headercss: "wLRestScheduleHeader"},
            { name: "closeTime", title:"Close", readOnly:true, type: "time", width:10, align:"right", headercss: "wLRestScheduleHeader"},
        ]
    });

/* Holidays */

    $("#jsHolidays").jsGrid({
        width: "96%",
        inserting: false,
        editing: false,
        sorting: false,
        paging: true,
        pageSize: 5,
        pageIndex: 1,
        autoload: true,
      

        controller: {
            loadData: function() {
                var d = $.Deferred();
            // console.log(wlPlaceSlug);
                $.ajax({
                    url: "/svc/place/"+wlPlaceSlug+"/profile/holidays",
                    method:"GET",
                    contentType: "json",
                }).done(function(response) {
                    d.resolve(response.details.holidays);
                    showNotification("info","Holidays loaded!");
                }).fail(function(jqXHR,textStatus, errorThrow){
                    showNotification("error",textStatus);
                });
                return d.promise();
            }
        },
  
        fields: [
            { name: "holidayName", title:"Holiday", type: "text",  validate: "required",headercss: "wLRestScheduleHeader" ,
                itemTemplate: function(value, item) {  
                         return "<b>" + item.holidayName + "</b><br>" + item.specialNote;
                }
            },
            { name: "holidayDate", title:"Date", type: "date", align:"right", wlDateDisplayFormat:"default",headercss: "wLRestScheduleHeader",
                itemTemplate: function(value, item) {  
                    return new Date(value.date +" " + value.timezone).toLocaleString('default',{
                                                                                            weekday: "long",
                                                                                            year: "numeric",
                                                                                            month: "long",
                                                                                            day: "numeric",
                                                                                        }
                      );
                },
             },
           
        ]
    });

/* Image controls */
    

/* Miscellaneous  */
    $("#tabs").tabs();
