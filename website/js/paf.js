/**
  */
var serverUrl = "paf.php"

var invokers = new Array();


function createXmlQuery(className, methodName, parameters) {
    className  = escapeXMLContent(className);
    methodName = escapeXMLContent(methodName);
    var query = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
    query += "<query>\n";
    query += "  <className>"  + className  + "</className>\n";
    query += "  <methodName>" + methodName + "</methodName>\n",
    query += "  <params>\n";
    parameters.each(function (p) { query += "    <param>" + parameters[p] + "</param>\n";});
    query += "  </params>\n";
    query += "</query>\n";
    return query;
}

function invoke(className, methodName, parameters, callBackFunction,html) {
	var query = createXmlQuery(className, methodName, parameters);
	var callback = function(response) {
		
	}
	new Ajax.Request(serverUrl,{parameters: 'query='+query}, onSuccess: callback});
	
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