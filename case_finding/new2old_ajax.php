<?php
header('Content-Type: application/json');
include('../connection.php');
$appno = $_REQUEST['app_no'];
$apptype = $_REQUEST['app_type'];
$appyear = $_REQUEST['app_year'];

$caseno = $_REQUEST['case_no'];
$casetype = $_REQUEST['case_type'];
$caseyear = $_REQUEST['case_year'];

$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

 $sql="SELECT iaf.ia_case_type,ictt.ia_type_name,iaf.ia_regno,iaf.ia_regyear,
 iaf.regcasetype,ctt.type_name,iaf.reg_no,iaf.reg_year,
 iaf.calhc_appl_type,iaf.calhc_appl_no,iaf.calhc_appl_year,
 dtt.disp_name,
 'N' from_ia_filing_a
 from ia_filing iaf join ia_case_type_t ictt on iaf.ia_case_type=ictt.ia_case_type join case_type_t ctt on iaf.regcasetype=ctt.case_type 
 left outer join disp_type_t dtt on iaf.disp_nature=dtt.disp_type  
 where  iaf.regcasetype=:casetype  and iaf.reg_no=:caseno and iaf.reg_year=:caseyear 
 and iaf.calhc_appl_type=:apptype and iaf.ia_regno=:appno and iaf.ia_regyear=:appyear 
 
 UNION

 SELECT iaf_a.ia_case_type,ictt.ia_type_name,iaf_a.ia_regno,iaf_a.ia_regyear,
 iaf_a.regcasetype,ctt.type_name,iaf_a.reg_no,iaf_a.reg_year,
 iaf_a.calhc_appl_type,iaf_a.calhc_appl_no,iaf_a.calhc_appl_year,
 dtt.disp_name,
 'Y' from_ia_filing_a
 from ia_filing_a iaf_a join ia_case_type_t ictt on iaf_a.ia_case_type=ictt.ia_case_type join case_type_t ctt on iaf_a.regcasetype=ctt.case_type
 left outer join disp_type_t dtt on iaf_a.disp_nature=dtt.disp_type 
 where  iaf_a.regcasetype=:casetype  and iaf_a.reg_no=:caseno and iaf_a.reg_year=:caseyear 
 and iaf_a.calhc_appl_type=:apptype and iaf_a.ia_regno=:appno and iaf_a.ia_regyear=:appyear";
 //$param_arr=array();

 $stmt=$conn->prepare($sql);	
 
 $stmt->bindParam(':apptype', $apptype);
 $stmt->bindParam(':appno', $appno);
 $stmt->bindParam(':appyear', $appyear);
 
 $stmt->bindParam(':casetype', $casetype);
 $stmt->bindParam(':caseno', $caseno);
 $stmt->bindParam(':caseyear', $caseyear);
 
 
 $result=$stmt->execute();	
 $rec=$stmt->fetchAll(PDO::FETCH_ASSOC);
 echo json_encode($rec);

?>