var wizard;
$(function() {
    // SmartWizard initialize
    wizard = $("#signupWizard").smartWizard({
        selected: 0, // Initial selected step, 0 = first step
        theme: 'dots', // theme for the wizard, related css need to include for other than default theme
        justified: true, // Nav menu justification. true/false
        autoAdjustHeight: false, // Automatically adjust content height
        backButtonSupport: true, // Enable the back button support
        enableUrlHash: false, // Enable selection of the step based on url hash
        transition: {
            animation: 'none', // Animation effect on navigation, none|fade|slideHorizontal|slideVertical|slideSwing|css(Animation CSS class also need to specify)
            speed: '400', // Animation speed. Not used if animation is 'css'
            easing: '', // Animation easing. Not supported without a jQuery easing plugin. Not used if animation is 'css'
            prefixCss: '', // Only used if animation is 'css'. Animation CSS prefix
            fwdShowCss: '', // Only used if animation is 'css'. Step show Animation CSS on forward direction
            fwdHideCss: '', // Only used if animation is 'css'. Step hide Animation CSS on forward direction
            bckShowCss: '', // Only used if animation is 'css'. Step show Animation CSS on backward direction
            bckHideCss: '', // Only used if animation is 'css'. Step hide Animation CSS on backward direction
        },
        toolbar: {
            position: 'bottom', // none|top|bottom|both
            showNextButton: false, // show/hide a Next button
            showPreviousButton: false, // show/hide a Previous button
            extraHtml: '' // Extra html to show on toolbar
         },
        anchor: {
            enableNavigation: false, // Enable/Disable anchor navigation 
            enableNavigationAlways: false, // Activates all anchors clickable always
            enableDoneState: true, // Add done state on visited steps
            markPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
            unDoneOnBackNavigation: true, // While navigate back, done state will be cleared
            enableDoneStateNavigation: true // Enable/Disable the done state navigation
        },
        keyboard: {
            keyNavigation: false, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
            keyLeft: [37], // Left key code
            keyRight: [39] // Right key code
        },
        lang: { // Language variables for button
            next: 'Next',
            previous: 'Previous'
        },
        disabledSteps: [], // Array Steps disabled
        errorSteps: [], // Array Steps error
        warningSteps: [], // Array Steps warning
        hiddenSteps: [], // Hidden steps
        getContent: null // Callback function for content loading
    });
});

