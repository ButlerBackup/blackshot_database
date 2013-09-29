<?php
/**
* Blackshot Class,
* 
* @author Shaun
* @version 0.01b
* @package blackshot
*/

define('WEB_URL', 'http://ingameweb.blackshotonline.com/UserInfo/BattleInfo.aspx?cid=');

class Blackshot {
	var $id;
	var $data;
	public function __construct()  {  
		//echo 'Blackshot class was initiated!';  
	} 
	
	public function __destruct() { 
		//echo 'Blackshot class was destroyed.<br />';  
	}  
	
	public function setID($id) {
		$this->id = $id;
	}
	
	public function getID($id) {
		return $this->id;
	}
	/**
	* Curl and return data from player page
	* @return content of player page
	*/
	public function getData() {
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => WEB_URL . $this->id,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'
		));
		$l = curl_exec($curl);
		curl_close($curl);
		$this->data = trim(preg_replace('/\s\s+/', ' ', $l));
	}
	
	/**
	* Automatically parse data from webpage into simple array
	* @param string player page content
	* @return array of information
	*/
	public function parseData() {
		$l = $this->data;
		$data = array();
		$data['time'] = time();
		if (strpos($l,'Server Error') === false) {
			$data['error'] = 0;
			$data['error_msg'] = 200; // success
			$data['name'] = stribet($l, '<span id="char_name">', '</span></a> </li>');
			$data['character'] = stribet($l, '<span id="chartype">', '</span></p> <p class="in_nick">');
			$data['current_exp'] = stribet($l, '<span id="curr_exp">', '</span> / <span id="exp_tnl">') . '/' . stribet($l, '<span id="exp_tnl">', '</span></p> <p class="in_fill">');
			$data['current_rank'] = stribet($l, 'Current rank: <img id="curr_rank" src="images/ranking/', '.gif" style="height:12px;');
			$data['clan'] = stribet($l, '<span id="clan">', '</span></p> <p class="in_rank">');
			$data['bp'] = stribet($l, '<p class="in_bp"> <span id="bp">', '</span></p> <div class="in_table">');
			
			$infoTable = stribet($l, '<div class="in_table">', '<!-- Information Table -->');
			$infoTable = explode("</p>", $infoTable);
			$info = array();
			foreach ($infoTable as $o) {
				if (preg_match("/<p>([^\<]*)<span id=\"([^\"]*)\">([^\<]*)<\/span>/", $o, $matches)) {
					$info[$matches[2]] = trim($matches[3]);
				}
			}
			$data['player_info'] = $info;
			
			$teamInfo = stribet($l, '<!-- Team Info -->', '<!-- Map Info -->');
			$teamInfo = explode("<tr>", $teamInfo);
			unset($teamInfo[0]);
			unset($teamInfo[1]);
			$team = array();
			foreach ($teamInfo as $o) {
				$o = str_replace("</td> <td>", "</td><td>", $o);
				if (preg_match("/<td align=\"left\">([^\<]*)<\/td><td><span id=\"([^\"]*)\">([^\<]*)<\/span><\/td><td><span id=\"([^\"]*)\">([^\<]*)<\/span><\/td>/", $o, $matches)) {
					$team[$matches[2]] = trim($matches[3]);
					$team[$matches[4]] = trim($matches[5]);
				}
			}
			$data['player_team_info'] = $team;
			
			$mapInfo = explode('<tr>', stribet($l, '<!-- Map Info -->', '<!-- SD Mode Info -->'));
			unset($mapInfo[0]);
			unset($mapInfo[1]);
			$map = array();
			foreach ($mapInfo as $o) {
				if (preg_match("/<[^\>]*>[^\>]*>([^\<]*)<\/span><\/td> <td><span id=\"[^\>]*>([^\<]*)<\/span><\/td> <td><span id=\"[^\>]*>([^\<]*)<\/span><\/td> <td><span id=\"[^\>]*>([^\<]*)<\/span>%<\/td> <td><span id=\"[^\>]*>([^\<]*)<\/span>/", $o, $matches)) {
					$map[$matches[1]]['kills'] = trim($matches[2]);
					$map[$matches[1]]['death'] = trim($matches[3]);
					$map[$matches[1]]['ratio'] = trim($matches[4]);
					$map[$matches[1]]['mins'] = trim($matches[5]);
				}
			}
			$data['player_map_info'] = $map;
			
			$sdInfo = explode('<tr>', stribet($l, '<!-- SD Mode Info -->', '<!-- TFM Mode Info -->'));
			unset($sdInfo[0]);
			unset($sdInfo[1]);
			$sd = array();
			foreach ($sdInfo as $o) {
				$o = str_replace("</td> <td>", "</td><td>", $o);
				if (preg_match("/<td align=\"left\">([^\<]*)<\/td><td><span id=\"([^\"]*)\">([^\<]*)<\/span><\/td><td><span id=\"([^\"]*)\">([^\<]*)<\/span><\/td> <\/tr>/", $o, $matches)) {
					$sd[$matches[1]][$matches[2]] = trim($matches[3]);
					$sd[$matches[1]][$matches[4]] = trim($matches[5]);
				}
			}
			$data['player_sd_info'] = $sd;
			
			$tfmInfo = explode('<tr>', stribet($l, '<!-- TFM Mode Info -->', '<!-- Body and Weapon Skill -->'));
			unset($tfmInfo[0]);
			unset($tfmInfo[1]);
			$tfm = array();
			foreach ($tfmInfo as $o) {
				if (preg_match("/<td align=\"left\">([^\<]*)<\/td> <td><span id=\"([^\"]*)\">([^\<]*)<\/span><\/td> <td><span id=\"([^\"]*)\">([^\<]*)<\/span><\/td> <\/tr>/", $o, $matches)) {
					$tfm[$matches[1]][$matches[2]] = trim($matches[3]);
					$tfm[$matches[1]][$matches[4]] = trim($matches[5]);
				}
			}
			$data['player_tfm_info'] = $tfm;
		} else {
			$data['error'] = 1;
			$data['error_msg'] = 404; //not found
		}
		return $data;
	}
}

?>