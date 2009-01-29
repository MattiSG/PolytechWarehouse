/**
  */
var serverUrl = "paf.php"

function createXmlQuery(className, methodName, parameters) {
    className  = escapeXMLContent(className);
    methodName = escapeXMLContent(methodName);
    var query = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
    query += "<query>\n";
    query += "  <className>"  + className  + "</className>\n";
    query += "  <methodName>" + methodName + "</methodName>\n",
    query += "  <params>\n";
    parameters.each(function (p) { 
	query += "    <param>" + p + "</param>\n";});
    query += "  </params>\n";
    query += "</query>\n";
    return query;
}

function invoke(className, methodName, parameters, callback) {
    var xmlQuery = createXmlQuery(className, methodName, parameters);
    var cb = function(x) { try { callback(x); } catch(e) { alert(e); }};
    new Ajax.Request(serverUrl, 
		     {parameters: {request: xmlQuery}, onSuccess: cb,
		      onFailure: function(response){ alert("Network Error");}});
}
		      

function escapeXMLContent(aString) {
    var tmp = String(aString);
    tmp = tmp.replace(/&/gi,"&amp;"); // &
    tmp = tmp.replace(/</gi,"&lt;");  // <
    tmp = tmp.replace(/>/gi,"&gt;");  // >
    tmp = tmp.replace(/"/gi,"&#34;"); //"
    tmp = tmp.replace(/'/gi,"&#39;"); //'
    return tmp;	
}


function getTextContent(elem) {
	//for(var p in elem)
	//	alert(p);
	if (document.getElementsByTagName('body')[0].innerText)
		return elem.value;
	else
		return elem.textContent
}
function selectCallback (response,target) {
    var ans = response.responseXML;
    var items = $A(ans.getElementsByTagName("item"));
    $(items).each(function(i) {
		var opt = document.createElement('option');
		//alert(getTextContent(i) + " " + getTextContent(i.attributes[0]));
		$(opt).update(getTextContent(i.attributes[1]));
		$(opt).writeAttribute("value",getTextContent(i.attributes[0]));
		$(target).appendChild(opt);
    });
    $(target).enable();
}


function divCallback(response,target) {
    $(target).innerHTML = response.responseText;
}