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
				<legend>work's constraints</legend>
				<div class="manager">
					<form method="post" action="contraintesTravailEnseignant_.html">
				        <table>
				            <tr>
						        <td class="no_align"><label>Work's name</label></td>
						        <td class="no_align"><input type="text" name="nom" size="20" value="TD10_IPC"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Extra time</label></td>
						        <td class="no_align"><input type="text" name="jour" id="jour" size="2"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Size</label></td>
						        <td class="no_align"><input type="text" name="jour" id="jour" size="2"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Format</label></td>
						        <td class="no_align"><input type="text" name="format" id="format" size="20"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Minimum members</label></td>
						        <td class="no_align"><input type="text" name="grpMin" id="grpMin" size="2"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label>Maximum members</label></td>
						        <td class="no_align"><input type="text" name="grpMax" id="grpMax" size="2"/></td>
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
		        <a href="matiereEnseignant_.html"><img src="img/arrow_left.png"/>Back to previous</a>
			    <a href="index_.html"><img src="img/logout.png"/>Logout</a>
		    </p>
	    </div>
	    <div id="footer"><p>Polytech'WareHouse by Julien Lapalus, Karim Matrah, Oualid Merzouga &amp; St&eacute;phane Trepier | Polytech'Nice-Sophia 2009</p></div>
	</body>
</html>

