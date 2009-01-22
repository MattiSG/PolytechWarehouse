/**
  */
var serverUrl = "paf.php"

var invokers = new Array();

function httpFactory() {
    var invoker = false;
    if (window.XMLHttpRequest)      // Firefox 
	invoker = new XMLHttpRequest(); 
    else if(window.ActiveXObject) { // Internet Explorer 
	invoker = new ActiveXObject("Microsoft.XMLHTTP");
    }
    else {
	var fail = "you should use a real browser guy !";
	alert(fail); 
	exit(); 
    }
    return invoker;
}

function createXmlQuery(className, methodName, parameters) {
    className  = escapeXMLContent(className);
    methodName = escapeXMLContent(methodName);
    for (p in parameters)
	parameters[p] = escapeXMLContent(parameters[p]);
    var query = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
    query += "<query>\n";
    query += "  <className>"  + className  + "</className>\n";
    query += "  <methodName>" + methodName + "</methodName>\n",
    query += "  <params>\n";
    for (p in parameters)
	query += "    <param>" + parameters[p] + "</param>\n";
    query += "  </params>\n"
    query += "</query>\n"
    return query;
}

function invoke(id,className, methodName, parameters, callBackFunction) {
    invokers[id] = false;
    invokers[id] = httpFactory();
    var xmlQuery = createXmlQuery(className, methodName, parameters);	
    invokers[id].onreadystatechange = function() {
	if ( 4 == invokers[id].readyState) {
	    if (invokers[id].status == 200) 
	      	callBackFunction(invokers[id].responseXML);
	    else
		alert("HTTP Error : " + invokers[id].status);	    
	}
    } 	
    invokers[id].open("POST",serverUrl, true);	
    invokers[id].setRequestHeader("Content-type", 
				  "application/x-www-form-urlencoded");
    invokers[id].setRequestHeader("Charset", "utf-8");
    var query = "request="+escape(xmlQuery);
    invokers[id].send(query);
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