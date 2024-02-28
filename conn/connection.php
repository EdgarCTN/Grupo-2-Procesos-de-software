<?php
try{
$conn = new PDO('mysql:host=localhost;port=3307; dbname=sma_unayoe', 'pma', '');
} catch(PDOException $e){
   echo "Error: ". $e->getMessage();
   die();
}
?>