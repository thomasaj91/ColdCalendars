<?php

require_once(__DIR__.'/DB.php');

class reports {
	private static $qryReport = 'SELECT CONCAT(u.First," ",u.Last) AS Name, p.number AS "Phone Number", 
       								e.address AS Email, t.title, CASE u.PTFT WHEN 1 THEN "Full Time"
                                    ELSE "Part Time" END AS "Work Status",
                                    u.Vacation AS "Vacation Days"
								FROM User u JOIN UserType t
								ON (u.title = t.PK)
								JOIN Email e
								ON (e.User_FK = u.PK)
								JOIN Phone p
								ON (p.User_FK = u.PK)
								GROUP BY u.Login';
	
	function export_excel_csv()
	{
		$conn = mysql_connect("preumbranet.domaincommysql.com","backend","wearewinners");
		$db = mysql_select_db("cold_calendars_test",$conn);
	
		$sql = $qryReport;
		$rec = mysql_query($sql) or die (mysql_error());
	
		$num_fields = mysql_num_fields($rec);
	
		for($i = 0; $i < $num_fields; $i++ )
		{
			$header .= mysql_field_name($rec,$i)."\\t";
		}
	
		while($row = mysql_fetch_row($rec))
		{
			$line = '';
			foreach($row as $value)
			{
				if((!isset($value)) || ($value == ""))
				{
					$value = "\\t";
				}
				else
				{
					$value = str_replace( '"' , '""' , $value );
					$value = '"' . $value . '"' . "\\t";
				}
				$line .= $value;
			}
			$data .= trim( $line ) . "\\n";
		}
	
		$data = str_replace("\\r" , "" , $data);
	
		if ($data == "")
		{
			$data = "\\n No Record Found!\n";
		}
	
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=reports.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		print "$header\\n$data";
		echo "$header\\n$data";
	}
}
?>
