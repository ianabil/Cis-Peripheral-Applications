<?php
include('../connection.php');

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// For dataTable :: STARTS
$columns = array( 
    0 =>'Caveat No',
    1 =>'Caveator Name & Address',
    2 =>'Caveatee Name & Address',
    3 =>'Filing Date',
    4 =>'Filing Time',
    5 =>'Lower Case No',
    6 =>'Order Date',
    7 =>'Advocate Name & Address',
    8 =>'Section',
    9 =>'District Name'
);

if(strtotime($_POST['from_date']) < strtotime($_POST['to_date'])){
    $from_date = date("Y-m-d", strtotime($_POST['from_date'])); // From Date
    $to_date = date("Y-m-d", strtotime($_POST['to_date'])); // To Date
}
else{
    $to_date = date("Y-m-d", strtotime($_POST['from_date'])); // To Date
    $from_date = date("Y-m-d", strtotime($_POST['to_date'])); // From Date
}

$limit = $_POST['length']; // For No. of rows per page
$start = $_POST['start']; // For Offset

if($limit==-1)
    $limit_data ='';
else
    $limit_data =' LIMIT '.$limit. ' OFFSET '.$start;

// Base Query
$sql="(SELECT CAST(SUBSTRING(caveat_no, 1, 10) as int), caveat_t.* FROM caveat_t WHERE dt_filing BETWEEN '".$from_date."' AND '".$to_date."') 
      UNION ALL (SELECT CAST(SUBSTRING(caveat_no, 1, 10) as int), caveat_t_a.* FROM caveat_t_a WHERE dt_filing BETWEEN '".$from_date."' AND '".$to_date."') ORDER BY dt_filing DESC, 2 ASC".$limit_data;


$stmt=$conn->prepare($sql);	
$result=$stmt->execute();	

// Base Data Fetched
$rec=$stmt->fetchAll(PDO::FETCH_ASSOC);

/* For getting the total no. of data :: STARTS */
$sql_count = "SELECT COUNT(*) FROM 
(SELECT caveat_t.caveat_no FROM caveat_t WHERE dt_filing BETWEEN '".$from_date."' AND '".$to_date."'
UNION ALL SELECT caveat_t_a.caveat_no FROM caveat_t_a WHERE dt_filing BETWEEN '".$from_date."' AND '".$to_date."') jj";

$stmt=$conn->prepare($sql_count);	
$result=$stmt->execute();
$totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For getting the total no. of data which is in this case equal to the total no. of filtered data as here is no search option in the dataTable
$totalFiltered = $totalData['0']['count'];

/* For getting the total no. of data :: ENDS */

$record = array();

