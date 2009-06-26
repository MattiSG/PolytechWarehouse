
function resetForm(level) {
    function resetElem(id) {
	Try.these(function(){ $(id).length = 1; $(id).disable(); },
		  function(){ $(id).update(); }); 	
    }
    var upload = $("send");
    if (null != upload)
	upload.disable();

    switch(level) {
    case 0:
	$("promotion").options[0].selected = true;
    case 1:
	resetElem("course");
    case 2:
	resetElem("group");
    case 3: 
	resetElem("deliverable");
    case 4: 
	resetElem("products");
    case 5: 
	resetElem("result");
    }
}

function genericUpdate(source,level,rClass,rMethod,opt,callback) {
    var source = $F(source);
    resetForm(level);
    if ("" == source) {	return; }
    var params = new Array();
    params[0] = source; 
    opt.each(function(e) { params.push(e); });
    invoke(rClass,rMethod,params,callback); 
}

function updateSelect(source,target,level,remoteClass,remoteMethod) {
    genericUpdate(source,level,remoteClass,remoteMethod,new Array(),
		  function (r) { selectCallback(r,target);});
}

function getCourses() {
    updateSelect("promotion","course",1,"Promotion","getCourses");
}

function getGroups() {
    updateSelect("course","group",2,"Course","getGroups");
}

function getDeliverables() {
    var opt = new Array();
    opt.push($F("course"));
    genericUpdate("group",3,"Course","getDeliverables",opt,
		  function (r) { selectCallback(r,"deliverable");});
}
var getDeliverable = getDeliverables; // TO DO: FIXME

function getProducts() {
    function callback(response) {
	var ans = response.responseXML;
	var items = $A(ans.getElementsByTagName("item"));
	$(items).each(function(i) {
	    var li = document.createElement('li');
	    var input = document.createElement('input');
	    input.type = "file";
	    input.id = getTextContent(i.attributes[0]);
	    input.name = getTextContent(i.attributes[0]);
	    $(li).update(getTextContent(i.attributes[1]) +  ": ");
	    li.appendChild(input);
	    $("products").appendChild(li);
	});
	$("send").enable();
    }
    var opt = new Array();
    opt.push($F("deliverable"));
    genericUpdate("course",4,"Course","getProducts",opt,callback);
}

function getDeposits() {
    var opt = new Array();
    opt.push($F("course"));
    opt.push($F("promotion"));
    genericUpdate("group",5,"Delivery","getDeliverablesStatus",opt,
		  function(r) { divCallback(r,"result"); });
}

function getPromoGroups() {
    updateSelect("promotion","group",1,"Promotion","getGroups");
}

function showGroupContent() {
    var opt = new Array();
    opt.push($F("promotion"));
    genericUpdate("group",5,"Promotion","getGroupAsHTML",opt,
		  function(r) { divCallback(r,"result"); });
}

function getGroupMail() {
    var opt = new Array();
    opt.push($F("promotion"));

    function callback(response) {
	var ans = response.responseXML;
	var items = $A(ans.getElementsByTagName("item"));
	var str = "";
	$(items).each(function(i) {
	    str = str + getTextContent(i.attributes[1]) + ",";
	});
	var a = document.createElement('a');
	a.href = "mailto:" + str;
	$(a).update("Utilisez ce lien dans votre client mail préféré.");
	var p = document.createElement('p');
	$(p).appendChild(a);
	$("result").appendChild(p);
    }
    genericUpdate("group",5,"Promotion","getGroupMembersMail",opt,callback);
}

function getSubmitButton() {
    var deliverable = $F("deliverable");
    resetForm(4);
    if ("" == deliverable)
	return ;
    $("send").enable();
}

function getCourseDescription() {
    genericUpdate("course",3,"Course","getCourseDescription",new Array(),
		  function(x) { divCallback(x,"result"); });
}