<?php 
function stribet($inputstr, $delimiterLeft, $delimiterRight) {
	$posLeft = stripos($inputstr, $delimiterLeft) + strlen($delimiterLeft);
	$posRight = stripos($inputstr, $delimiterRight, $posLeft);
	return substr($inputstr, $posLeft, $posRight - $posLeft);
}