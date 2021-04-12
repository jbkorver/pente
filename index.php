<?php

//include('db_connect.php');

$boardSize = 17;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="main.css" rel="stylesheet" type="text/css" />
<title>Pente</title>
<script>
var boardSize = 17;

adjRow = [-1, 0, 1];
adjColumn = [-1, 0, 1];
captureCount = [ ];
var board = [ ];
var undoAvailable = false;
	
for(var i = 0; i < boardSize; i++) {
    board[i] = [ ];
    for(var j = 0; j < boardSize; j++) {
        board[i][j] = 0;     }
}

function doClearBoard() {
	console.log("doClearBoard: ");
	undoAvailable = false;
	var divId;
	for (var i=0; i<boardSize; i++) {
		for (var j=0; j<boardSize; j++) {
			board[i][j] = 0;
			divId = i+"_"+j;
			document.getElementById(divId).style.visibility = "hidden";
		}
	}
	captureCount[1] = 0;
	captureCount[2] = 0;
	document.getElementById("winner").style.display = "none";
	document.getElementById("winner").style.color = "#000000";
	clearCaptures();
	
	for (i=2;i<15;i=i+3) {
		for (j=2;j<15;j=j+3) {
			divId = "d"+i+"_"+j;
			document.getElementById(divId).style.display = "block";
		}
	}
	document.getElementById("WhoseUpMarble").style.backgroundColor = document.getElementById("player1Color").value;
}

function doPlaceOnBoard(r,c, undo) {

	var player = document.getElementById("whoseUp").value;
	if (undo && (player == 1)) {
		player = 2;
		nextPlayer =  2;
	} else if (undo && (player == 2)) {
		player = 1;
		nextPlayer = 1;
	} else if (player == 1) {
		nextPlayer = 2;
	} else if (player = 2) {
		nextPlayer = 1;
	}
	
	var playerColorDiv = "player"+player+"Color";
	var nextPlayerColorDiv = "player"+nextPlayer+"Color";

	var playerColor = document.getElementById(playerColorDiv).value;
	var nextPlayerColor = document.getElementById(nextPlayerColorDiv).value;
	
	if (undo) {
		playerColor = document.getElementById("lastMoveColor").value;
	}
	
	var marbleDiv = r+"_"+c;
	
	if (board[r][c] == 0) { // empty location, continue
		board[r][c] = player;
		
		if (!undo)
			undoAvailable = true;
		document.getElementById(marbleDiv).style.backgroundColor = playerColor;
		document.getElementById(marbleDiv).style.visibility = "visible";
		console.log("set "+marbleDiv+" to visible");
		if (capture(r,c)) {
			if (captureCount[player] >= 5) {
				declareWinner(player, playerColor);
			} 
		} else if (fiveInARow(r,c,player)) {
			declareWinner(player, playerColor);
		}

		var diamondId = "d"+r+"_"+c;
		console.log("diamondId="+diamondId);
		if (document.getElementById(diamondId)) {
			document.getElementById(diamondId).style.display = "none";
		}
		if ((player == 1) && (!undo)) document.getElementById("whoseUp").value = 2;
		else if ((player == 2) && (!undo)) document.getElementById("whoseUp").value = 1;
		
		// save this piece so it can be undone
		document.getElementById("lastPlacedPiece").value = marbleDiv;
		document.getElementById("lastMoveColor").value = playerColor;
		document.getElementById("lastMove").value = "place";
		document.getElementById("lastR").value = r;
		document.getElementById("lastC").value = c;
		document.getElementById("WhoseUpMarble").style.backgroundColor = nextPlayerColor;
	} else {
		console.log("that locaion is filled, make another move.");
	}
}

function doRemovePiece(r,c, undo) {
	board[r][c] = 0;
	var marbleDiv = r+"_"+c;
	document.getElementById(marbleDiv).style.visibility = "hidden";
	console.log("doRemovePiece: set "+marbleDiv+" to hidden");

	var diamondId = "d"+r+"_"+c;
	if (document.getElementById(diamondId)) {
		document.getElementById(diamondId).style.display = "block";
	}
	
	if (!undo) {
		// save this piece so it can be undone
		document.getElementById("lastPlacedPiece").value = marbleDiv;
		document.getElementById("lastMoveColor").value = document.getElementById(marbleDiv).style.backgroundColor;
		document.getElementById("lastMove").value = "remove";
		document.getElementById("lastR").value = r;
		document.getElementById("lastC").value = c;
		undoAvailable = true;
	}
}

