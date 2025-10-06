
<?php

define('DB_HOST','localhost');
define('DB_NAME','mode_unique');
define('DB_USER','root');
define('DB_PASSWORD','');
define('DB_CHARSET','utf8mb4');


$dsn="mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
$options =[
    PDO::ATTR_ERRMODE => PDO:: ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO:: FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES => false,

];
try {
    $pdo= new PDO ($dsn,DB_USER,DB_PASSWORD,$options);
}catch (PDOException $e){
    throw new \PDOException("Database connection failed:" 
    .$e->getMessage()."(ESrror code".$e->getCode().")");
}

