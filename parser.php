<?php

$value_final = Array();


include '_countries.php';

$files = scandir("./files");

foreach($files as $file){

		if(strpos($file,'.txt')){

		$country_code = explode('.',$file)[0];
		$country = array_search($country_code, $countries);
		
		echo "<br>".$file."<br>";
		
		if(!$country){
			echo "##<<<<<<<<<<<<<".$file." / ".$country_code.">>>>>>>>>>>>>##";
			break;
		}

		$content = file_get_contents('./files/'.$file);
		$content = str_replace(' - ', '-', $content);
		$lines = explode(" ",$content);

		foreach($lines as $line){

			$ips = explode('-', $line);

			$ip_low = $ips[0];
			$ip_hight = $ips[1];

			if(!isset($ips[1])){
				echo $file;
				echo "<br>";
				var_dump($ips);
			}

			$low_parts = explode(".", $ip_low);

			$n1 = intval($low_parts[0]);
			$n2 = intval($low_parts[1]);

			$hight_parts = explode(".", $ip_hight);

			$n1_h = intval($hight_parts[0]);
			$n2_h = intval($hight_parts[1]);

			if($n2 != $n2_h){

				for($i = $n2; $i <= $n2_h; $i++){

					$ip1 = $low_parts[0].".".$i;
					if($i == $n2){
						$ip1.=".".$low_parts[2].".".$low_parts[3];
					} else {
						$ip1.=".0.0";
					}

					$ip2 = $hight_parts[0].".".$i;
					if($i == $n2_h){
						$ip2.=".".$hight_parts[2].".".$hight_parts[3];
					} else {
						$ip2.=".255.255";
					}

					pack_ip($ip1, $ip2, $country);

				}

			} else {

				pack_ip($ip_low, $ip_hight, $country);

			}
		}
	}

}

	function pack_ip($ip_low, $ip_hight, $country){
		global $value_final;

		$low_parts = explode(".", $ip_low);

		$n1 = intval($low_parts[0]);
		$n2 = intval($low_parts[1]);

		$hight_parts = explode(".", $ip_hight);

		$n1_h = intval($hight_parts[0]);
		$n2_h = intval($hight_parts[1]);

		if(!isset($value_final[$n1])){
			$value_final[$n1] = Array();
		}

		if(!isset($value_final[$n1][$n2])){
			$value_final[$n1][$n2] = Array();
		}

		$value_final[$n1][$n2][] = Array(
			0=>ip2long($ip_low),
			1=>ip2long($ip_hight),
			2=>$country
		);

	}


	/*

	foreach($value_final as $key=>$value){

		$str = "Array(\r\n";

		foreach($value as $sub_key=>$sub_val){

			$str.= $sub_key."=> Array(";

			foreach($sub_val as $sub_sub_val){
				$str.= "Array(".$sub_sub_val[0].",".$sub_sub_val[1].",".$sub_sub_val[2]."),";
			}

			$str.="),\r\n";

		}

		$str.=");\r\n";


		file_put_contents("./data/".$key.".php",'<?php $arr = '.$str);

	}	

	*/


	foreach($value_final as $key=>$value){

		

		foreach($value as $sub_key=>$sub_val){

			$str = "Array(\r\n";			

			foreach($sub_val as $sub_sub_val){
				$str.= "Array(".$sub_sub_val[0].",".$sub_sub_val[1].",".$sub_sub_val[2]."),";
			}

			$str.=");\r\n";

			if(!is_dir('./data/'.$key)){
				mkdir('./data/'.$key);
			}

			file_put_contents("./data/".$key."/".$sub_key.".php",'<?php $arr = '.$str);

		}

	}
	
	
	