function doRemoveCapture(p,count) {
	var marbleDiv = "p"+p+"c"+count+"m1";
	document.getElementById(marbleDiv).style.backgroundColor = "#fafafa";
	var marbleDiv = "p"+p+"c"+count+"m2";
	document.getElementById(marbleDiv).style.backgroundColor = "#fafafa";
}

function capture(r,c) {
return false;
	var player = document.getElementById("whoseUp").value;
	var newR, newC, rowAdjustment, columnAdjustment;
	
	for (var i=0; i<adjRow.length;i++) {
		rowAdjustment = adjRow[i];
		newR = r + rowAdjustment;
		for (var j=0; j<adjColumn.length; j++) {
			columnAdjustment = adjColumn[j];
			newC = c + columnAdjustment;
			
			if ((newR > 0) && (newR < boardSize-1)) { // on the board with room for 1 more in this direction
				if ((newC > 0) && (newC < boardSize-1)) { // on the board with room for 1 more in this direction
					if (board[newR][newC] == player) {
					
					}
				}
			}
		}
	}
}

function fiveInARow(r,c,player) {
	return false;
}

function declareWinner(player, playerColor) {
	document.getElementById("winner").style.color = playerColor;
	document.getElementById("winner").style.display = "block";
}

function setCapture(player, count) {
	var divId = "player"+player+"Color";
	var playerColor = document.getElementById(divId).value;
	
	divId = "p"+player+"c"+count+"m1";
	document.getElementById(divId).style.backgroundColor = playerColor;
	divId = "p"+player+"c"+count+"m2";
	document.getElementById(divId).style.backgroundColor = playerColor;
}

function clearCaptures(){
	var divId;
	
	for (var i=1; i<6; i++) {
		divId = "p1c"+i+"m1";
		document.getElementById(divId).style.backgroundColor = "#fafafa";
		divId = "p2c"+i+"m1";
		document.getElementById(divId).style.backgroundColor = "#fafafa";
		divId = "p1c"+i+"m2";
		document.getElementById(divId).style.backgroundColor = "#fafafa";
		divId = "p2c"+i+"m2";
		document.getElementById(divId).style.backgroundColor = "#fafafa";
	}
}

function doShowPopup(divId) {
	if (document.getElementById(divId))
		document.getElementById(divId).style.visibility = "visible";
}

function doCancelPopup(divId) {
	if (document.getElementById(divId))
		document.getElementById(divId).style.visibility = "hidden";
}

function doUndo() {
	var move = document.getElementById("lastMove").value;
	var r = document.getElementById("lastR").value;
	var c = document.getElementById("lastC").value;

console.log("undo "+move+" r="+r+" c="+c);
	if (move == "place") { // remove the last placement
		if (undoAvailable) {
			doRemovePiece(r,c, true);
			undoAvailable = false;
		}
	} else if (move == "remove") {
		if (undoAvailable) {
			doPlaceOnBoard(r,c, true);
			undoAvailable = false;
		}
	}
}

</script>

<input id="boardSize" type="hidden" value="<?=$boardSize?>">
<input id="player1Color" type="hidden" value="#B0E0E6">
<input id="player2Color" type="hidden" value="#2E8B57">
<input id="whoseUp" type="hidden" value="1">
<input id="lastPlacedPiece" type="hidden" >
<input id="lastMoveColor" type="hidden" >
<input id="lastMove" type="hidden">
<input id="lastR" type="hidden">
<input id="lastC" type="hidden">
<input id="lastCaptures" type="hidden">


<div id="winner" style="display:none;">Winner</div>

