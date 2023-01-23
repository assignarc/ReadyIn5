$("#jsRestaurants").jsGrid(
        {
            width:"96%",
            inserting: false,
            editing: false,
            sorting: false,
            paging: true,
            pageSize: 5,
            pageIndex: 1,
            autoload: true,
            controller:{
                loadData: function() {
                    var d = $.Deferred();
                    $.ajax({
                        url: '/svc/place/owner/dashboard' ,
                        dataType: "json",
                        type:"GET"
                   }).done(function(response) {
                        showNotification("info", "Authorized places loaded." );
                        d.resolve(response.details.places);
                    }).fail(function(jqXHR,textStatus, errorThrow){
                        showNotification("error", JSON.parse(jqXHR.responseText).text);
                    });
                    return d.promise();
                },
            },
            fields: [
                { name: "places", title:"Places", type: "text", width:200,
                        itemTemplate: function(value, item) {  
                                return "" 
                                    + "<div class=\"wlPlaceInfo\" id=\"wlPlaceInfo\" style=\"display:flex\">"
                                    + "    <div class=\"placeImage\">"
                                    + "       <img class=\"wlLogo\" src=\"/images/ri5/default-place-logo.png\" id=\"placeLogo\">"
                                    + "    </div>"
                                    + "    <div class=\"placeMeta\">"
                                    + "        <label id=\"placeName\">"+ item.name +"</label>"
                                    + "        <br>"
                                    + "        <a href=\"" + "/place/"+ item.slug + "/info\" id=\"placeInfoLink\">"
                                    + "                   <span class=\"material-symbols-outlined\">gps_fixed</span>"
                                    + "        </a>"
                                    + "        <a href=\"" + "https://www.google.com/maps/place/?q=place_id:" + item.slug + "\" id=\"placeAddressLink\">"
                                    + "                   <span class=\"material-symbols-outlined\">map</span>"
                                    + "        </a>" 
                                   // + "        <br>"
                                   // + "        <label id=\"placeAddress\">" + item.addressdata.address_adr_address +  "</label>" 
                                   // + "        <br>" 
                                    + "        <span class=\"material-symbols-outlined\">deskphone</span>&nbsp;<a href=\tel:" + item.phone + "\" id=\"placePhoneLink\"><label id=\"placePhone\">" + item.phone + "</label></a>"
                                    + "    </div>"
                                    + "</div><br>"
                                    + "    <button type=\"button\" id=\"ownerManage\" onmousedown=\"processButton('ownerPlaceManage', '"+ item.slug +"')\" data-slug=\"" + item.slug + "\">"
                                    + "         <span class=\"material-symbols-outlined\">supervisor_account</span>&nbsp;Manage Place"
                                    + "     </button>"
                                    + "    <button type=\"button\" id=\"ownerReservations\" onmousedown=\"processButton('placeOwnerReservations', '"+ item.slug +"')\" data-slug=\"" + item.slug + "\">"
                                    + "         <span class=\"material-symbols-outlined\">manage_history</span>&nbsp;Reservations"
                                    + "    </button>"
                                    + "    <button type=\"button\" id=\"publicBoard\" onmousedown=\"processButton('placePublicBoard', '"+ item.slug +"')\" data-slug=\"" + item.slug + "\">"
                                    + "         <span class=\"material-symbols-outlined\">team_dashboard</span>&nbsp;Board"
                                    + "    </button>"
                                    + "";
                            },
                            editing : false,
                        sorter: function(value1, value2) {
                            //return (value1.firstname + " " + value2.lastname).localeCompare(value2.firstname + " " + value2.lastname);
                        },
                }
            ]
     
    });

    function processButton(typeStep, slug){
      
        switch(typeStep) {
            case "ownerPlaceManage":
                $(location).attr('href',"/place/"+ slug + "/profile");
                break;
            case "placeOwnerReservations":
                $(location).attr('href',"/place/"+ slug + "/reservations");
                break;
            case "placePublicBoard":
                $(location).attr('href',"/place/"+ slug + "/board");
                break;
            default:
                // code block
            }  
    }