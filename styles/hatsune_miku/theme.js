var fix=false;
$(function(){if(jQuery(document).height()>2248)$('#toppic').addClass( 'candoit' )})
$(window).scroll(function () { 
      if(jQuery(document).height()>2248){
      	if(!fix){
      	$('#toppic').addClass( 'candoit' );
      	fix=true;
      	}
      }else if(fix){
      	$('#toppic').removeClass( 'candoit' );
      	fix=false;
      }
});