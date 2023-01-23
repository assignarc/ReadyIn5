/**
 * @license
 * Copyright 2019 Google LLC. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0
 */
// [START maps_places_autocomplete]
// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
//<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?language=&amp;hl=en&key=AIzaSyAfWzCJqeFCkGQe0ONg_drO6_14NE0cW8E&libraries=places"></script>
function initMap() {
    const map = new google.maps.Map(document.getElementById("placeMap"), {
      center: { lat: 40.749933, lng: -73.98633 },
      zoom: 13,
      mapTypeControl: false,
    });
    //const card = document.getElementById("pac-card");
    const input = document.getElementById("pac-input");
    const biasInputElement = document.getElementById("use-location-bias");
    const strictBoundsInputElement = document.getElementById("use-strict-bounds");
    const options = {
      fields: ["formatted_address","international_phone_number","formatted_phone_number" , "plus_code", "geometry", "name", "place_id","adr_address", "address_components"],
      strictBounds: false,
      types: ["establishment"],
    };
  
    //map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);
  
    const autocomplete = new google.maps.places.Autocomplete(input, options);
  
    // Bind the map's bounds (viewport) property to the autocomplete object,
    // so that the autocomplete requests use the current map bounds for the
    // bounds option in the request.
    // [START maps_places_autocomplete_bind]
    autocomplete.bindTo("bounds", map);
  
    // [END maps_places_autocomplete_bind]
    const infowindow = new google.maps.InfoWindow();
    const infowindowContent = document.getElementById("infowindow-content");
  
    infowindow.setContent(infowindowContent);
  
    const marker = new google.maps.Marker({
      map,
      anchorPoint: new google.maps.Point(0, -29),
    });
  
    autocomplete.addListener("place_changed", () => {
      infowindow.close();
      marker.setVisible(false);
  
      const place = autocomplete.getPlace();
  
      if (!place.geometry || !place.geometry.location) {
        // User entered the name of a Place that was not suggested and
        // pressed the Enter key, or the Place Details request failed.
        showMessage("error","No details available for input: '" + place.name + "'");
        return;
      }
  
      // If the place has a geometry, then present it on a map.
      if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
      } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17);
      }
  
      marker.setPosition(place.geometry.location);
      marker.setVisible(true);
      //console.log("Place_Data: Name: " + place.getName() + "\tLatLng: " + place.getLatLng() + "\tAddress: " + place.getAddress() + "\tAddress Component: " + place.getAddressComponents());

      displayPlace(place);
      $("#establish").show();
      infowindowContent.children["place-name"].textContent = place.name;
      infowindowContent.children["place-address"].textContent =place.formatted_address;
      infowindow.open(map, marker);
    });

  function getAddressObject(address_components) {
      var ShouldBeComponent = {
        home: ["street_number"],
        postalcode: ["postal_code"],
        street: ["street_address","route"],
        intersection: ["intersection"],
        colloquialarea : ["colloquial_area"],
        neighborhood : ["neighborhood", "premise", "subpremise"],
        phone : ["international_phone_number","formatted_phone_number"],
        landmark :["landmark"],
        region: [
          "administrative_area_level_1",
          "administrative_area_level_2",
          "administrative_area_level_3",
          "administrative_area_level_4",
          "administrative_area_level_5"
        ],
        sublocality: [
          "sublocality",
          "sublocality_level_1",
          "sublocality_level_2",
          "sublocality_level_3",
          "sublocality_level_4"
        ],
        city: [
          "locality"
        ],
        country: ["country"]
      };

      var address = {
        home: "",
        street: "",
        intersection: "",
        colloquialarea :"",
        neighborhood : "",
        landmark :"",
        city: "",
        postalcode: "",
        region: "",
        country: "",
        phone :""
      };
      address_components.forEach(component => {
        for (var shouldBe in ShouldBeComponent) {
          if (ShouldBeComponent[shouldBe].indexOf(component.types[0]) !== -1) {
              address[shouldBe] = component.long_name;
          }
        }
      });
      return address;
    }
  /*Test Data*/
    // var wlPlace ={
    //   "name": "Bedekar Misal",
    //   "lat": 18.5148471,
    //   "long": 73.84992969999999,
    //   "placeId": "ChIJyaFFIHHAwjsR8EPkdCKUoSM",
    //   "address_formatted": "418, Munjabacha Bole Rd, Narayan Peth, Pune, Maharashtra 411030, India",
    //   "address_adr_address": "<span class=\"street-address\">418, Munjabacha Bole Rd</span>, <span class=\"extended-address\">Narayan Peth</span>, <span class=\"locality\">Pune</span>, <span class=\"region\">Maharashtra</span> <span class=\"postal-code\">411030</span>, <span class=\"country-name\">India</span>",
    //   "address": {
    //       "home": "418",
    //       "street": "Munjabacha Bole Road",
    //       "intersection": "",
    //       "colloquialarea": "",
    //       "neighborhood": "",
    //       "landmark": "",
    //       "city": "Pune",
    //       "postalcode": "411030",
    //       "region": "Maharashtra",
    //       "country": "India",
    //       "sublocality": "Narayan Peth"
    //   },
    //   "address_components": [
    //       {
    //           "long_name": "418",
    //           "short_name": "418",
    //           "types": [
    //               "street_number"
    //           ]
    //       },
    //       {
    //           "long_name": "Munjabacha Bole Road",
    //           "short_name": "Munjabacha Bole Rd",
    //           "types": [
    //               "route"
    //           ]
    //       },
    //       {
    //           "long_name": "Narayan Peth",
    //           "short_name": "Narayan Peth",
    //           "types": [
    //               "sublocality_level_1",
    //               "sublocality",
    //               "political"
    //           ]
    //       },
    //       {
    //           "long_name": "Pune",
    //           "short_name": "Pune",
    //           "types": [
    //               "locality",
    //               "political"
    //           ]
    //       },
    //       {
    //           "long_name": "Pune",
    //           "short_name": "Pune",
    //           "types": [
    //               "administrative_area_level_3",
    //               "political"
    //           ]
    //       },
    //       {
    //           "long_name": "Pune Division",
    //           "short_name": "Pune Division",
    //           "types": [
    //               "administrative_area_level_2",
    //               "political"
    //           ]
    //       },
    //       {
    //           "long_name": "Maharashtra",
    //           "short_name": "MH",
    //           "types": [
    //               "administrative_area_level_1",
    //               "political"
    //           ]
    //       },
    //       {
    //           "long_name": "India",
    //           "short_name": "IN",
    //           "types": [
    //               "country",
    //               "political"
    //           ]
    //       },
    //       {
    //           "long_name": "411030",
    //           "short_name": "411030",
    //           "types": [
    //               "postal_code"
    //           ]
    //       }
    //   ],
    //   "plus_compoundCode": "GR7X+WX Pune, Maharashtra, India",
    //   "plus_globalCode": "7JCMGR7X+WX"
    // };

    var wlPlace;


    window.wlPlace = wlPlace;

    function displayPlace(place){
        window.wlPlace = {
                name : place.name,
                lat: place.geometry.location.lat(),
                long: place.geometry.location.lng(),
                placeId: place.place_id,
                phone: place.international_phone_number,
                address_formatted: place.formatted_address,
                address_adr_address : place.adr_address,
                address : getAddressObject(place.address_components),
                address_components: place.address_components,
                plus_compoundCode : place.plus_code.compound_code,
                plus_globalCode : place.plus_code.global_code,
                ownerInfo : ""
        };

        $("#locationMeta").empty();
        displayLocationMeta("Name ", place.name);
       
        displayLocationMeta("Phone", place.international_phone_number)
        displayLocationMeta("Address" , place.formatted_address);

        displayLocationMeta("Location", place.geometry.location.lat() + " / " + place.geometry.location.lng());
        displayLocationMeta("Place ID", place.place_id);
        displayLocationMeta("Plus Code" , place.plus_code.global_code + " / " + place.plus_code.compound_code);
        //displayLocationMeta("Address 1" , place.adr_address);
        for (var key in place.address_components) {
            //displayLocationMeta(place.address_components[key].types[0],place.address_components[key].long_name);
        }

    }
    function displayLocationMeta(name, value) {
        $("<label>", {"class": "wlPlaceKey", "for": "i-" + name , "id" : "l-" + name }).text(name +" : ").appendTo($("#locationMeta"));
        $("<br>").appendTo($("#locationMeta"));
        $("<label>", {"class": "wlPlaceValue", "id": "i" + name}).text(value).appendTo($("#locationMeta"));
        $("<br>").appendTo($("#locationMeta"));
    }
    // Sets a listener on a radio button to change the filter type on Places
    // Autocomplete.

    $("#changetype").change(function() {
        console.log( $('option:selected', this).val() );
        var type=$('option:selected', this).val();
        if(type=="all")
            type="";
        autocomplete.setTypes([type]);
        input.value="";
    });
   
    biasInputElement.addEventListener("change", () => {
      if (biasInputElement.checked) {
        autocomplete.bindTo("bounds", map);
      } else {
        // User wants to turn off location bias, so three things need to happen:
        // 1. Unbind from map
        // 2. Reset the bounds to whole world
        // 3. Uncheck the strict bounds checkbox UI (which also disables strict bounds)
        // [START maps_places_autocomplete_unbind]
        autocomplete.unbind("bounds");
        autocomplete.setBounds({ east: 180, west: -180, north: 90, south: -90 });
        // [END maps_places_autocomplete_unbind]
        strictBoundsInputElement.checked = biasInputElement.checked;
      }
  
      input.value = "";
    });
    strictBoundsInputElement.addEventListener("change", () => {
      autocomplete.setOptions({
        strictBounds: strictBoundsInputElement.checked,
      });
      if (strictBoundsInputElement.checked) {
        biasInputElement.checked = strictBoundsInputElement.checked;
        autocomplete.bindTo("bounds", map);
      }
  
      input.value = "";
    });
  }
  
  window.initMap = initMap;
  // [END maps_places_autocomplete]