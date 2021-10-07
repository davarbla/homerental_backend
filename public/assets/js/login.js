$.noConflict();

jQuery(document).ready(function($) {

    "use strict";

    // Load Resize 
    $(window).on("load resize", function(event) {
        var windowWidth = $(window).width();
        if (windowWidth < 1010) {
            $('body').addClass('small-device');
        } else {
            $('body').removeClass('small-device');
        }

    });

    $("#formLogin").submit(function(event) {
        var em = $('#email').val();
        var ps = $('#password').val();

        console.log('email : ' + em + ' password ' + ps);

        $('#loadMe').modal('show');
        $('#formLogin').submit();

        setTimeout(function() {
            $('#loadMe').modal('hide');
            //window.location.href = 'home/login?';
        }, 2000);


        event.preventDefault();
        return false;
    });


});