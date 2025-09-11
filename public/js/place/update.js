/*Profile*/
    $("#profileForm").validate({
        rules: {
            slug: "required",
            name: "required",
            addressline1: "required",
            city: "required",
            state: "required",
            country: "required",
            postalcode: "required",
            phone: "required",
        },  
        messages: {
            slug: "Please enter Place Nickname",
            name: "Please enter Place Name",
            addressline1: "Please enter address",
            city: "Please enter City",
            state: "Please enter State",
            country: "Please enter Country",
            postalcode: "Please enter Postal Code",
            phone: "Please enter Phone",
        },
        submitHandler: function(event) {
            profileFormSubmitHandler();     
    }
    });
    function profileFormSubmitHandler(){
        formData = {
            slug: $("#slug").val(),
            name: $("#name").val(),
            addressline1: $("#addressline1").val(),
            addressline2: $("#addressline2").val(),
            addressline3: $("#addressline3").val(),
            city: $("#city").val(),
            state: $("#state").val(),
            country: $("#country").val(),
            postalcode: $("#postalcode").val(),
            phone: $("#phone").val(),
        };

        $.ajax({
            url: "/svc/place/"+wlPlaceSlug+"/profile/meta" ,
            contentType: "json",
            method:'POST',
            data: JSON.stringify(formData),
        }).done(function(response) {
            showNotification("info","Restautant profile updated!");
            initializePhoneField();
        }).fail(function(jqXHR,textStatus, errorThrow){
            showNotification("error","Error loading Place Profile");
        });
    }
    function loadPlaceProfile(){
        $.ajax({
            url: "/svc/place/"+wlPlaceSlug+"/profile/meta" ,
            contentType: "json",
            method:'GET',
        }).done(function(response) {
            $("#slug").val(response.details.place.slug);
            $("#name").val(response.details.place.name);
            $("#addressline1").val(response.details.place.addressline1);
            $("#addressline2").val(response.details.place.addressline2);
            $("#addressline3").val(response.details.place.addressline3);
            $("#city").val(response.details.place.city);
            $("#state").val(response.details.place.state);
            $("#country").val(response.details.place.country);
            $("#postalcode").val(response.details.place.postalcode);
            $("#phone").val(response.details.place.phone);
            showNotification("info","Restautant profile loaded!");
        }).fail(function(jqXHR,textStatus, errorThrow){
            showNotification("error","Error updating Place Profile");
        });
    }
    
    loadPlaceProfile();

/*Schedules*/

    $("#jsSchedule").jsGrid({
        width: "95%",
        inserting: false,
        editing: true,
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
        onItemUpdating: function(args) { 
                if(Date.parse(args.item.openTime) > Date.parse(args.item.closeTime)){
                    showNotification("error","Open time cannot be greater than close time");
                    return false;
                }
                var d = $.Deferred();
                $.ajax({
                    url: "/svc/place/"+wlPlaceSlug+"/profile/schedule",
                    method:"POST",
                    dataType: "json",
                    data: JSON.stringify(args.item),
                }).done(function(response) {
                    showNotification("info","Schedule updated!");
                    $("#jsSchedule").jsGrid("loadData");
                    d.resolve(response);
                }).fail(function(jqXHR,textStatus, errorThrow){
                    showNotification("error",JSON.parse(jqXHR.responseText).text);
                });
                return d.promise();
            },
        fields: [
            { name: "day", title:"Day",readOnly:true, width: 10, headercss: "wLRestScheduleHeader"},
            { name: "openTime", title:"Open" ,type: "time",width:10, align:"right", headercss: "wLRestScheduleHeader"},
            { name: "closeTime", title:"Close", type: "time", width:10, align:"right", headercss: "wLRestScheduleHeader"},
            { type: "control" , title:"Control",width:10,headercss: "wLRestScheduleHeader",
                    modeSwitchButton: false,
                    deleteButton: false,
                    headerTemplate: function() {
                        return "Control";
                    }
            }
        ]
    });

