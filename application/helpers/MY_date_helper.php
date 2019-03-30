<?php 
	//lay ngay tu he thong dang int
	//$time = int: tgian muon hien thi
	//$full_time: cho biet co lay gio phut giay ko	
	function get_date($time, $full_time = TRUE)
	{	
		$format = '%d-%m-%y';
		if($full_time)
		{
			$format = $format.'- %h:%i:%s';
		}
		$date = mdate($format,$time);
		return $date;
	}

 ?>