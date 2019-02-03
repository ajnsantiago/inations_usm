$(document).ready(function () {
   $(".input-search").keyup(function(){
      //takes table css
      var table = $(this).attr('alt');
      if( $(this).val() != ""){
         $("#"+table+" tbody>tr").hide();
         $("#"+table+" td:contains-ci('" + $(this).val() + "')").parent("tr").show();
      } else{
         $("#"+table+" tbody>tr").show();
      }
   });

   $.extend($.expr[":"], {
      "contains-ci": function(elem, i, match, array) {
         return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
      }
   });

});
