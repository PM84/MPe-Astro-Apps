<?php
error_reporting(E_ALL & ~E_NOTICE);

// EXTENSION muss CSV sein wird hier aber nicht gesetzt, sondern weiter unten!
// $fnParts=array("0001_Cepheid","0002_Cepheid","0003_Cepheid","0004_Cepheid","0005_Cepheid","0006_Cepheid","0007_Cepheid","0008_Cepheid","0009_Cepheid","0010_Cepheid");

$files = scandir("data/raw/");
$fileList = array();
foreach ($files as $file) {
    if (strlen($file) > 3) {
        array_push($fileList, $file);
    }
}

$LK_files = array();
foreach ($fileList as $fn) {
    $LK_file = calculate_light_curves($fn);
    array_push($LK_files, $LK_file);
}
$outputfile = fopen("data/List_of_light_curves.js", "w");
$txt = "var cephFiles=" . json_encode($LK_files);
fwrite($outputfile, $txt);
fclose($outputfile);


function calculate_light_curves($cepheid_FILE)
{
    $StarArr = ParseCSV_to_AssocArray("data/0000_Cepheiden_uebersicht.csv", ";");

    $periodes = array();
    $StarDetails = array();
    foreach ($StarArr as $star) {
        $periodes[$star['source_id']] = $star['pf'];
        $StarDetails[$star['source_id']] = $star;
    }

    $MesurmentPoints = ParseCSV_to_AssocArray("data/raw/$cepheid_FILE", ",");
    $lightCurve = array();

    bcscale(20);
    $MagArr = array();
    foreach ($MesurmentPoints as $key => $MpArr) {
        if ($MpArr['band'] == "G" && $MpArr['rejected_by_photometry'] == "false" && $MpArr['rejected_by_variability'] == "false") {
            $PtP_G = str_replace(",", ".", strval($StarDetails[$MpArr['source_id']]['peak_to_peak_g']));
            $PtP_G_error = str_replace(",", ".", strval($StarDetails[$MpArr['source_id']]['peak_to_peak_g_error']));
            $parallax = str_replace(",", ".", strval($StarDetails[$MpArr['source_id']]['parallax']));
            $a_g_val = str_replace(",", ".", strval($StarDetails[$MpArr['source_id']]['a_g_val']));
            $teff_val = str_replace(",", ".", strval($StarDetails[$MpArr['source_id']]['teff_val']));
            $parallax_error = str_replace(",", ".", strval($StarDetails[$MpArr['source_id']]['parallax_error']));
            if ($a_g_val == "") {
                $a_g_val = 0;
            }
            $metallicity = str_replace(",", ".", strval($StarDetails[$MpArr['source_id']]['metallicity']));


            $pf = str_replace(",", ".", strval($periodes[$MpArr['source_id']]));
            $time = str_replace(",", ".", strval($MpArr["time"]));
            $VisMag = str_replace(",", ".", strval($MpArr["mag"]));
            $periodPart = bcmul(strval(bcdiv($time, $pf) - floor(bcdiv($time, $pf))), $pf);
            array_push($lightCurve, array("PfPart" => $periodPart, "VisMag" => $MpArr["mag"]));
            array_push($MagArr, $MpArr["mag"]);
        }
    }
    $MagMin = max($MagArr);
    $MagMax = min($MagArr);

    bcscale(6);
    $Cepheid = array();
    $meanMag = bcdiv(strval(bcadd(bcsub($MagMin, $a_g_val), bcsub($MagMax, $a_g_val))), 2); //-2.8; // -2.8 Extinktion
    $meanMagOhneAgVal = bcdiv(bcadd($MagMin, $MagMax), "2");
    $absMag = bcsub(bcmul("-2.78", bclog10($pf)), strval(1.32));
    $exp = bcdiv(bcadd(bcsub($meanMag, $absMag), strval(5)), strval(5));
    $distPC = bcdiv("1000", $parallax); //-$parallax_error);
    $m_calc = bcsub(bcsub(bcmul(5, bclog10($distPC)), "6.37"), bcmul("2.78", bclog10($pf)));
    $mdiff = bcsub($meanMagOhneAgVal, $m_calc);
    echo "pf:$pf=>calc:$m_calc=>mes:$meanMagOhneAgVal=>Extinction: $mdiff<br>";

    $Cepheid = array('source_id' => $MpArr['source_id'], "Teff_val" => $teff_val, "extinction" => $mdiff, "pf" => $pf, "a_g_val" => $a_g_val, "metallicity" => $metallicity, "MagMax" => $MagMax, "MagMin" => $MagMin, "parallax" => $parallax, "dist_pc" => $distPC, "PtP_G" => $PtP_G, "PtP_G_Er" => $PtP_G_error, "LK" => $lightCurve);

    $FNwithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $cepheid_FILE);
    $outputfile = fopen("data/$FNwithoutExt" . "_light_curve.js", "w");
    $txt = json_encode($Cepheid);
    fwrite($outputfile, $txt);
    fclose($outputfile);
    return $FNwithoutExt . "_light_curve.js";
}


function bclog10($n)
{
    //←Might need to implement some validation logic here!
    $pos = strpos($n, '.');
    if ($pos === false) {
        $dec_frac = '.' . substr($n, 0, 15);
        $pos = strlen($n);
    } else {
        $dec_frac = '.' . substr(substr($n, 0, $pos) . substr($n, $pos + 1), 0, 15);
    }
    return log10((float)$dec_frac) + (float)$pos;
}

function ParseCSV_to_AssocArray($path, $separator = ",")
{
    $row = 1;
    $AssocArray = array();
    if (($handle = fopen($path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, $separator)) !== FALSE) {
            $num = count($data);
            if ($row == 1) {
                $header = $data;
                $row++;
                continue;
            }
            array_push($AssocArray, array_combine($header, $data));
            $row++;
        }
        fclose($handle);
    }
    return $AssocArray;
}
