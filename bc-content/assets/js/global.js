$(document).ready(function() {
 $("#notification").fadeIn("slow");
});
function close_notice() {
 $("#notification").slideUp("slow");
}
$(".close-message").click(function(){
 $(this).parent('.msg').slideUp("slow");
});
