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
				<legend>deliveries</legend>
				<div class="manager">
					<form method="post">
				        <table>
					        <tr>
						        <th>Title</th>
						        <th>Action</th>
						        <th>Delete</th>
					        </tr>
                            <tr>
						        <td><a href="renduEnseignant.html"><img src="img/bullet_go.png"/>TD10_IPC_1</a></td>
						        <td><a href="contraintesRenduEnseignant.html"><img src="img/bullet_wrench.png"/>Settings</a></td>
						        <td><input type="checkbox" name="td10_ipc_1" id="td10_ipc_1"/></td>
					        </tr> 
					        <tr>
						        <td><a href="renduEnseignant.html"><img src="img/bullet_go.png"/>TD10_IPC_2</a></td>
						        <td><a href="contraintesRenduEnseignant__.html"><img src="img/bullet_wrench.png"/>Settings</a></td>
						        <td><input type="checkbox" name="td10_ipc_2" id="td10_ipc_2"/></td>
					        </tr>
					        <tr>
					            <td colspan="2"><input type="submit" id="supprimer" value="Delete"/></td>
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

