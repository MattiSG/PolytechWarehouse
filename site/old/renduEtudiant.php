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
			    <li><a href="listeRendusEtudiant.html">Deliveries</a></li>
			    <li><a href=#>Export</a></li>
			</ul>
		</div>
		<div id="content">
			<fieldset>
				<legend>description and constraints</legend>
				<div class="text">
				    <h4>Description</h4>
				    <p>Programing with Unix IPC. Use semaphores to make two programs that communicate through a shared memory</p>
				</div>
				<div class="manager">
					<table>
					    <tr>
					        <th class="no_align">Constraints</th>
					        <th class="no_align">Values</th>
					    </tr>
					    <tr>
					        <td class="no_align">Due date</td>
					        <td class="no_align">13-03-2009</td>
					    </tr>
					    <tr>
					        <td class="no_align">Extra time</td>
					        <td class="no_align">2</td>
					   </tr>
					    <tr>
					        <td class="no_align">Group creation deadline</td>
					        <td class="no_align">06-03-2009</td>
					    </tr>
					    <tr>
					        <td class="no_align">Minimum members</td>
					        <td class="no_align">2</td>
					    </tr>
					    <tr>
					        <td class="no_align">Maximum members</td>
					        <td class="no_align">2</td>
					   </tr>
					   <tr>
					        <td class="no_align">Size</td>
					        <td class="no_align">1Mo</td>
					   </tr>
					   <tr>
					    <td class="no_align">Format</td>
					    <td class="no_align">*.zip</td>
					   </tr>
				    </table>
				</div>
			</fieldset>
			<fieldset>
				<legend>actions</legend>
				<div class="list">
				    <ul>
				        <li><a href="composerGroupeEtudiant.html"><img src="img/group_add.png"/>Create a delivery group</a></li>
				        <li><a href="groupeEtudiant.html"><img src="img/group_go.png"/>See group's informations</a></li>
				   </ul>		   
				<div class="text">
				    <h4>Delivery</h4>
				    <form method="post" enctype="multipart/form-data">
					    <input type="file" name="fichier"/>
				        <input type="submit" value="Deliver"/>
				    </form>
			    </div>
			</fieldset>
			<p id="links">
			    <a href="accueilEtudiant.html"><img src="img/home.png"/>Back to home</a>
				<a href="index.html"><img src="img/logout.png"/>Logout</a>
			</p>
		</div>
		<div id="footer"><p>Polytech'WareHouse by Julien Lapalus, Karim Matrah, Oualid Merzouga &amp; St&eacute;phane Trepier | Polytech'Nice-Sophia 2009</p></div>
	</body>
</html>

