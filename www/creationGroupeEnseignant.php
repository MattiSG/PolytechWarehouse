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
				<legend>new group</legend>
				<div class="manager">
					<form method="post">
				        <table>
					        <tr>
						        <td class="no_align"><label for="nom">Group's name</label></td>
						        <td class="no_align"><input type="text" name="nom" size="20"/></td>
					        </tr>
					        <tr>
						        <td class="no_align"><label for="groupe">Group</label></td>
						        <td class="no_align">
							        <select name="groupe" id="groupe">
							            <option value="SI4">SI4</option>
								         <option value="SYLO1">SYLO1</option>
								         <option value="SYLO2">SYLO2</option>
								         <option value="VIM1">VIM1</option>
								         <option value="VIM2">VIM2</option>
							        </select>
						        </td>
					        </tr>
				        </table>
				        <table>
					        <tr>
						        <th>Name</th>
						        <th>S&eacute;lection</th>
					        </tr>
						    <tr>
						        <td>arnaout</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>arnoux</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>batard</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>benabu</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
				            <tr>
						        <td>bouzdou</td>
						        <td><input type="checkbox" name="etudiant2" id="etudiant2"/></td>
					        </tr>
					        <tr>
						        <td>colombie</td>
						        <td><input type="checkbox" name="etudiant3" id="etudiant3"/></td>
					        </tr>
					        <tr>
						        <td>falavel</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>gentile</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>lapalus</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>lequeux</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>ligavant</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>merzouga</td>
						        <td><input type="checkbox" name="etudiant3" id="etudiant3"/></td>
					        </tr>
					        <tr>
						        <td>nerriere</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>pascal</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>perrulli</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
						        <td>trepier</td>
						        <td><input type="checkbox" name="etudiant3" id="etudiant3"/></td>
					        </tr>
					        <tr>
						        <td>trichet</td>
						        <td><input type="checkbox" name="etudiant1" id="etudiant1"/></td>
					        </tr>
					        <tr>
					            <td colspan="2"><input type="submit" id="creer" value="Create"/></td>
					        </tr>
				        </table>
				   </form>
				</div>
			</fieldset>
			<p id="links">
			    <a href="accueilEnseignant.html"><img src="img/home.png"/>Back to homme</a>
				<a href="index_.html"><img src="img/logout.png"/>Logout</a>
			</p>
		</div>
		<div id="footer"><p>Polytech'WareHouse by Julien Lapalus, Karim Matrah, Oualid Merzouga &amp; St&eacute;phane Trepier | Polytech'Nice-Sophia 2009</p></div>
	</body>
</html>

