<?php
#display list of audio tracks in folder ~/ServerFolder/Recordings
#each recording contains mp3 file, full audio transcript,only text transcript and small desc txt file
/*$recDir = '/home/luigi/SharedFolder/HTML/Projects/SpeechText/Recordings';
$recordings = array_diff(scandir($recDir),array('.','..'));
foreach($recordings as $rec){
    echo "<h3>".$rec."</h3><br>"; 
    $file = fopen($recDir.'/'.$rec."/desc.txt", "r");
    echo fgets($file);
    fclose($file);
}*/

$bruhdir='Recordings/Ormoni/';

$timestamps = fopen($bruhdir."timestamp.txt","r");
if($timestamps){
    while(!feof($timestamps)){
        //echo fgets($timestamps);
        $data = explode(" ",fgets($timestamps));
        echo $data[1]."_".$data[3]."_".$data[5]."-";
    }
} else{
    echo "error, unable to open file";
}
fclose($timestamps);
?>
