
function getCourses(){
    var promo = document.getElementById("promotion").value;
    if ("" == promo)
	return;
    var params = new Array();
    params[0] = promo;

    function callback(response) {
	alert(response);
    }
    invoke(1,"Promotions","getCourses",params,callback); 
}