<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"> 
	<title>Market 43 - Item</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

<?php
	include('navbar.php');
	echo create_navbar('item.php');

	include('database_connect.php');
	session_start() or die('Failed to create session');
	$userid=$_SESSION['userid'];
	!empty($userid) or die('Session lacks userid!');

	if (!empty($_GET)) {
		if (isset($_GET['typeid'])) {
			$itemtype = mysql_real_escape_string($_GET['typeid']);

			$query = "SELECT DISTINCT(i.ItemId)
				FROM item AS i
				INNER JOIN item_type AS t ON i.ItemType = '$itemtype'
				INNER JOIN user ON i.OwnerUserId = '$userid'
				WHERE i.ItemId NOT IN (
					SELECT m.ItemId FROM item AS m
					INNER JOIN listing AS l ON l.ListedItemId = m.ItemId
					WHERE l.ExpiryTimestamp > CURRENT_TIMESTAMP
				);";
			$result = mysql_query($query) or die (mysql_error());
			$rows = mysql_numrows($result);

			# find a nice description of the item
			$query = "SELECT t.Name, t.IconPath, t.Description
				FROM item as i 
				INNER JOIN item_type AS t ON i.ItemType = t.ItemTypeId
				WHERE '$itemtype' = i.ItemType";
			$result = mysql_query($query) or die (mysql_error());
			(mysql_numrows($result) == 1) or die('Unexpected data!');
			$name = mysql_result($result, 0, 't.Name');
			$icon = mysql_result($result, 0, 't.IconPath');
			$description = mysql_result($result, 0, 't.Description');

			echo "<H1><img class=\"item-icon\" src=\"$icon\">$name</H1>";
			echo "<em>$description</em>";
			echo "<br><em>Number available: $rows<br>";
		}
	}

	if (!empty($_POST)) {

	}
?>

<H1>Crafting Table</H1>
<table class="item-table">
	<colgroup>
		<col style="width: 15%" />
		<col style="width: 35%" />
		<col style="width: 35%" />
		<col style="width: 15%" />
	</colgroup>
	<tr>
		<th>craft</th>
		<th>item</th>
		<th>description</th>
	</tr>

<?php 
	$query = "SELECT R.OutputItemType, R.InputItemCount, S.Name, S.IconPath, S.Description
		FROM recipe AS R, item_type AS T, item_type AS S
		WHERE T.ItemTypeId = '$itemtype'
		AND R.InputItemType = T.ItemTypeId
		AND R.OutputItemType = S.ItemTypeId;";

	$result = mysql_query($query) or die (mysql_error());
	$rows = mysql_numrows($result);
	$i=0; while ($i < $rows) { 
		$name=mysql_result($result, $i, "S.Name");
		$icon=mysql_result($result, $i, "S.IconPath");
		$description=mysql_result($result, $i, "S.Description");
		echo "<tr>
			<td></td>
			<td><img class=\"item-icon\" src=\"$icon\">$name</td>
			<td>$description</td>
			<td></td>
		</tr>";
		$i++;
	}
 ?>

</table>
</body>
</html>
