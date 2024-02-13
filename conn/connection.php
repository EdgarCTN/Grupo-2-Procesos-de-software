<?php
try{
$conn = new PDO('mysql:host=localhost; dbname=sma_unayoe', 'pma', '');
} catch(PDOException $e){
   echo "Error: ". $e->getMessage();
   die();
}
?>