<div id="theGreatBoarder">
	<div id="theBoard">
		<div id="innerBoard">
			<? for($row=0; $row<$boardSize; $row++) { ?>
			<?	for ($column=0; $column<$boardSize; $column++) {
			?>
					<div class="intersection" onclick="doPlaceOnBoard(<?=$row?>,<?=$column?>, false);" >
					<? if ($column == 8) { ?>
						<div class="vMedian"></div>
					<? } else { ?>
						<div class="vLine"></div>
					<? } ?>
					
					<? if ($row == 8) { ?>
						<div class="hMedian"></div>
					<? } else { ?>
						<div class="hLine"></div>
					<? } ?>
					
						<div class="marble" id="<?=$row?>_<?=$column?>" ondrag="doRemovePiece(<?=$row?>,<?=$column?>, false)" draggable="true" title="<?=$row?>_<?=$column?>"></div>
					</div>
			
			<? 	} ?>
			<? } ?>

		</div><!-- end innerBoard -->
		
		<? for($row=0; $row<$boardSize; $row++) { 
			$offset = $row * 48;
		?>
			<div class="vExtendedLine" style="left:<?=$offset?>px;"></div>
			<div class="hExtendedLine" style="top:<?=$offset?>px;"></div>
		<? } ?>
		<? $offset_3 = 46 * 3 + 1; ?>
		<div id="d2_2"  class="diamond" style="top:<?=$offset_3?>px;left:<?=$offset_3?>px"></div>
		<? $offset_6 = 47 * 6 + 1; ?>
		<div id="d5_5"class="diamond" style="top:<?=$offset_6?>px;left:<?=$offset_6?>px"></div>
		<? $offset_9 = 47 * 9 + 4; ?>
		<div id="d8_8" class="diamond" style="top:<?=$offset_9?>px;left:<?=$offset_9?>px"></div>
		<? $offset_12 = 47 * 12 + 6; ?>
		<div id="d11_11" class="diamond" style="top:<?=$offset_12?>px;left:<?=$offset_12?>px"></div>
		<?	$offset_15 = 47 * 15 + 10; ?>
		<div id="d14_14" class="diamond" style="top:<?=$offset_15?>px;left:<?=$offset_15?>px"></div>

		<div id="d2_5" class="diamond" style="top:<?=$offset_3?>px;left:<?=$offset_6?>px"></div>
		<div id="d2_8" class="diamond" style="top:<?=$offset_3?>px;left:<?=$offset_9?>px"></div>
		<div id="d2_11" class="diamond" style="top:<?=$offset_3?>px;left:<?=$offset_12?>px"></div>
		<div id="d2_14" class="diamond" style="top:<?=$offset_3?>px;left:<?=$offset_15?>px"></div>

		<div id="d5_2" class="diamond" style="top:<?=$offset_6?>px;left:<?=$offset_3?>px"></div>
		<div id="d5_8" class="diamond" style="top:<?=$offset_6?>px;left:<?=$offset_9?>px"></div>
		<div id="d5_11" class="diamond" style="top:<?=$offset_6?>px;left:<?=$offset_12?>px"></div>
		<div id="d5_14" class="diamond" style="top:<?=$offset_6?>px;left:<?=$offset_15?>px"></div>

		<div id="d8_2" class="diamond" style="top:<?=$offset_9?>px;left:<?=$offset_3?>px"></div>
		<div id="d8_5" class="diamond" style="top:<?=$offset_9?>px;left:<?=$offset_6?>px"></div>
		<div id="d8_11" class="diamond" style="top:<?=$offset_9?>px;left:<?=$offset_12?>px"></div>
		<div id="d8_14" class="diamond" style="top:<?=$offset_9?>px;left:<?=$offset_15?>px"></div>
		
		<div id="d11_2" class="diamond" style="top:<?=$offset_12?>px;left:<?=$offset_3?>px"></div>
		<div id="d11_5" class="diamond" style="top:<?=$offset_12?>px;left:<?=$offset_6?>px"></div>
		<div id="d11_8" class="diamond" style="top:<?=$offset_12?>px;left:<?=$offset_9?>px"></div>
		<div id="d11_14" class="diamond" style="top:<?=$offset_12?>px;left:<?=$offset_15?>px"></div>
		
		<div id="d14_2" class="diamond" style="top:<?=$offset_15?>px;left:<?=$offset_3?>px"></div>
		<div id="d14_5" class="diamond" style="top:<?=$offset_15?>px;left:<?=$offset_6?>px"></div>
		<div id="d14_8" class="diamond" style="top:<?=$offset_15?>px;left:<?=$offset_9?>px"></div>
		<div id="d14_11" class="diamond" style="top:<?=$offset_15?>px;left:<?=$offset_12?>px"></div>
		
		<div class="repository" style="top:288px;left:-242px;"></div>
		<div class="repository" style="top:288px;right:-238px;"></div>
		<div class="repository" style="top:-242px;left:288px;"></div>
		<div class="repository" style="bottom:-238px;left:288px;"></div>
	</div><!-- theBoard -->
