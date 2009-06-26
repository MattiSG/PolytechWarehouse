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
				<legend>new work</legend>
				<div class="manager">
					<form method="post" action="creationTravailEnseignant_.html">
				        <table>
					        <tr>
						        <td class="no_align"><label for="nom">Work's name</label></td>
						        <td class="no_align"><input type="text" name="nom" size="20"/></td>
					        </tr>
				        </table>
				        <table>
					        <tr>
						        <th>Name</th>
						        <th>S&eacute;lection</th>
						        <th>Teacher</th>
					        </tr>
					        <tr>
						        <td>SYLO1</td>
						        <td><input type="checkbox" name="sylo1" id="sylo1"/></td>
						        <td>
							        <select name="enseignant" id="enseignant">
								         <option value="enseignant3">blay</option>
							             <option value="enseignant3">bond</option>
							             <option value="enseignant2">colette</option>
							             <option value="enseignant3">franchi</option>
							             <option value="enseignant3">gallesio</option>
							             <option value="enseignant3">mosser</option>
							             <option value="enseignant3">pinna</option>
								         <option value="enseignant1">riveill</option>
								         <option value="enseignant1">rueher</option>
								         <option value="enseignant1">regin</option>
								         <option value="enseignant2">tigli</option>
							        </select>
						        </td>
					        </tr>
				            <tr>
						        <td>SYLO2</td>
						        <td><input type="checkbox" name="sylo2" id="sylo2"/></td><td>
							        <select name="enseignant" id="enseignant">
								         <option value="enseignant3">blay</option>
							             <option value="enseignant3">bond</option>
							             <option value="enseignant2">colette</option>
							             <option value="enseignant3">franchi</option>
							             <option value="enseignant3">gallesio</option>
							             <option value="enseignant3">mosser</option>
							             <option value="enseignant3">pinna</option>
								         <option value="enseignant1">riveill</option>
								         <option value="enseignant1">rueher</option>
								         <option value="enseignant1">regin</option>
								         <option value="enseignant2">tigli</option>
							        </select>
						        </td>				        
					        </tr>
					        <tr>
						        <td>VIM1</td>
						        <td><input type="checkbox" name="vim1" id="vim1"/></td>
						        <td>
							        <select name="enseignant" id="enseignant">
								         <option value="enseignant3">blay</option>
							             <option value="enseignant3">bond</option>
							             <option value="enseignant2">colette</option>
							             <option value="enseignant3">franchi</option>
							             <option value="enseignant3">gallesio</option>
							             <option value="enseignant3">mosser</option>
							             <option value="enseignant3">pinna</option>
								         <option value="enseignant1">riveill</option>
								         <option value="enseignant1">rueher</option>
								         <option value="enseignant1">regin</option>
								         <option value="enseignant2">tigli</option>
							        </select>
						        </td>
					        </tr>
					        <tr>
						        <td>VIM2</td>
						        <td><input type="checkbox" name="vim2" id="vim2"/></td>
						        <td>
							        <select name="enseignant" id="enseignant">
							             <option value="enseignant3">blay</option>
							             <option value="enseignant3">bond</option>
							             <option value="enseignant2">colette</option>
							             <option value="enseignant3">franchi</option>
							             <option value="enseignant3">gallesio</option>
							             <option value="enseignant3">mosser</option>
							             <option value="enseignant3">pinna</option>
								         <option value="enseignant1">riveill</option>
								         <option value="enseignant1">rueher</option>
								         <option value="enseignant1">regin</option>
								         <option value="enseignant2">tigli</option>
							        </select>
						        </td>
					        </tr>
					        <tr>
					            <td colspan="3"><input type="submit" id="creer" value="Create"/></td>
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

