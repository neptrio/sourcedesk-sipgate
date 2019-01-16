<?php

class SipgateCallthrough extends TelephoneModule {
	protected $name = "sipagte";

	public function call($number, $info) {
		$ex = explode("|", $info);

		$data = Array(
			"deviceId" => $ex[2],
			"caller" => $ex[3],
			"callee" => $number,
			"callerId" => $ex[4]
		);
		
		$data_string = json_encode($data);

		$ch = curl_init("https://api.sipgate.com/v2/sessions/calls");
		curl_setopt($ch, CURLOPT_USERPWD, $ex[0] . ":" . $ex[1]);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    		'Content-Type: application/json',                                                                                
    		'Content-Length: ' . strlen($data_string))                                                                       
		);  
		$res = json_decode(curl_exec($ch));
		curl_close($ch);

		return $res->result == 1;
	}
}