/*Phone Field */
function processPhoneInput() {
    $("#"+phoneInputField).val(phoneInput.getNumber());
}
$(document).ready(function(){
    initializePhoneField();
});
var phoneInputField ="phone"; 
var phoneInput; 
function initializePhoneField(){
        phoneInput = window.intlTelInput(document.querySelector("#"+phoneInputField), {
            preferredCountries: ["us", "in",],
            initialCountry: "auto",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
}