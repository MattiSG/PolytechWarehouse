
function resetForm(level) {
    var upload = document.getElementById("send");
    if (null != upload)
	upload.disabled = true;
    switch(level) {
    case 0:
	var promotion = document.getElementById("promotion");
	if (null != promotion)
	    promotion.options[0].selected = true;
    case 1:
	var courses = document.getElementById("course");
	if (null != courses) {
	    courses.length = 1;
	    courses.disabled = true;
	}
    case 2:
	var groups = document.getElementById("group");
	if (null != groups) {
	    groups.length = 1;
	    groups.disabled = true;
	}
    case 3: 
	var deliverables = document.getElementById("deliverable");
	if (null != deliverables) {
	    deliverables.length = 1;
	    deliverables.disabled = true;
	}
    case 4: 
	var products = document.getElementById("products");
	if (products != null)
	    products.textContent = "";
    case 5: 
	var result = document.getElementById("result");
	if (result != null)
	    result.textContent = "";
	break;
	
    }
}
 
function getCourses(){
    var promo = document.getElementById("promotion").value;
    resetForm(1);
    if ("" == promo)
	return ;
    var params = new Array();
    params[0] = promo;

    function callback(response) {
	var items = response.getElementsByTagName("item");
	var courses = document.getElementById("course");
	courses.disabled = false;
	for (i =0; i < items.length; i++){
	    var opt = document.createElement('option');
	    opt.text = items[i].textContent;
	    opt.value = items[i].attributes[0].textContent;
	    courses.add(opt,null);
	}
    }
    invoke(1,"Promotion","getCourses",params,callback); 
}

function getGroups() {
    var course = document.getElementById("course").value;
    resetForm(2);
    if ("" == course)
	return ;
    var params = new Array();
    params[0] = course;

    function callback(response) {
	var items = response.getElementsByTagName("item");
	var groups = document.getElementById("group");
	groups.disabled = false;
	for (i =0; i < items.length; i++){
	    var opt = document.createElement('option');
	    opt.text = items[i].textContent;
	    opt.value = items[i].attributes[0].textContent;
	    groups.add(opt,null);
	}
    }
    invoke(1,"Course","getGroups",params,callback); 
}

function getDeliverable() {
    var group = document.getElementById("group").value;
    resetForm(3);
    if ("" == group)
	return ;
    var course = document.getElementById("course").value;
    var params = new Array();
    params[0] = course;
    params[1] = group;

    function callback(response) {
	var items = response.getElementsByTagName("item");
	var deliverables = document.getElementById("deliverable");
	deliverables.disabled = false;
	for (i =0; i < items.length; i++){
	    var opt = document.createElement('option');
	    opt.text = items[i].textContent;
	    opt.value = items[i].attributes[0].textContent;
	    deliverables.add(opt,null);
	}
    }
    invoke(1,"Course","getDeliverables",params,callback); 
}

function getProducts() {
    var deliverable = document.getElementById("deliverable").value;
    resetForm(4);
    if ("" == deliverable)
	return ;
    var course = document.getElementById("course").value;
    var params = new Array();
    params[0] = course;
    params[1] = deliverable;

    function callback(response) {
	var items = response.getElementsByTagName("item");
	var products = document.getElementById("products");
	var elements = "";
	for (i =0; i < items.length; i++){
	    var li = document.createElement('li');
	    var input = document.createElement('input');
	    input.type="file";
	    input.id = items[i].attributes[0].textContent;
	    input.name = items[i].attributes[0].textContent;
	    li.textContent = items[i].textContent +  ": ";
	    li.appendChild(input);
	    products.appendChild(li);
	} 
	document.getElementById("send").disabled = false;
    }
    invoke(1,"Course","getProducts",params,callback); 
}


function getDeposits() {
    var group = document.getElementById("group").value;
    resetForm(5);
    if ("" == group)
	return ;
    var course = document.getElementById("course").value;
    var promo = document.getElementById("promotion").value;
    var params = new Array();
    params[0] = promo;
    params[1] = course;
    params[2] = group;

    function callback(response) {
	var div = document.getElementById("result"); 
	div.innerHTML = response;
    }
    invoke(1,"Delivery","getDeliverablesStatus",params,callback,true); 
}

function getPromoGroups(){
    var promo = document.getElementById("promotion").value;
    resetForm(1);
    if ("" == promo)
	return ;
    var params = new Array();
    params[0] = promo;

    function callback(response) {
	var items = response.getElementsByTagName("item");
	var courses = document.getElementById("group");
	courses.disabled = false;
	for (i =0; i < items.length; i++){
	    var opt = document.createElement('option');
	    opt.text = items[i].textContent;
	    opt.value = items[i].textContent;
	    courses.add(opt,null);
	}
    }
    invoke(1,"Promotion","getGroups",params,callback); 
}


function showGroupContent(){
   var group = document.getElementById("group").value;
    resetForm(5);
    if ("" == group)
	return ;
    var promo = document.getElementById("promotion").value;
    var params = new Array();
    params[0] = promo;
    params[2] = group;

    function callback(response) {
	var div = document.getElementById("result"); 
	div.innerHTML = response;
    }
    invoke(1,"Promotion","getGroupAsHTML",params,callback,true); 
}

function getGroupMail() {
    var group = document.getElementById("group").value;
    resetForm(5);
    if ("" == group)
	return ;
    var promo = document.getElementById("promotion").value;
    var params = new Array();
    params[0] = promo;
    params[2] = group;

    function callback(response) {
	var div = document.getElementById("result"); 
	var items = response.getElementsByTagName('item');
	var str = "";
	for(i = 0; i < items.length; i++) {
	    str = str + items[i].textContent + ",";
	}
	var a = document.createElement('a');
	a.href= "mailto:" + str;
	a.textContent = "Use this link in your favorite mailer agent";
	var p = document.createElement('p');
	p.appendChild(a);
	div.appendChild(p);
    }
    invoke(1,"Promotion","getGroupMembersMail",params,callback); 
}
