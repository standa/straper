<?php if (!defined('ROOT')) die('ROOT const not set.');

class Fio extends Module {

	var $dependencies = array(
		'curl_init', 'simplexml_load_string', ''
	);

	var $username;
	var $password;

	function __construct($username, $password) {

		parent::__construct();
		
		$this->username = $username;
		$this->password = $password;
	}

	function exchange_rates2($date) {

		$snoopy = $this->snoopy();
		

	}

	/**
	 * runs the following curl command
	 * curl --data 
	 * "LOGIN_USERNAME=$username&LOGIN_PASSWORD=$password&LOGIN_TIME=`date 
	 * +%s`&DAT_ke_dni=21.03.2013" https://www.fio.cz/scgi-
	 * bin/hermes/kurzovni_listek.cgi
	 * @param $date ve formatu d.m.Y
	 */
	function exchange_rates($date) {

		$curl = $this->cache('exchange_rates_'.$date);

		if (empty($curl)) {
			$curl = $this->exec("curl --data \"LOGIN_USERNAME=".$this->username.
				"&LOGIN_PASSWORD=".$this->password."&LOGIN_TIME=".time().
				"&DAT_ke_dni=$date\" ".
				"https://www.fio.cz/scgi-bin/hermes/kurzovni_listek.cgi"
			);
			$this->cache('exchange_rates_'.$date, $curl);
		}

		$d = $this->simple_html_dom($curl);

		$rates = array('_id' => date('Ymd', strtotime($date)));

		$skip_first = false; // first is the header
		$set_id     = false;

		foreach($d->find('table.main tr') as $r) {
			if (!$skip_first) {
				$skip_first = true;
				continue;
			}

			$pair = strip_tags($r->children(0)->innertext);
			$pair = preg_replace('/ \(.*\)/', '', $pair);

			$rates[$pair]['datetime'] = substr(
				$r->children(0)->children(0)->innertext, 1, -1);

			$rates[$pair]['date'] = 
				date('Y-m-d', strtotime($rates[$pair]['datetime']));

			$rates[$pair]['time'] = 
				date('G:i', strtotime($rates[$pair]['datetime']));

			if (!$set_id) {
				$rates['_id'] = strtotime($rates[$pair]['datetime']);
				$set_id = true;				
			}

			$rates[$pair]['buy']  = $r->children(1)->innertext;
			$rates[$pair]['sell'] = $r->children(2)->innertext;
		}

		$db = get_mongo('fio', 'exchange_rates');
		
		if ($ret = $db->save($rates)) {
			// echo 'Write of '.$date.' OK'."\n";
		} else {
			return $ret;
		}

		// foreach ($db->find() as $id => $v) {
		// 	echo "$id => ";
		// 	print_r($v);
		// }

		return $rates;
	}


}