$(document).ready(function()
{
    $('fieldset').not(':first').hide();
    $('.next-section').on('click', function(e)
    {
        e.preventDefault();
        target = $(this).attr('href');
        $('fieldset').hide();
        $(target).fadeIn();
    });
});

$(function() {
    $( "#sortable" ).sortable({
     containment: "parent"
    });
});
$(document).ready(function(){

$('.fields').on('click', '.delete_row', function(e){

  e.preventDefault();
  $(this).parent().remove();
  if ($('.fields div').length < 1){
   $('#new').val('create first row');
  }
});

var $clone = $(".fields div:eq(0)").clone().html();
var $button_txt = $('#new').val();
count = 1;
$('#new').on('click', function(e){
  e.preventDefault();
  if ($('#new').val() != $button_txt){
   $('#new').val($button_txt);
  }
  var $counter = count++;
  var $new_row = $clone.replace(/row_new_0/g,'row_new_'+$counter);

  $(".fields").append('<div class="field">'+$new_row+'</div>');
});

});

  $(function() {
    $( "#datepicker" ).datepicker();
  });