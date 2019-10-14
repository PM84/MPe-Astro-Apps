<?php
error_reporting(E_ALL & ~E_NOTICE);

$fn="data/raw/Cepheiden_Subset_Zone1.csv";
calculate_cepheiden_M_L($fn);


// $cepheid_FILE="0006_Cepheid"; 
function calculate_cepheiden_M_L($fn){
    $StarArr=ParseCSV_to_AssocArray($fn,",");
    bcscale(20);
    $periodes=array();
    $StarDetails=array();
    $i=1;
    foreach($StarArr as $star){
        //      var_dump($star);
        //     exit;
        $source_id=$star['source_id'];
        $parallax=str_replace ( "-" , "" ,strval($star['parallax']));
        if(floatval($parallax)>0){
            //         echo $parallax."<br>";
            $VisMag=str_replace ( "," , "." ,strval($star["phot_g_mean_mag"]));
            $pf=str_replace ( "," , "." ,strval($star['pf']));
            $distPC=bcdiv("1000",$parallax);//-$parallax_error);
            $M_calc=bcsub(bcsub(bcmul(5,bclog10($distPC)),"5"),$VisMag);
            array_push($StarDetails,array("source_id"=>$source_id,"parallax"=>$parallax,"m_g"=>$VisMag,"pf"=>$pf,"d_pc"=>$distPC,"M_g"=>$M_calc));
        }
        $i++;
// if($i>250){break;}
    }
    echo "$i Zeilen verarbeitet";
    $FNwithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $cepheid_FILE);
    $outputfile = fopen("data/cepheiden_korrigiert.js", "w");
    $txt = json_encode($StarDetails);
    fwrite($outputfile, $txt);
    fclose($outputfile);
}


function bclog10($n){
    //←Might need to implement some validation logic here!
    $pos=strpos($n,'.');
    if($pos===false){
        $dec_frac='.'.substr($n,0,15);$pos=strlen($n);
    }else{  $dec_frac='.'.substr(substr($n,0,$pos).substr($n,$pos+1),0,15);
         }
    return log10((float)$dec_frac)+(float)$pos;
}

function ParseCSV_to_AssocArray($path,$separator=","){
    $row = 1;
    $AssocArray=array();
    if (($handle = fopen($path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, $separator)) !== FALSE) {
            $num = count($data);
            if($row==1){$header=$data; $row++; continue;}
            array_push($AssocArray,array_combine ( $header , $data ));
            $row++;
        }
        fclose($handle);
    }
    return $AssocArray;
}