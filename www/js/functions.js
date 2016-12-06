function validateForm() {
    var a=document.forms["Form"]["reg[login]"].value;
    var b=document.forms["Form"]["reg[jmeno]"].value;
    var c=document.forms["Form"]["reg[pass1]"].value;
    var d=document.forms["Form"]["reg[pass2]"].value;
    var e=document.forms["Form"]["reg[email]"].value;
    if (a==null || a=="",b==null || b=="",c==null || c=="",d==null || d=="",e==null || e=="") {
        document.getElementById('fill1').style.display="block";
        return false;
    }

    if(c != d) {
        document.getElementById('fill2').style.display="block";
        return false;
    }
}