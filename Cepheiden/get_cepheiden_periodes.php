<?php
$path="data/0000_Cepheiden_uebersicht.csv";
var_dump(ParseCSV_to_AssocArray($path));
function ParseCSV_to_AssocArray($path){
    $row = 1;
    $AssocArray=array();
    if (($handle = fopen($path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
            $num = count($data);
            if($row==1){$header=$data; $row++; continue;}
            array_push($AssocArray,array_combine ( $header , $data ));
            $row++;
        }
        fclose($handle);
    }
    return $AssocArray;
}

