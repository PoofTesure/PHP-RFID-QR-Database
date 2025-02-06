<?php

    require 'connect.php'; #Connect database
    if(!empty($_POST["id"] && $_POST["id"] != 0)){
        $uid = (string)$_POST["id"];

        $sql = $database->query("SELECT id, name FROM id_rfid WHERE rfid_uid=" . $uid);
        $result = $sql->fetch_assoc();
        if(!$result){
            echo "0";
            exit();
        }
        
        $name = $result['name'];
        $id = $result['id'];
        echo "1," . $name;
    }

    if(!empty($_POST["qr"])){
        $otp = $_POST["qr"];

        $sql = $database -> query("SELECT user_id,generated_time FROM otp WHERE otp='" . $otp . "'");
        $result = $sql->fetch_assoc();

        $userid = $result['user_id'];
        $active = $result['generated_time'];

        //echo "SELECT name from id_rfid WHERE id=" . $userid;

        $sql = $database -> query("SELECT name from id_rfid WHERE id=" . $userid);
        $result = $sql -> fetch_assoc();
        $name = $result["name"];

        $active = date("Y-m-d H:i:s",strtotime("+1 day",strtotime($active)));
        $time_now = date("Y-m-d H:i:s",strtotime("now"));

        if($time_now > $active){
            echo "0";
            exit;
        }
        $sql = $database -> query("INSERT INTO data_id (user_id) VALUES (" . $userid . ")");

        echo "1," . $name;
    }
?> 