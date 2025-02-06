<?php
    use chillerlan\QRCode\{QRCode, QROptions};
    use chillerlan\QRCode\Data\QRMatrix;
    use chillerlan\QRCode\Output\QROutputInterface;
    use chillerlan\QRCode\Common\EccLevel;

    require_once __DIR__.'/../vendor/autoload.php';
    require 'connect.php';

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
    
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
    
        return $randomString;
    }
    
    if(isset($_GET["contact"])){
       $phone_number = $_GET["contact"];
       $stmt = "SELECT id FROM id_rfid WHERE contact=" . $phone_number;
       $sql = $database->query($stmt);
       $result = $sql->fetch_assoc();
        if(!$result){
            echo "0";
            exit();
        }

        $id = $result["id"];
        $otp = generateRandomString(12);
        $stmt = "INSERT INTO otp (user_id,otp) VALUES (" . $id . ",'" . $otp ."')";
        $sql = $database->query($stmt);


        $options = new QROptions;
        $options->version             = 4;
        $options->outputType          = QROutputInterface::GDIMAGE_PNG;
        $options->scale               = 20;
        $options->outputBase64        = false;
        $options->bgColor             = [200, 150, 200];
        $options->imageTransparent    = true;
        $options->eccLevel            = 0b10;
        $options->maskPattern         = 6;


        $qrcode = (new QRCode($options))->render($otp);
        //header('Content-type: image/png');
        echo $qrcode;
    }

?>
