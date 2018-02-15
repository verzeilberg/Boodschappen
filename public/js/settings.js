$(document).ready(function () {

    $("#time").timeDropper({
        format: 'H:mm'
    });


    getOrderFrequency();

    $('select[name="orderFrequency"]').click(function () {
        getOrderFrequency();
    });

});

function getOrderFrequency() {
    var orderFrequency = $('select[name="orderFrequency"]').val();

    switch (orderFrequency) {
        case '1':
            $('#daily').show();
            $('#weekly').hide();
            $('#monthly').hide();
            break;
        case '2':
            $('#daily').hide();
            $('#weekly').show();
            $('#monthly').hide();
            break;
        case '3':
            $('#daily').hide();
            $('#weekly').hide();
            $('#monthly').show();
            break;
        default:
            console.log('umh');
            
            $('#daily').hide();
            $('#weekly').hide();
            $('#monthly').hide();
    }
}