$(".tab-content").attr("style","");
var placeSlug ="";
function processButton(typeStep){
    processPhoneInput();
    switch(typeStep) {
        case "sendcode":
            var formData={
                type: typeStep ,
                phone: $("#phone").val(),
                loginType:"place",
                redirectUrl: window.location.pathname,
                method: $('input[name="method"]:checked').val(),
            }
            $.ajax({
                url: "/svc/place/otp" ,
                contentType: "json",
                method:"POST",
                data: JSON.stringify(formData),
            }).done(function(response) {
                $("#otpsection").show();
                $("#verify").show();
                $("#sendcode").hide();
                $(".tab-content").attr("style","");
                processPhoneInput();
                showNotification("info",response.text);
            }).fail(function(jqXHR,textStatus, errorThrow){
                $("#sendcode").html("<span class='material-symbols-outlined'>replay</span>&nbsp;Resend");
                showNotification("error",JSON.parse(jqXHR.responseText).text + " |  phone : " + $("#phone").val() );
            });
            
            break;
        case "verify":
            var formData={
                type: typeStep ,
                phone: $("#phone").val(),
                otp: $("#otp").val(),
                loginType:'place',
            }
            $.ajax({
                url: "/svc/place/otp" ,
                contentType: "json",
                method:'POST',
                data: JSON.stringify(formData),
            }).done(function(response) {
                $("#phone").addClass("wlReadonly");
                $("#otpsection").hide();
                $("#verify").hide();
                $("#sendcode").hide();
                $(".tab-content").attr("style","");
                $("#phoneVerified").show();
                
                /*Find Owner*/
                var formDataOwner={
                    ownerPhoneNumber: $("#phone").val(),
                    loginType:'place',
                }
                $.ajax({
                    url: "/svc/place/owner/info" ,
                    contentType: "json",
                    method:'POST',
                    data: JSON.stringify(formDataOwner),
                }).done(function(response) {
                    $("#name").val(response.details.placeOwner.name);
                    $("#addressline1").val(response.details.placeOwner.addressline1);
                    $("#addressline2").val(response.details.placeOwner.addressline2);
                    $("#addressline3").val(response.details.placeOwner.addressline3);
                    $("#city").val(response.details.placeOwner.city);
                    $("#state").val(response.details.placeOwner.state);
                    $("#country").val(response.details.placeOwner.country);
                    $("#postalcode").val(response.details.placeOwner.postalcode);
                    $("#email").val(response.details.placeOwner.email);
                    showNotification("info",response.text);
                }).fail(function(jqXHR,textStatus, errorThrow){
                    showNotification("error",JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
                });

                showNotification("info",response.text);
                $('#signupWizard').smartWizard("next");
            }).fail(function(jqXHR,textStatus, errorThrow){
                $("#sendcode").show();
                //$("#sendcode").text("Resend");
                $("#sendcode").html("<span class='material-symbols-outlined'>replay</span>&nbsp;Resend");
                showNotification("error", JSON.parse(jqXHR.responseText).text + " | phone : " + $("#phone").val());
            });
            break;
        case "establish":
            if($("#confimrMyEstablishment").prop("checked") && $("#termsAndConditions").prop("checked") && window.wlPlace !=null)
            {
                $('#signupWizard').smartWizard("next");
            }
            else
                showNotification("error","You must agree to Terms and Conditions.");
            break;
        case "save":
            $('#signupWizard').smartWizard("next");
            break;
        case "finish":
            var url ="/place/" + placeSlug + "/profile";
            $(location).prop('href', url);
            break;
        default:
            // code block
        }
}
function createPlace(){
    window.wlPlace.ownerInfo = {
        phone : $("#phoneOwner").val(),
        name : $("#name").val(),
        addressline1 : $("#addressline1").val(),
        addressline2 : $("#addressline2").val(),
        addressline3 : $("#addressline3").val(),
        city : $("#city").val(),
        state : $("#state").val(),
        country : $("#country").val(),
        postalcode : $("#postalcode").val(),
        email : $("#email").val(),
    };
    console.log(window.wlPlace);
     $.ajax({
        url: "/svc/place/new" ,
        contentType: "json",
        method:'POST',
        data: JSON.stringify(window.wlPlace), //Window.wlPlace comes from googlemaps.js
    }).done(function(response) {
        showMessage("info",response.text, "placeCreationStatus",10000);
        showNotification("info",response.text);
        
        placeSlug = response.details.placeSlug;
        /*
        var formData={
            phone : $("#phoneOwner").val(),
            name : $("#name").val(),
            addressline1 : $("#addressline1").val(),
            addressline2 : $("#addressline2").val(),
            addressline3 : $("#addressline3").val(),
            city : $("#city").val(),
            state : $("#state").val(),
            country : $("#country").val(),
            postalcode : $("#postalcode").val(),
            email : $("#email").val(),
        };
        
        $.ajax({
            url: "/svc/place/" +  placeSlug + "/profile/owner" ,
            contentType: "json",
            method:'POST',
            data: JSON.stringify(formData),
        }).done(function(response) {
            showMessage("info","Owner Information Updated", "placeCreationStatus",10000);
            showNotification("info","Owner Information Updated");

            $('#signupWizard').smartWizard("setOptions", {
                toolbar: {
                    showNextButton: false, // show/hide a Next button
                }
            });

            $("#finishWizard").show();

        }).fail(function(jqXHR,textStatus, errorThrow){
            showNotification("error", JSON.parse(jqXHR.responseText).text);
            showMessage("info",JSON.parse(jqXHR.responseText).text, "placeCreationStatus",10000);
            showNotification("info",JSON.parse(jqXHR.responseText).text);
        });
        */
        $("#finishWizard").show();

    }).fail(function(jqXHR,textStatus, errorThrow){
        showNotification("error", JSON.parse(jqXHR.responseText).text);
        showMessage("info",JSON.parse(jqXHR.responseText).text, "placeCreationStatus",10000);
        showNotification("info",JSON.parse(jqXHR.responseText).text);
    });
}

$("#signupWizard").on("showStep", function(e, anchorObject, stepIndex, stepDirection, stepPosition) {
   switch(stepIndex){
        case 1:
            $("#pac-card").show();
            initMap();
            $('#signupWizard').smartWizard("setOptions", {
                toolbar: {
                    showNextButton: false, // show/hide a Next button
                    showPreviousButton:false
                }});
            break;
        case 2:
            $('#signupWizard').smartWizard("setOptions", {
                    toolbar: {
                        showNextButton: false, // show/hide a Next button
                    }
            });
            $("#phoneOwner").val($("#phone").val());
            break;
        case 3:
            $('#signupWizard').smartWizard("setOptions", {
                toolbar: {
                    showNextButton: false, // show/hide a Next button
                }
            });
           
            if(stepDirection=="forward")
                createPlace();
            break;
            
        case 4:
            console.log(stepIndex);
            break;
        default:
            break;
   }
});

