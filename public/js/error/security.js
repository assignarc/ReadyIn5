
function processButton(typeStep){
   
    switch(typeStep) {
        case 'customerLogin':
            $(location).attr('href',"/customer/login");
            break;
        case 'placeLogin':
            $(location).attr('href',"/place/login");
            break;
        case 'home':
            $(location).attr('href',"/");
            break;
        default:
            $(location).attr('href',"/");
            break;
        }  
}

