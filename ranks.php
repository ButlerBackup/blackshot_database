<?php 

function stribet($inputstr, $delimiterLeft, $delimiterRight) {
	$posLeft = stripos($inputstr, $delimiterLeft) + strlen($delimiterLeft);
	$posRight = stripos($inputstr, $delimiterRight, $posLeft);
	return substr($inputstr, $posLeft, $posRight - $posLeft);
}

include "config.php";

$curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => "http://blackshot.garena.com/info/ranks",
CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'
));

$l = curl_exec($curl);
curl_close($curl);
$data = explode("</li>", stribet($l, '<li class="cols3 col1">', "</ul>"));
foreach ($data as $d) {
	$i = explode("\n", $d);
	if (count($i) > 3) {
		//print_r($i);
		echo $i[2];
		$name = str_replace("</h5>", "", $i[2]);
		$name = str_replace("<h5>", "", $name);
		//$name = $i[2];
		$exp = trim($i[3]);
		//echo "Type : " . $name . " - " . $exp . "\n";
	}
}