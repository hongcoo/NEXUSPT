/*var fix=false;
$(window).scroll(function () { 
      if($(window).scrollTop()+$(window).innerHeight()>=2100-100){
      	if(!fix){
      	$('body').addClass( 'fix' );
      	fix=true;
      	}
      }else if(fix){
      	$('body').removeClass( 'fix' );
      	fix=false;
      }
});
*/

var fix=false;
$(window).scroll(function () { 
      if($(window).scrollTop()+$(window).innerHeight()>=2100-100){
      	if(!fix){
      	$('#toppic').addClass( 'candoit' );
      	fix=true;
      	}
      }else if(fix){
      	$('#toppic').removeClass( 'candoit' );
      	fix=false;
      }
});