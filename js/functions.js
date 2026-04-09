$(document).ready(function() {


    var val1 = 0;

    $('.navbar-handler').click(function() {

        if (val1 == 0) {
            $('.custom-menu').slideToggle();
            $(this).children("svg").toggleClass("fa-bars");
            $(this).children("svg").toggleClass("fa-times");


            val1 = 1;

        } else {
            $('.custom-menu').slideToggle();
            $(this).children("svg").toggleClass("fa-bars");
            $(this).children("svg").toggleClass("fa-times");

            val1 = 0;

        }
    })
});



$(document).ready(function() {

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


    $("#imageUpload").change(function() {
        readURL(this);
    });




    function readURL2(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview2').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreview2').hide();
                $('#imagePreview2').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


    $("#imageUpload2").change(function() {
        readURL2(this);
    });



});




$(document).ready(function() {

    $('.vehicle-selection').children('label').click(function() {

        $('.vehicle-selection').children("label").removeClass("active");
        $('.vehicle-selection').children("label").find("input").removeAttr("checked");
        $(this).children("input").attr("checked", "checked");
        $(this).addClass("active");
    });


    $('.shop-selection').children('label').click(function() {

        $('.shop-selection').children("label").removeClass("active");
        $('.shop-selection').children("label").find("input").removeAttr("checked");
        $(this).children("input").attr("checked", "checked");
        $(this).addClass("active");
    });


    $('#open-notification').click(function() {

        $('.new-notification-panel').css("display", "block");
        $('.all-notification-panel').css("display", "none");
    });


    $('.category-selection').children('label').click(function() {

        $('.category-selection').children("label").removeClass("active");
        $('.category-selection').children("label").find("input").removeAttr("checked");
        $(this).children("input").attr("checked", "checked");
        $(this).addClass("active");
    });




});

$(document).ready(function() {


    $(".multiple_upload_form").on("change", ".file-upload-field", function() {
        $(this).parent(".file-upload-wrapper").attr("data-text", $(this).val().replace(/.*(\/|\\)/, ''));
    });


});

$(document).ready(function(){


 $('#opener-1').click(function(){

  $(this).children("svg").toggleClass("fa-angle-down");
  $(this).children("svg").toggleClass("fa-angle-right");
  $('#content-open1').slideToggle();

 })

})