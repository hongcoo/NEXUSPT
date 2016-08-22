/*
* by moli
*/
$(document).ready(function(){
    setTimeout("jQuery.mxblur.interID = setInterval('jQuery.mxblur.begin()', 5)", 1500);
});

$.mxblur = {
    interID : null,
    num: 0.01,
    begin : function() {
        jQuery.mxblur.blur( jQuery.mxblur.num );
        jQuery.mxblur.num = jQuery.mxblur.num  + 1 /100;
    },
    blur : function() {
        $("body").css("-webkit-filter","blur("+$.mxblur.num+"px)");
        $("body").css("-moz-filter","blur("+$.mxblur.num+"px)");
        $("body").css("-o-filter","blur("+$.mxblur.num+"px)");
        $("body").css("-ms-filter","blur("+$.mxblur.num+"px)");
        $("body").css("filter","blur("+$.mxblur.num+"px)");
        $("body").css("filter","url(blur.svg#"+ $.mxblur.num.toFixed(1) +")");
    }
}