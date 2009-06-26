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
				<legend>new subject</legend>
				<div class="manager">
					<form method="post" action="creationMatiereEnseignant_.html">
				        <table>
					        <tr>
						        <td class="no_align"><label for="nom">Subject's name</label></td>
						        <td class="no_align"><input type="text" name="nom" size="20"/></td>
					        </tr>
				        </table>
				        <table>
					        <tr>
						        <th>Name</th>
						        <th>S&eacute;lection</th>
					        </tr>
					        <tr>
						        <td>SYLO1</td>
						        <td><input type="checkbox" name="sylo1" id="sylo1"/></td>
					        </tr>
				         <tr>
						        <td>SYLO2</td>
						        <td><input type="checkbox" name="sylo2" id="sylo2"/></td>
					        </tr>
					        <tr>
						        <td>VIM1</td>
						        <td><input type="checkbox" name="vim1" id="vim1"/></td>
					        </tr>
					        <tr>
						        <td>VIM2</td>
						        <td><input type="checkbox" name="vim2" id="vim2"/></td>
					        </tr>
					        <tr>
					            <td colspan="2"><input type="submit" id="creer" value="Create"/></td>
					        </tr>
				        </table>
				   </form>
				</div>
			</fieldset>
			<p id="links">
			    <a href="accueilEnseignant.html"><img src="img/home.png"/>Back to home</a>
			    <a href="listeMatieresEnseignant_.html"><img src="img/arrow_left.png"/>Back to previous</a>
				<a href="index_.html"><img src="img/logout.png"/>Logout</a>
			</p>
		</div>
		<div id="footer"><p>Polytech'WareHouse by Julien Lapalus, Karim Matrah, Oualid Merzouga &amp; St&eacute;phane Trepier | Polytech'Nice-Sophia 2009</p></div>
	</body>
</html>

