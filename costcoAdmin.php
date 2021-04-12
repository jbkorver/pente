<?php
include('helper.inc.php');
include('../helper.inc.php');
include('javascript.helper.php');
include('../../data/db_connect.php');

$device = strtolower($_SERVER["HTTP_USER_AGENT"]);

$css = costco.css;
$mobile = false;
if (strpos($device, 'iphone') !== false) {
    $css = "mobile.css";
	$mobile = true;
} else if (strpos($device, 'android') !== false) {
    $css = "mobile.css";
	$mobile = true;
} 


function getLocationsList($showall = true) { 
	$locations = array();
	$query = "SELECT * FROM `roons_costco_locations` WHERE";
	if ($showall != true) {
		$query .= " `show` = 1 AND ";
	}
	$query .= "`deleted` = 0 ORDER BY `name` ASC";
//	echo $query;
	$result = mysql_query($query);
	if ($result) {
		for ($b=0; $b<mysql_numrows($result); $b++) {
			$item = array();
			$item['id'] = mydecode(mysql_result($result, $b,"id"));
			$item['name'] = mysql_result($result, $b,"name");
			$item['show'] = mysql_result($result, $b,"show");
			$item['additional_time'] = mysql_result($result, $b,"additional_time");
			
			$locations[$item['id']] = $item;
		}
	}
	return $locations;
}

$id = 0;
if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
}

$sheet = doGetWorksheet($id);
$location_id = $sheet['location_id'];
$time = strtotime($sheet['date']);

$doShowTableOfWorksheets = false;
if ($id == 0) {
	$doShowTableOfWorksheets = true;
} else {
	$mdyFormattedDate = date("m/d/Y", $time);
	$dayOfTheWeek = date('l',$time);

	$flavors = getAvailableFlavorsAndPacks($time); // individual flavors available at the time of this roadshow

	$locationList = getLocationsList(false);
	$allCurrentEmployees = getEmployeesList(false);
	$largeSuitcaseItems = getLargeSuitcaseItems();

	$roadshowLocation = $locationList[$location_id]['name'];
} 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php if($mobile){ ?>
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
<?php } ?>

<link href="<?=$css?>" rel="stylesheet" type="text/css" />
<title>Costco Road Show Admin</title>
</head>

<body class="default">
	<input type="hidden" id="roadshow_id" value="<?=$id?>">
	<input type="hidden" id="location_id" value="<?=$sheet['location_id']?>">
	<input type="hidden" id="date" value="<?=$mdyFormattedDate?>">
	
	<div id="content">
		<div id="header">
			<div style="font-style:bold;">Admin: Costco Road Show Sales</div>
			<? if (!$doShowTableOfWorksheets) { ?>
			
			<div style='clear:both;margin:5px 0 0 80px;'><?=$roadshowLocation?></div>
			<div style='clear:both;margin:5px 0 40px 80px;font-size:15px'><?=$dayOfTheWeek?>, <?=$mdyFormattedDate?></div>
			<? } ?>
		</div>
		<div id="worksheetsAvailable" style="<?php if ($doShowTableOfWorksheets) { ?> display:block; <? } else { ?> display:none; <? } ?>">
			<div class="table" style="width:580px;">
				<div class="tableheader">
					<div class="tc_location tc">Location</div>
					<div class="tc_date tc">Day</div>
					<div class="tc_date tc">Date</div>
				</div>

		<?	$limit = 30;
			$roadshowList = doGetRoadShow($limit);
			foreach ($roadshowList as $roadshow) {
				$temp_rid = $roadshow['id'];
				$time = strtotime($roadshow['date']);
				$dayOfTheWeek = date('l',$time);
				$name = $roadshow['name'];
				if ($roadshow['name'] == "") {
					$name = "<span class='messageText'>No location choosen</span>";
				}

				$time = strtotime($roadshow['date']);
				$date = date("m/d/Y", $time); ?>
				<div class="worksheetRow" >
					<div class="tc_location tc"><?=$name?></div>
					<div class="tc_date tc"><?=$dayOfTheWeek?></div>
					<div class="tc_date tc"><?=$date?></div>
					<div class="tc_icons tc" title="open" onclick="doOpenAdmin(<?=$temp_rid?>);">
						<img style="width:24px;" src="../img/open.png" title="Open Worksheet">
					</div>
				</div>
		<?	} ?>

			</div>
		</div> <!-- worksheetTable -->
		
		
		<? if (!$doShowTableOfWorksheets) { ?>
	
			<div id="adminContents">
			
				<div id="needMoreBox" style="margin:0 0 40px 0;font-size:18px;">
					Need More
					
					<? 
					foreach ($largeSuitcaseItems as $item) { 
						$suitcaseInventory = getLargeSuitcaseInventory($id, $item['id']);
						if ($suitcaseInventory['need'] == 1) { ?>
							<div style="margin:10px 0 0 20px;font-size:15px;">
								<span style="font-weight:bold;margin:0 10px 0 0;"><?=$item['name']?></span> - 
								<span style="font-style:italic"><?=$suitcaseInventory['ending_count']?> remaining</span>
							</div>
						<? } ?>
					<? } ?>
				</div>
				<div id="formBox">
					<div style="font-size:18px;margin:0 0 10px 0;">Additional Expenses</div>
					<div class="formLine">
						<label>Fees:</label>
						<input type="text" id="fees" style="width:60px;" 
							value="<?=$sheet['fees']?>" onchange="doSaveRoadshowItem(this);">
					</div>
					<div class="formLine">	
						<label>Variable Costs:</label>
						<input id="variable_costs" type="text" style="width:60px;" 
							value="<?=$sheet['variable_costs']?>" onchange="doSaveRoadshowItem(this);">
					</div>
					<div class="formLine">	
						<label>Random Expenses:</label>
						<input id="random_expenses" type="text" style="width:60px;" 
							value="<?=$sheet['random_expenses']?>" onchange="doSaveRoadshowItem(this);">
					</div>
					<div class="formLine">	
						<label>Revenue:</label>
						<input id="revenue" type="text" style="width:74px;" 
							value="<?=$sheet['revenue']?>" onchange="doSaveRoadshowItem(this);">
					</div>
				</div>
				
				
				<div style="margin:40px 0 20px 0;font-size:18px;clear:both;">Labor</div>
				
				<div id="laborTable" class="table" style="width:835px;">
				</div><!-- end labor table -->
				
				<button style="margin:30px 0 30px 0;" onclick="doBuildTotalsTable();">Calculate Profits</button>
				<div id="TotalsTableDiv">
				
				</div>
				
				
				
			</div>
		
	<? } ?>
		
	</div> <!-- end content -->
	
	<div id="footer">
		<div style="float:left;font-size:14px;margin:20px 0 0px 20px;">Look at 
			<a href="http://jubon.biz/rickaroons/costco/worksheet.php?id=<?=$id?>" style="text-decoration:none;" target="_blank">worksheet</a> used to generate this page.</div>
		<img src="../img/poweredby.png" style="margin-top:-20px;padding:0 0 10px 0">
	</div>
	
</body>

<script>
	doBuildLaborTable();
</script>
