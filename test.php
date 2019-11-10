<head>
</head>
<html>

<?php
$url = file_get_contents('http://dev.artilia.bg/test.json');
//$url = file_get_contents('http://localhost/deviceStatus');
$obj = json_decode($url);
$deviceName = $obj->deviceName;
$deviceStatus = $obj->deviceStatus;
//return json_encode($obj);

$servername = "127.0.0.1";
$database = "dev_graf_bg";
$username = "homestead";
$password = "secret";
$charset = "utf8mb4";

//$url = 'http://localhost/deviceStatus';
//$curl = curl_init($url);
//curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//$curl_response = curl_exec($curl);
//curl_close($curl);
//$data = json_decode($curl_response);
//$deviceName = $data->deviceName;
//$deviceStatus = $data->deviceStatus;
//echo $deviceName;
//echo $deviceStatus;
//return $deviceStatus;

//$servername = "localhost";
//$database = "ri_db";
//$username = "test";
//$password = "";
//$charset = "utf8mb4";

try {

    $dsn = "mysql:host=$servername;dbname=$database;charset=$charset";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM rules where deviceName =  '$deviceName' and deviceStatus = '$deviceStatus'");
    if($stmt->rowCount() > 0) {
        if($stmt->rowCount() >= 2) {
            print_r("ambigious");
        }else{
            while ($row = $stmt->fetch()) {
                print_r($row["action"]);
            }
        }
    }else{
        print_r("unknown");
    }


    return $pdo;

}

catch (PDOException $e)

{
    echo "Connection failed: ". $e->getMessage();
}



?>

</html>
