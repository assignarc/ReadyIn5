/* WLDateField */
    var WLDateField = function(config) {
        jsGrid.Field.call(this, config);
    };
    WLDateField.prototype = new jsGrid.Field({
        css: "date-field",            // redefine general property 'css'
        align: "right",              // redefine general property 'align'
        wlDateDisplayFormat:"default",
        wlDateOptions : { year: '2-digit', month: '2-digit', day: '2-digit', hour:'numeric',minute:'numeric' },
        wlCustomProperty: "WLDateField",      // custom property
        wlReadOnly : false,

        sorter: function(index1, index2) {
          //  return new Date(index1) < new Date(index2);
        },
        itemTemplate: function(value) {
           //return $("div").val(value).countdown({since: new Date(value)});
            return new Date(value).toLocaleString(this.wlDateDisplayFormat,this.wlDateOptions);
        },

        insertTemplate: function(value) {
            return this._insertPicker = $("<input>").datepicker({ defaultDate: new Date() });
        },

        editTemplate: function(value) {
            if(this.readOnly)
                return new Date(value).toLocaleString('default');
            return this._editPicker = $("<input>").datepicker().datepicker("setDate", new Date(value));
        },

        insertValue: function() {
            return this._insertPicker.datepicker("getDate").toISOString();
        },

        editValue: function(value) {
            return value; //this._editPicker.datepicker("getDate").toISOString();
        }
    });
    jsGrid.fields.date = WLDateField;

/* WLTimeField */
    var WLTimeField = function(config) {
        jsGrid.Field.call(this, config);
    };
    WLTimeField.prototype = new jsGrid.Field({
        css: "date-field",            // redefine general property 'css'
        align: "right",              // redefine general property 'align'
        wlTimeDisplayFormat:"default",
        wlTimeOptions : { timeFormat: 'HH:mm:ss',
                            startTime: new Date(0, 0, 0, 0, 0, 0),
                            interval: 30,
                            dynamic: true,
                        },
        wlCustomProperty: "WLTimeField",      // custom property
        wlReadOnly : false,

        itemTemplate: function(value) {
            return value;
            //new Date(value).toString("hh:mm tt");//new Date(Date.parse(value)).toString("hh:mm tt"); //new Date(Date.parse(value)).toString("hh:mm tt");// (new Date(value)).toString("hh:mm tt");//.getTime();
        },

        insertTemplate: function(value) {
            return this._insertPicker = $("<input>").timepicker(new Date(value),this.wlTimeOptions);
        },

        editTemplate: function(value) {
            return this._editPicker = $("<input>").val(value).timepicker(this.wlTimeOptions);
        },

        insertValue: function() {
            return this._insertPicker.timepicker(this.wlTimeOptions);
        },

        editValue: function(value) {
            return this._editPicker.val();
        }
    });
    jsGrid.fields.time = WLTimeField;

/* WLStatusChangeField */
    var WLStatusChangeField = function(config) {
        jsGrid.Field.call(this, config);
    };
    WLStatusChangeField.prototype = new jsGrid.Field({
        css: "button-field",            // redefine general property 'css'
        align: "center",              // redefine general property 'align'
        wlToStatus: "",      // custom property
        wlUpdateGrids : "TEST",
        wlToStatusIcon : "",
        itemTemplate: function(value, item) {
            var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
            wlToStatusIcon = getStatusIcon(this.wlToStatus);
            if(this.wlToStatus=="") return "N/A";
            var customEditButton = $("<button>")//.attr({class: "customGridEditbutton jsgrid-button jsgrid-edit-button"})
                .html(getStatusIconSpan(this.wlToStatus,true))
                .attr("title","Set to " + this.wlToStatus)
                .attr("wlToStatus",this.wlToStatus)
                .attr("wlUpdateGrids",this.wlUpdateGrids)
                .click(function(e) {
                    var request = { 
                        "wlToStatus"  : $(this).attr("wlToStatus") ,
                        "reservationId" : item.reservationid
                    };
                    $.ajax({
                        url: "/svc/place/"+ wlPlaceSlug +"/reservations/status" ,
                        dataType: "json",
                        type: "POST",
                        data: JSON.stringify(request)
                    }).done(function(response, statusText, xhr) {
                        grids =  $(e.currentTarget).attr("wlUpdateGrids");//$(e.target).attr('wlUpdateGrids');
                        showNotification("info",response.text);
                        $.each(grids.split(","), function(index, value ) {
                            $("#"+value).jsGrid("loadData");
                          });
                    }).fail(function(jqXHR,textStatus, errorThrow){
                        showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
                    });
                    e.stopPropagation();
                });
            return customEditButton; // $("<div>").append($customEditButton);
        },
    });
    jsGrid.fields.statuschange = WLStatusChangeField;

/* Data */

   


