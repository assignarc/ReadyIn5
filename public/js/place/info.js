/*Profile*/
  

/*Schedules*/

    $("#jsSchedule").jsGrid({
        width: "auto",
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
                    url: "/svc/place/"+wlPlaceSlug+"/profile/schedules",
                    method:"GET",
                    dataType: "json"
                }).done(function(response) {
                    showNotification("info","Schedules loaded!");
                    d.resolve(response.details.schedules);
                }).fail(function(jqXHR,textStatus, errorThrow){
                    showNotification("error",JSON.parse(jqXHR.responseText).text);
                });
                return d.promise();
            },
        // onItemUpdated: function(args) { console.log(args)},
        },
       
        fields: [
            { name: "day", title:"Day",readOnly:true, width: "auto", headercss: "wLRestScheduleHeader"},
            { name: "openTime", title:"Open" ,readOnly:true, type: "time", align:"right", headercss: "wLRestScheduleHeader"},
            { name: "closeTime", title:"Close", readOnly:true, type: "time", align:"right", headercss: "wLRestScheduleHeader"},
        ]
    });

/* Holidays */

    $("#jsHolidays").jsGrid({
        width: "100%",
        inserting: false,
        editing: false,
        sorting: false,
        paging: true,
        pageSize: 50,
        pageIndex: 1,
        autoload: true,
      

        controller: {
            loadData: function() {
                var d = $.Deferred();
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
           { name: "holidayDate", title:"Date", type: "date", width:"auto", align:"right", wlDateDisplayFormat:"default",headercss: "wLRestScheduleHeader",
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
            { name: "holidayName", title:"Holiday", type: "text", width:"auto", validate: "required",headercss: "wLRestScheduleHeader" ,
                itemTemplate: function(value, item) {  
                         return "<b>" + item.holidayName + "</b><br>" + item.specialNote;
                }
            },
            
           
        ]
    });


