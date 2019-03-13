<?php
    if(session_id() == '') {
        session_start();
    }


    if($_SESSION['username']=='')
        header('location: ../login.php');

    header('Content-Type: application/json');

    include('../connection.php');

    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    // will fetch existing purpose list for a specific bench
    function fetch_bench_purpose($conn, $bench){
        
        $sql = "select * from bench_purpose where bench_id=:bench and 
        create_modify=(select max(create_modify) from bench_purpose where bench_id=:bench)";
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':bench', $bench);  
        $result=$stmt->execute();
        $bench_purpose = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($bench_purpose);

    }


    // will insert purpose list for a specific bench
    function insert_bench_purpose($conn, $bench, $purpose_name, $purpose_code){
        
        $current_date = date('Y-m-d');
        
        $sql = "delete from bench_purpose where bench_id=:bench and create_modify=:current_date";
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':bench', $bench); 
        $stmt->bindParam(':current_date', $current_date, PDO::PARAM_STR);  
        $result=$stmt->execute();

        for($i=0; $i<sizeof($purpose_name);$i++){
                $priority = $i+1;

                $sql = "insert into bench_purpose
                (bench_id, purpose_code, purpose_name, purpose_priority, est_code_src, create_modify) 
                values (:bench_id,:purpose_code,:purpose_name,:priority,:est_code,:current_date)";
                
                $stmt=$conn->prepare($sql);
                
                $stmt->bindParam(':bench_id', $bench, PDO::PARAM_INT);
                $stmt->bindParam(':purpose_code', $purpose_code[$i], PDO::PARAM_INT);
                $stmt->bindParam(':purpose_name', $purpose_name[$i], PDO::PARAM_STR);
                $stmt->bindParam(':priority', $priority, PDO::PARAM_INT);
                $stmt->bindParam(':est_code', $_SESSION['est_code'], PDO::PARAM_STR);
                $stmt->bindParam(':current_date', $current_date, PDO::PARAM_STR);

                $result=$stmt->execute();
        }

        echo 1;

    }



    $type_of_job = $_POST['type_of_job'];

    if($type_of_job=='bench_purpose_fetching')
    {
        $bench = $_POST['bench'];
        fetch_bench_purpose($conn, $bench);
    }
    else if($type_of_job=='bench_purpose_insertion')
    {
        $bench = $_POST['bench'];
        $purpose_name = $_POST['purpose_name'];
        $purpose_code = $_POST['purpose_code'];
        insert_bench_purpose($conn, $bench, $purpose_name, $purpose_code);

    }

?>