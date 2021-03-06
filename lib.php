<?php
function checkGJP($gjp, $accountID) {
	include "connection.php";
	require "gjValues.php";
	$pwd = sha1(decodeGJP($gjp) . "ThUj31rsRRf");

	$q = $db->prepare("SELECT * FROM accounts WHERE accountID = '$accountID'");
	$q->execute();
	$r = $q->fetch(PDO::FETCH_ASSOC);
	if($pwd == $r["password"]) {
		return true;
	} else {
		return false;
	}
}

function sqlTrim($data) {
	return str_replace(array("'", "(", ")", "~", ":", "|", "#"), "", htmlspecialchars($data, ENT_QUOTES));
}

function makeTime($datetime) {
    return date("j.n.Y H:i:s", $datetime);
}

function calcTop($accountID) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM users ORDER BY stars DESC");
	$q->execute();
	$r = $q->fetchAll();

	for($i = 0; $i < count($r); $i++) {
		$user = $r[$i];
		$t = 1 + $i;
		if($user["accountID"] == $accountID) {
			return $t;
		}
	}
}

function newFriends($accountID) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM friends WHERE accountID = '$accountID' AND new = 1");
	$q->execute();
	$r = $q->fetchAll();
	return count($r);
}

function newRequests($accountID) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM frRequests WHERE `read` = '0' AND targetID = '$accountID'");
	$q->execute();
	$r = $q->fetchAll();
	return count($r);
}

function newMessages($accountID) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM messages WHERE `read` = '0' AND targetID = '$accountID'");
	$q->execute();
	$r = $q->fetchAll();
	return count($r);
}

function friendStatus($a, $b) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM friends WHERE accountID = '$a' AND targetID = '$b'");
	$q->execute();
	if($q->rowCount() > 0) {
		return 1;
	}
	$q = $db->prepare("SELECT * FROM frRequests WHERE accountID = '$a' AND targetID = '$b'");
	$q->execute();
	if($q->rowCount() > 0) {
		return 4;
	}
	$q = $db->prepare("SELECT * FROM frRequests WHERE accountID = '$b' AND targetID = '$a'");
	$q->execute();
	if($q->rowCount() > 0) {
		return 3;
	}
	return 0;
}

function canWriteTo($accountID) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM accounts WHERE accountID = '$accountID'");
	$q->execute();
	$r = $q->fetch(PDO::FETCH_ASSOC);
	if($r["msgAllowed"] != "0") return false; else return true;
}

function isMod($accountID) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM accounts WHERE accountID = '$accountID'");
	$q->execute();
	$r = $q->fetch(PDO::FETCH_ASSOC);
	if($r["mod"] != "0") return false; else return true;
}

function messageStatus($accountID) {
	include "connection.php";
	$q = $db->prepare("SELECT * FROM accounts WHERE accountID = '$accountID'");
	$q->execute();
	$r = $q->fetch(PDO::FETCH_ASSOC);
	return $r["msgAllowed"];
}
?>