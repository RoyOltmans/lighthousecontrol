//<![CDATA[ 
$(window).load(function(){
$(document).ready(function () {

    $('form').each(function () {
        $(this).validate({
            submitHandler: function (form) { // for demo
                alert('valid form');
                return false;
            }
        });
    });

});
});//]]>  