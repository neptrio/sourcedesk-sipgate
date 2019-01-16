<?php
 
class SipgateLog extends TelephoneLogModule {
	protected $name = "Sipgate";
	protected $short = "sipgate";
 
	public function getSettings() {
		return Array(
			"username" => Array("type" => "text", "name" => "E-Mail"),
			"password" => Array("type" => "password", "name" => "Passwort"),
		);
	}
	 
	public function getLogs() {
		$username = $this->options['username'];
		$password = $this->options['password'];
 
		$resource = "/history?types=CALL";
 
		$ch = curl_init("https://api.sipgate.com/v2" . $resource);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$res = json_decode(curl_exec($ch));
		curl_close($ch);
 
		if (empty($res->items)) {
			return Array();
		}
 
		$c = Array();
 
		foreach ($res->items as $i) {
			$info = "";
			if($i->status == "PICKUP"){
				
				if($i->direction == "OUTGOING"){
					$info = "Zu: ".$i->target;
				}else{
					$info = "Von: ".$i->source;
				}
			
				$c[] = Array(
					"start" => date("Y-m-d H:i:s", strtotime($i->created)),
					"end" => date("Y-m-d H:i:s", strtotime("+{$i->duration} seconds", strtotime($i->created))),
					"info" => $info
				);
			}
		}
 
		return $c;
	}
}