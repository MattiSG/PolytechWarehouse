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
                <div class="manager"><label for="ordre">Order</label>
					<select name="ordre" id="ordre">
						 <option value="renduProche">Due date increasing</option>
						 <option value="renduEloigne">Due date decreasing</option>
						 <option value="groupeProche">Group creation increasing</option>
						 <option value="groupeEloigne">Group creation decreasing</option>
					</select>
                <table>
					<tr>
						<th>Title</th>
						<th>Days left</th>
						<th>Group creation days left</th> 
						<th>Extra time left</th>
						<th>Notifications</th>
					</tr>
					<tr>
						<td><a href="renduEnseignant.html"><img src="img/bullet_go.png"/>TD10_IPC</a></td>
						<td>44</td>
						<td>13</td>
						<td>0</td>
						<td><a href=#>Deadline</a><a href=#>Group creation</a></td>
					</tr>
				</table>
			</div>
			</fieldset>
			<p id="links">
			    <a href="accueilEnseignant.html"><img src="img/home.png"/>Back to home</a>
				<a href="index_.html"><img src="img/logout.png"/>Logout</a>
			</p>
		</div>
		<div id="footer"><p>Polytech'WareHouse by Julien Lapalus, Karim Matrah, Oualid Merzouga &amp; St&eacute;phane Trepier | Polytech'Nice-Sophia 2009</p></div>
	</body>
</html>

