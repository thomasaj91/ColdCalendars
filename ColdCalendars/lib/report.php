<?php

require_once(__DIR__.'/../DB.php');

class report {
	//User, PT/FT, hours worked between start/end
	private static $qryReport = "SELECT CONCAT(u.First,' ',u.Last) AS Name, 
       								 CASE u.PTFT WHEN 1 THEN 'Full Time'
                                     ELSE 'Part Time' END AS 'Work Status', SUM(swappy.Hours) as 'Total Hours'
								FROM User u
								JOIN(SELECT 
									Swap.Owner,
									(time_to_sec(timediff(End_time, Start_time )) / 3600) as Hours
									FROM Shift
									JOIN ( SELECT Shift_FK, MAX(Timestamp) AS Timestamp
									  FROM  Swap
								      WHERE Approved = True
									  GROUP BY Shift_FK, Approved
									) AS swp
									ON   swp.Shift_FK  = Shift.PK
									JOIN Swap
									ON   swp.Shift_FK  = Swap.Shift_FK
									AND  swp.Timestamp = Swap.Timestamp
									WHERE Shift.Start_time >= '@PARAM'
									AND   Shift.End_time   <= '@PARAM'
								) as swappy
								ON swappy.Owner = u.PK
								GROUP BY u.Login";
	
	function export_excel_csv($start,$end)
	{
		$conn = DB::getNewConnection();
		$result = DB::query($conn, DB::injectParamaters(array($start,$end), self::$qryReport));
		
		$rows = array();
		foreach($result as $row)
			array_push($rows,implode(',',$row));
		return implode("\n",$rows);
		
	/*	$conn = mysql_connect("preumbranet.domaincommysql.com","backend","wearewinners");
		$db = mysql_select_db("cold_calendars_test",$conn);
	
		$sql = self::$qryReport;
		$rec = mysql_query($sql) or die (mysql_error());
	
		$num_fields = mysql_num_fields($rec);
	
		$header = '';
		$data = '';
		for($i = 0; $i < $num_fields; $i++ )
		{
			$header .= mysql_field_name($rec,$i).",";
		}
	
		while($row = mysql_fetch_row($rec))
		{
			$line = '';
			foreach($row as $value)
			{
				if((!isset($value)) || ($value == ""))
				{
					$value = ",";
				}
				else
				{
					$value = str_replace( '"' , '""' , $value );
					$value = '"' . $value . '"' . ",";
				}
				$line .= $value;
			}
			$data .= trim( $line ) . "\n";
		}
	
		$data = str_replace("\\r" , "" , $data);
	
		if ($data == "")
		{
			$data = "\\n No Record Found!\n";
		}

		return "$header\n$data";*/
	}
}
?>