foreach($rec as $data){
    if($data['caveat_no']!=""){
            $caveat_suffix = substr($data['caveat_no'],1,10);
            $caveat_prefix = substr($data['caveat_no'],11,4);
            $report['Caveat No'] = ltrim($caveat_suffix,'0') . ' / '. $caveat_prefix; // Caveat No

            
            /* Caveator and Caveatee Name, Address Along With Extra Party's Name:: STARTS*/
            $caveator_name = $data['petname']; // Caveator Name
            $caveator_address;
            $caveatee_name = $data['resname']; // Caveatee Name
            $caveatee_address;

            $sql2 = "SELECT * FROM caveat_t LEFT JOIN party_address ON caveat_t.cino = party_address.cino WHERE caveat_t.cino='".$data['cino']."'
                    UNION ALL SELECT * FROM caveat_t_a LEFT JOIN party_address_a ON caveat_t_a.cino = party_address_a.cino WHERE caveat_t_a.cino='".$data['cino']."'";

            $stmt=$conn->prepare($sql2);	
            $result=$stmt->execute();	

            $rec2=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $caveatee_address="";
			$caveator_address="";
            foreach($rec2 as $data2){
                if($data2['type']==1)
                    $caveator_address = $data2['address'];
                else
                    $caveatee_address = $data2['address'];
            }
            

            $report['Caveator Name & Address'] = "<b>Name: </b>".$caveator_name ."<br>\n<b>Address: </b>".$caveator_address;   
            $report['Caveatee Name & Address'] = "<b>Name: </b>".$caveatee_name ."<br>\n<b>Address: </b>".$caveatee_address;   

            $sql3 = "SELECT civ_address_t.name, civ_address_t.type FROM caveat_t LEFT JOIN civ_address_t ON caveat_t.cino = civ_address_t.cino WHERE caveat_t.cino='".$data['cino']."'
                    UNION ALL SELECT civ_address_t_a.name, civ_address_t_a.type FROM caveat_t_a LEFT JOIN civ_address_t_a ON caveat_t_a.cino = civ_address_t_a.cino WHERE caveat_t_a.cino='".$data['cino']."'";

            $stmt=$conn->prepare($sql3);	
            $result=$stmt->execute();	

            $rec3=$stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($rec3 as $data3){
                if($data3['name']!=""){
                    if($data3['type']==1)
                        $report['Caveatee Name & Address'] = $report['Caveatee Name & Address']. "<br><br>\n\n<b>Name: </b>".$data3['name'];
                    else
                        $report['Caveator Name & Address'] = $report['Caveator Name & Address']. "<br><br>\n\n<b>Name: </b>".$data3['name'];
                }
            }


            /* Caveator and Caveatee Name, Address Along With Extra Party's Name:: ENDS*/

            // Filing Date
            $report['Filing Date'] = date("d-m-Y", strtotime($data['dt_filing']));

            // Filing Time
            $report['Filing Time'] = $data['time_of_filing'];

            /* Lower Court Case No :: STARTS */
            if($data['low_case_no']!=""){
                $l_court_casetype = substr($data['low_case_no'],1,3);
                $l_court_caseno_suffix = substr($data['low_case_no'],4,7);
                $l_court_caseno_prefix = substr($data['low_case_no'],11,4);

                $sql_1 = "SELECT * FROM lcase_type_t WHERE lcase_type=".$l_court_casetype;
                
                $stmt=$conn->prepare($sql_1);	
                $result=$stmt->execute();	

                $rec_1=$stmt->fetchAll(PDO::FETCH_ASSOC);

                $report['Lower Case No'] = $rec_1['0']['type_name']. ' <b> / </b>'. ltrim($l_court_caseno_suffix,'0')." <b> / </b>".$l_court_caseno_prefix;
                //$report['Lower Case No'] = $sql_1;
            }
            else{
                $report['Lower Case No']='';
            }
            /* Lower Court Case No :: ENDS */

            // Order Date
            if(date("d-m-Y", strtotime($data['ldec_date']))=='01-01-1970')
                $report['Order Date'] = '';
            else
                $report['Order Date'] = date("d-m-Y", strtotime($data['ldec_date']));

            // Advocate Name & Address :: STARTS
            $adv_name = $data['pet_adv'];

            $sql4 = "SELECT * FROM caveat_t LEFT JOIN advocate_t ON caveat_t.pet_adv_cd = advocate_t.adv_code WHERE caveat_t.caveat_no='".$data['caveat_no']."'
                     UNION ALL SELECT * FROM caveat_t_a LEFT JOIN advocate_t ON caveat_t_a.pet_adv_cd = advocate_t.adv_code WHERE caveat_t_a.caveat_no='".$data['caveat_no']."'";

            $stmt=$conn->prepare($sql4);	
            $result=$stmt->execute();	

            $rec4=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $adv_address = $rec4['0']['address'];
            $adv_phone = $rec4['0']['adv_mobile'];

            $report['Advocate Name & Address'] = "<b>Name: </b>".$adv_name."<br>\n<b> Address: </b>".$adv_address."<br>\n<b> Contact No.: </b>".$adv_phone;
        

            // Advocate Name & Address :: ENDS

            // Section
            $report['Section'] = ''; // No Data Avilable

            /* District Name :: STARTS */
            if($data['lower_court_district']!=''){
                $sql5 = "SELECT * FROM caveat_t LEFT JOIN district_t ON caveat_t.lower_court_district = district_t.dist_code WHERE caveat_t.lower_court_district=".$data['lower_court_district']." AND caveat_t.caveat_no='".$data['caveat_no']."'
                        UNION ALL SELECT * FROM caveat_t_a LEFT JOIN district_t ON caveat_t_a.lower_court_district = district_t.dist_code WHERE caveat_t_a.lower_court_district=".$data['lower_court_district']." AND caveat_t_a.caveat_no='".$data['caveat_no']."'";

                $stmt=$conn->prepare($sql5);	
                $result=$stmt->execute();	

                $rec5=$stmt->fetchAll(PDO::FETCH_ASSOC);

                $report['District Name'] = $rec5['0']['dist_name'];
            }
            else
                $report['District Name']='';

            /* District Name :: ENDS */

            $record[] = $report;
    }
}


$json_data = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData['0']['count']),
    "recordsFiltered" =>intval($totalFiltered),
    "data" => $record
);

echo json_encode($json_data);

?>