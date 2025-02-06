<?php

require __DIR__ . "/vendor/autoload.php";

$database = new mysqli("localhost", "root", "FaFen542", "rfid");
if ($database->connect_errno) {
    echo "Failed to connect to MySQL: (" . $database->connect_errno . ") " . $database->connect_error;
}

$target_dir = "uploads/";
$datum = mktime(date('H')+0, date('i'), date('s'), date('m'), date('d'), date('y'));
$target_file = $target_dir . date('Y.m.d_H:i:s_', $datum) . basename($_FILES["imageFile"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
//echo ($_FILES["imageFile"]["name"]);


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  }
  else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["imageFile"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";

  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
}
else {
  if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
    echo "The file ". basename( $_FILES["imageFile"]["name"]). " has been uploaded.";
  }
  else {
    echo "Sorry, there was an error uploading your file.";
  }
  $id = "";
  if(!empty($_POST["id"])){
    $uid = (string)$_POST["id"];

    $sql = $database->query("SELECT id FROM id_rfid WHERE rfid_uid=" . $uid);
    $result = $sql->fetch_assoc();
    $id = $result["id"];
  }

  if(!empty($_POST["qr"])){
    $otp = $_POST["qr"];
    $sql = $database -> query("SELECT user_id FROM otp WHERE otp='" . $otp . "'");
    $result = $sql->fetch_assoc();
    $id = $result["user_id"];
  }

  $sql = $database -> prepare("INSERT INTO data_id (user_id,picture) VALUES (?,?)");
  $sql -> bind_param("ss", $id,$target_file);
  
  if(!$sql -> execute()){
      echo "0";
  }
}
?>