/* Holidays */

    $("#jsHolidays").jsGrid({
        width: "100%",
        inserting: false,
        editing: true,
        sorting: false,
        paging: true,
        pageSize: 5,
        pageIndex: 1,
        autoload: true,

        deleteConfirm: function(holiday) {
            return "The Holiday \"" + holiday.Name + "\" will be removed. Are you sure?";
        },
        deleteItem: function(holiday){

            $.ajax({
                url: "/svc/place/"+wlPlaceSlug+"/profile/holidays" ,
                contentType: 'json',
                method:"DELETE",
                data: JSON.stringify(holiday),
            }).done(function(response) {
                showNotification("info","Place Holiday removed");
                $("#jsHolidays").jsGrid("loadData");
            }).fail(function(jqXHR,textStatus, errorThrow){
                showNotification("error",textStatus);
            });
            return false;
        },
        rowClick: function(args) {
            showHolidayDetailsDialog("Edit", args.item);
        },
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
            { name: "holidayName", title:"Holiday", type: "text",  validate: "required",headercss: "wLRestScheduleHeader" },
            { name: "specialNote", title:"Notes" ,type: "text", headercss: "wLRestScheduleHeader"},
            { name: "holidayDate.date", title:"Date Time", type: "date", align:"right", wlDateDisplayFormat:"default",headercss: "wLRestScheduleHeader"},
            { type: "control" , headercss: "wLRestScheduleHeader",
                    modeSwitchButton: false,
                    editButton: false,
            }
        ]
    });
