
function resetForm(level) {
    var empty = document.createElement('option');
    empty.value="";
    empty.text="---";
    switch(level) {
    case 0:
	var promotion = document.getElementById("promotion");
	promotion.options[0].selected = true;
    case 1:
	var courses = document.getElementById("course");
	courses.length = 1;
	courses.disabled = true;
    case 2:
	var groups = document.getElementById("group");
	groups.length = 1;
	groups.disabled = true;
    case 3: 
	var deliverables = document.getElementById("deliverable");
	deliverables.length = 1;
	deliverables.disabled = true;
    case 4: 
	var products = document.getElementById("products");
	products.textContent = "";
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
	    opt.value = items[i].textContent;
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