</div><!-- theGreatBoard -->

<div id="sidePanel">
	<div style="height:10px; width:100px; background-color:#2E8B57"></div>
	<div id="player1Captures">
		<div class="clear" onclick="setCapture(2, 1)" ondrag="doRemoveCapture(2,1)" draggable="true">
			<div class="captureBox"><div id="p2c1m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p2c1m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(2, 2)"  ondrag="doRemoveCapture(2,2)"  draggable="true">
			<div class="captureBox"><div id="p2c2m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p2c2m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(2, 3)" ondrag="doRemoveCapture(2,3)"  draggable="true">
			<div class="captureBox"><div id="p2c3m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p2c3m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(2, 4)" ondrag="doRemoveCapture(2,4)"  draggable="true">
			<div class="captureBox"><div id="p2c4m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p2c4m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(2, 5)" ondrag="doRemoveCapture(2,5)"  draggable="true">
			<div class="captureBox"><div id="p2c5m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p2c5m2" class="capturedMarble"></div></div>
		</div>
	</div>
	<div id="button-box" class="clear" >
		<div class="buttonContainer"><a href="#" class="button" onclick="doClearBoard();">New Game</a></div>
		<div class="buttonContainer"><a href="#" class="button" onclick="doShowPopup('informationPopup');">Information</a></div>
		<div class="buttonContainer"><a href="#" class="button" onclick="doUndo();">Undo</a></div>
   		
   		<!--a href="#" class="button" onclick="alert('use html5 color picker');">Change Colors</a-->
   	</div>
	<div style="position:absolute;top:475px;width:130px;margin:0 0 0 10px;">
		<div id="WhoseUpBox">
			<div style="width:80px;float:left;">Whose Up:</div>
			<div class="marble" style="margin:15px 0 0 10px;" id="WhoseUpMarble"></div>
		</div>
	</div>
	
	<div id="player2Captures" >
		<div class="clear" onclick="setCapture(1, 1)" ondrag="doRemoveCapture(1,1)"  draggable="true">
			<div class="captureBox"><div id="p1c1m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p1c1m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(1,2 )" ondrag="doRemoveCapture(1,2)"  draggable="true">
			<div class="captureBox"><div id="p1c2m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p1c2m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(1, 3)" ondrag="doRemoveCapture(1,3)"  draggable="true">
			<div class="captureBox"><div id="p1c3m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p1c3m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(1, 4)" ondrag="doRemoveCapture(1,4)"  draggable="true">
			<div class="captureBox"><div id="p1c4m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p1c4m2" class="capturedMarble"></div></div>
		</div>
		<div class="clear" onclick="setCapture(1, 5)" ondrag="doRemoveCapture(1,5)"  draggable="true">
			<div class="captureBox"><div id="p1c5m1" class="capturedMarble"></div></div>
			<div class="captureBox"><div id="p1c5m2" class="capturedMarble"></div></div>
		</div>
		<div style="height:10px; width:100px; background-color:#B0E0E6;position:absolute;bottom:0px;"></div>

	</div>
</div>

<div>

<div id="informationPopup" class="dialog" style="position:absolute; top:200px;">
	<div class="dialogContent"> <!-- was saveNewDialogContent -->
		<div class="postSearchForm">
			<span class="dialogTitle">Information</span>
			<img src="images/close.png" style="top:10px; right:10px; position:absolute;" onclick="doCancelPopup('informationPopup')">
			<div style="padding:20px 0 10px 10px;">
				To start a game, click where you would like a piece to be placed on the board.  The colors of the pieces will rotate between player 1 and player 2.
				Drag to remove a specific piece from the board.  
				<p>To track the number of captures, click on a shadow set of marbles at the right of the board.  Player 1 will place their captures at the top of the board and
				player 2 captures are at the bottom of the board.  Drag to remove captured pieces.</p>
				<p>To clear the board, click New Game.</p>
				
				<p>Coming eventually: automated captures, choosing player colors, playing remotely, stats, undo last move, and the ability to change board colors.</p>
			</div>
		

		</div> <!-- end postSearchForm -->

	</div>
</div>	<!-- end saveNewPopup -->



</div>
<script>
</script>