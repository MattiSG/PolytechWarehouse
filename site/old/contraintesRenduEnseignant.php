<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Polytech'WareHouse</title>
		<link rel="stylesheet" type="text/css" href="pwh.css"/>
	</head>
	<body>
		<div id="header"></div>
		<div id="menu">
			<ul>
			    <li><a href="listeMatieresEnseignant.html">Subjects</a></li>
			    <li><a href="listeGroupesEnseignant.html">Groups</a></li>
			</ul>
		</div>
		<div id="content">
			<fieldset>
				<legend>delivery's constraints</legend>
				<div class="manager">
					<form method="post" action="contraintesRenduEnseignant_.html">
				        <table>
					        <tr>
						        <td class="no_align"><label>Due date</label></td>
						        <td class="no_align">
						            <input type="text" name="jourRendu" id="jourRendu" size="2"/>
						            <input type="text" name="moisRendu" id="moisRendu" size="2"/>
						            <input type="text" name="anneeRendu" id="anneeRendu" size="4"/>
					            </td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Group creation deadline</label></td>
						        <td class="no_align">
						            <input type="text" name="jourGrp" id="jourGrp" size="2"/>
						            <input type="text" name="moisGrp" id="moisGrp" size="2"/>
						            <input type="text" name="anneeGrp" id="anneeGrp" size="4"/>
					            </td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Extra time</label></td>
						        <td class="no_align"><input type="text" name="jour" id="jour" size="2" value="2" disabled="disabled"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Size</label></td>
						        <td class="no_align"><input type="text" name="jour" id="jour" size="2" value="1" disabled="disabled"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Format</label></td>
						        <td class="no_align"><input type="text" name="format" id="format" size="20" value="*.zip" disabled="disabled"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Minimum members</label></td>
						        <td class="no_align"><input type="text" name="grpMin" id="grpMin" size="2" value="2" disabled="disabled"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Maximum members</label></td>
						        <td class="no_align"><input type="text" name="grpMax" id="grpMax" size="2" value="2" disabled="disabled"/></td>
					        </tr>
					        <tr>
					            <td colspan="2"><input type="submit" id="valid" value="Valid"/></td>
					        </tr>
				        </table>
			        </form>
			    </div>
	        </fieldset>
		    <p id="links">
		        <a href="accueilEnseignant.html"><img src="img/home.png"/>Back to home</a>
		        <a href="listeRendusEnseignant.html"><img src="img/arrow_left.png">Back to previous</a>
			    <a href="index_.html"><img src="img/logout.png"/>Logout</a>
		    </p>
	    </div>
	    <div id="footer"><p>Polytech'WareHouse by Julien Lapalus, Karim Matrah, Oualid Merzouga &amp; St&eacute;phane Trepier | Polytech'Nice-Sophia 2009</p></div>
	</body>
</html>

