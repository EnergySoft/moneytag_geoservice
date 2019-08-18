<?php


$times = Array();

$apc_lifetime = 1200;

$readfromcache = false;

function add_benchmark_point($comment){

	global $times;

	$start = 0;

	if(count($times) > 0){
		$start = $times[count($times)-1][0];
	}

	$times[] = Array(
		0=> round(microtime(true) * 1000),
		1=> round(microtime(true) * 1000) - $start,
		2=>$comment
	);

}
	


	//$ip = '87.231.127.184';

for($i = 0;$i<10000;$i++){

	$readfromcache = false;
		
	$milliseconds_start = round(microtime(true) * 1000);

	add_benchmark_point('start');

	include './_countries.php';

	add_benchmark_point('include 1');

	$times = Array();

	$ip = rand(0,255).'.'.rand(0,255).'.'.rand(0,255).'.'.rand(0,255);

	//echo $ip.'<br>';

	$ip_l = ip2long($ip);

	$parts = explode('.',$ip);	

	$parts[0] = intval($parts[0]);
	$parts[1] = intval($parts[1]);
	$parts[2] = intval($parts[2]);
	$parts[3] = intval($parts[3]);

	$file = './data/'.$parts[0].'/'.$parts[1].'.php';

	$iterations = 0;

	if(file_exists($file)){

		$apc_var = $parts[0].'.'.$parts[1];

		add_benchmark_point('include f2 before');

		if(apc_exists($apc_var)){
			$arr = apc_fetch($apc_var);				
			$readfromcache = true;		
			
		} else {
			require_once $file;
			apc_add($apc_var, $arr,$apc_lifetime);			
		}

		add_benchmark_point('include f2 after');

		if(isset($arr)){

			$found = false;
			foreach($arr as $diap){
				$iterations++;
				if($ip_l >= $diap[0] && $ip_l <= $diap[1]){
					echo $countries[$diap[2]];
					$found = true;
					break;
				}
			}
			if(!$found){
				echo '00';
			}
		} else {
			echo '00';
		}	

	} else {
		echo '00';
	}

	$milliseconds_end = round(microtime(true) * 1000);

	if($readfromcache){

		echo "<br><br>";

		echo "<br>TIME: ".($milliseconds_end - $milliseconds_start)."ms Iterations = ".$iterations;

		echo "<br>";
		echo "<table border=1>";
		foreach($times as $t){
			echo "<tr>";
			echo "<td>".$t[2]."</td>";
			echo "<td>".$t[0]."</td>";
			echo "<td>".$t[1]."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br><br>";
		

	}

}
	
	
	