/* Queues */
    $("#jsQueues").jsGrid({
        width: "100%",
        inserting: false,
        editing: true,
        sorting: false,
        paging: true,
        pageSize: 5,
        pageIndex: 1,
        autoload: true,

        deleteConfirm: function(queue) {
            return "The Queue: \"" + queue.queuename + "\" will be removed. Are you sure?";
        },
        deleteItem: function(queue){
            $.ajax({
                url: "/svc/place/"+wlPlaceSlug+"/profile/queue" ,
                contentType: 'json',
                method:"DELETE",
                data: JSON.stringify(queue),
            }).done(function(response) {
                showNotification("info","Place queues removed");
                $("#jsQueues").jsGrid("loadData");
            }).fail(function(jqXHR,textStatus, errorThrow){
                showNotification("error",textStatus);
            });
            return false;
        },
        onItemUpdating: function(args) { 
            args.item.capcityTotal = args.item.capacityAdults  + args.item.capacityChildren;
            console.log(args.item);
            var d = $.Deferred();
            $.ajax({
                url: "/svc/place/"+wlPlaceSlug+"/profile/queues",
                method:"POST",
                dataType: "json",
                data: JSON.stringify(args.item),
            }).done(function(response) {
                showNotification("info","Queues updated!");
                $("#jsQueues").jsGrid("loadData");
                d.resolve(response);
            }).fail(function(jqXHR,textStatus, errorThrow){
                showNotification("error",JSON.parse(jqXHR.responseText).text);
            });
            return d.promise();
        },
        // rowClick: function(args) {
        //     showQueueDetailsDialog("Edit", args.item);
        // },

        controller: {
            loadData: function() {
                var d = $.Deferred();
            // console.log(wlPlaceSlug);
                $.ajax({
                    url: "/svc/place/"+wlPlaceSlug+"/profile/queues",
                    method:"GET",
                    contentType: "json",
                }).done(function(response) {
                    d.resolve(response.details.queues);
                    showNotification("info","Queues loaded!");
                }).fail(function(jqXHR,textStatus, errorThrow){
                    showNotification("error",textStatus);
                });
                return d.promise();
            }
        },

        fields: [
            { name: "queuename", title:"Queue", required:"true", type: "text",headercss: "wLRestScheduleHeader" },
            { name: "capacityAdults", title:"Adults" ,type: "number", headercss: "wLRestScheduleHeader"},
            { name: "capacityChildren", title:"Children", type: "number", align:"right", wlDateDisplayFormat:"default",headercss: "wLRestScheduleHeader"},
            { name: "capcityTotal", title:"Total", type: "number", readOnly:"true", align:"right", wlDateDisplayFormat:"default",headercss: "wLRestScheduleHeader"},
            { type: "control" , headercss: "wLRestScheduleHeader",
                    modeSwitchButton: false,
                    editButton: true,
            }
        ]
    });



    $("#detailsDialog").dialog({
        autoOpen: false,
        width: 400,
        Close: function() {
            $("#detailsForm").validate().resetForm();
            $("#detailsForm").find(".error").removeClass("error");
        }
    });

    $("#detailsForm").validate({
        rules: {
            holidayName: "required",
            specialNote: "required",
            holidayDate: "required"
        },
        messages: {
            holidayName: "Please enter name of Holiday",
            specialNote: "Please enter a note for the holiday",
            holidayDate: "Please enter date greater than today."
        },
        submitHandler: function(event) {
            formSubmitHandler();     
    }
    });
    var formSubmitHandler = $.noop;
    var showHolidayDetailsDialog = function(dialogType, holiday) {
        if(dialogType=="Add"){
            $("#holidayid").val("");
            $("#holidayName").val("");
            $("#specialNote").val("");
            $("#holidayDate").val(new Date().toLocaleDateString('default',
                {year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'}
            ));
        }
        if(dialogType=="Edit"){
            $("#holidayid").val(holiday.holidayid);
            $("#holidayName").val(holiday.holidayName);
            $("#specialNote").val(holiday.specialNote);
            $("#holidayDate").val(new Date(holiday.holidayDate.date).toLocaleDateString('default',
                {year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'}
            ));
        }
        $("#holidayDate").datepicker({
            dateFormat: "DD, MM d, yy",
            minDate: new Date()
        });
        formSubmitHandler = function(event) {
            saveClient(holiday, dialogType === "Add");
        };
        
        $("#detailsDialog").dialog("option", "title", dialogType + " Holiday")
                .dialog("open");
    };
    var saveClient = function(holiday, isNew) {
        
        formData = {
            "holidayid":$("#holidayid").val(),
            "holidayName":$("#holidayName").val(),
            "specialNote":$("#specialNote").val(),
            "holidayDate":$("#holidayDate").val()
        };
        $("#detailsDialog").dialog("close");
        $.ajax({
            url: "/svc/place/"+wlPlaceSlug+"/profile/holidays" ,
            contentType: "json",
            method:'POST',
            data: JSON.stringify(formData),
        }).done(function(response) {
            showNotification("info","Holidays Updated!");
            $("#jsHolidays").jsGrid("loadData");
        }).fail(function(jqXHR,textStatus, errorThrow){
            showNotification("error",textStatus);
        });

        
    };
/* Image controls */
    $( function() {
        $("#wlImgControl")
            .accordion({
                collapsible: true,
                heightStyle: "content"
            })
            .sortable({
                axis: "y",
                handle: "h3",
                stop: function( event, ui ) {
                // IE doesn't register the blur when sorting
                // so trigger focusout handlers to remove .ui-state-focus
                ui.item.children( "h3" ).triggerHandler( "focusout" );
                // Refresh accordion to handle new order
                $(this ).accordion( "refresh" );
                }
            });
    } );

/* Miscellaneous  */
    $("#tabs").tabs();

/*Phone Field */
    
    function process(event) {
        $("#phone").val(phoneInput.getNumber());
    }
    $(document).ready(function(){
        initializePhoneField();
    });
    var phoneInputField ="phone"; 
    var phoneInput; 
    function initializePhoneField(){
            phoneInput = window.intlTelInput(document.querySelector("#phone"), {
                preferredCountries: ["us", "in",],
                initialCountry: "auto",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });
    }