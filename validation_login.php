<?php
if(session_id() == '') {
    session_start();
}

include('connection_ecourtisuserdb.php');


header('Content-Type: application/json');


$uid_login = $_REQUEST['uid'];
$pwd = md5($_REQUEST['pwd']);
$est_code=$_SESSION['est_code'];


 $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


 //to find out user if permission given 

 $sql="select users.userid, users.username, users.mobile, users.full_name, users.email, users.ip, establishment.estname, 
 establishment.estid, id_role_est.role_type_id, role_master.role_type
 from users
 left outer join id_role_est on users.userid =id_role_est.user_id
 left outer join establishment on id_role_est.establishmentid =establishment.est_code
 left outer join role_master on id_role_est.role_type_id = role_master.role_type_id::text
 where  users.username=:uid_login and users.user_password=:pwd and establishment.est_code=:est_code" ;
 


 $stmt=$conn->prepare($sql);	
 
 $stmt->bindParam(':uid_login', $uid_login);
 $stmt->bindParam(':pwd', $pwd);
 $stmt->bindParam(':est_code', $est_code);

 
 
 $result=$stmt->execute();	
 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

    
    $i=0;


    if(sizeof($result)>0) //size of the arrey
    {
       
        $value = array();

        $_SESSION['userid']=$result['0']['userid'];
        $_SESSION['username']=$result['0']['username'];
        $_SESSION['mobile']=$result['0']['mobile'];
        $_SESSION['full_name']=$result['0']['full_name'];
        $_SESSION['email']=$result['0']['email'];        
        $_SESSION['ip']=$result['0']['ip'];
        $_SESSION['estname']=$result['0']['estname'];
        $_SESSION['estid']=$result['0']['estid'];        

        foreach($result as $r)
        {            
            $value['role_type_id'][$i]=$r['role_type_id'];
            $value['role_type'][$i]=$r['role_type'];

            $i++;           
        }              
              
        $_SESSION['role_type_id']= $value['role_type_id'];
        $_SESSION['role_type']= $value['role_type'];      

    }
    
    //to send the number of roles aasigned to the user:
    //if only one role: $i=1;   if multiple role: $i > 1;  if no role or login credencial error: $i=0

    //print_r($_SESSION);
    echo json_encode($i);  



?>

