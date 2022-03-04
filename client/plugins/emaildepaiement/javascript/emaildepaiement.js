$(document).ready(function() {
  
  var cible=$("li:contains('Payment method')").closest("ul").find("li:last").closest("ul");
  $('#emaildepaiement').appendTo($(cible).find('li:last'));
  $('#emaildepaiement').not(':first').remove();
  
});  