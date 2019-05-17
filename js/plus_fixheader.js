window.onscroll=function(){
    var scrollTop = window.pageYOffset  
                || document.documentElement.scrollTop  
                || document.body.scrollTop  
                || 0;
    var shadow=document.getElementById("header_shadow");
    var headbg=document.getElementById("titlebar");
    if (scrollTop == 0) {
        shadow.style.opacity=0;
    } else {
        shadow.style.opacity=0.4;
    }
}
