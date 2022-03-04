$(document).ready(function() {
  
  var cible=$("li:contains('Freight charges')").closest("ul").find("li:last").wrapInner('<span class="valeur"></span>').closest("ul");
  $("li:contains('Freight charges')").text('Freight + insurance charges');
  $(cible).wrap($("#fraisdeportvariables form"));
  $('form.fraisdeportvariables span.edition').appendTo($(cible).find('li:last'));
  $('form.fraisdeportvariables').not(':first').remove();
  $('form.fraisdeportvariables span.edition').not(':first').remove();
  
  $('form.fraisdeportvariables a').click(function (){
    $(this).closest('span.edition').find('span.input').show();
	$(this).closest('form').find('span.valeur').hide();
    $(this).hide();
    return false;
  });
  
});  