<?php
/*
Folder(randomnum_date(necessary)_subj_name(necessary)_name(necessary))
    rec
        mp3(necessary)
        wav not implemented yet
        m4a not implemented yet
    transcript1
    trans2

*/
include_once './utils/database.util.php';
$uplaods_dest_folder = '/var/www/quintaB.xyz/public_html/Recordings';
echo "POST <br>";
print_r($_POST);
echo "FILES <br>";
print_r($_FILES);

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["uploadSubmit"])){
    // Check if mp3 file was uploaded without error
    if(isset($_FILES["track_mp3"]) && $_FILES["track_mp3"]["error"] == 0){
        $allowed = array("mp3","m4a","wav");
        $mp3_tempDir = $_FILES["track_mp3"]["tmp_name"];
        $mp3_name = $_FILES["track_mp3"]["name"];
        $mp3_type = $_FILES["track_mp3"]["type"];
        $mp3_size = $_FILES["track_mp3"]["size"];    
        // Verify file extension
        $mp3_ext = pathinfo($mp3_name, PATHINFO_EXTENSION);
        if($mp3_ext != $allowed[0]) die("Error: mp3 file is not mp3.");
        
        /* Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");*/
        //check for transcript files 
        $trans_timestamps_temp = "";
        $trans_full_temp = "";
        $is_transcribed = false;
        if(isset($_FILES["track_trans_timestamps"]) && $_FILES["track_trans_timestamps"]["error"] == 0){
            if(isset($_FILES["track_trans_full"]) && $_FILES["track_trans_full"]["error"] == 0){
                $trans_timestamps_name = $_FILES["track_trans_timestamps"]["name"];
                $trans_full_name = $_FILES["track_trans_full"]["name"];       
                if( pathinfo($trans_full_name, PATHINFO_EXTENSION) != txt || pathinfo($trans_timestamps_name, PATHINFO_EXTENSION) != txt){
                    die("Error: Transcripts in incorrect format");
                } else {
                    $is_transcribed = true;
                    $trans_timestamps_temp = $_FILES["track_trans_timestamps"]["tmp_name"];
                    $trans_full_temp = $_FILES["track_trans_full"]["tmp_name"];
                }
            }

        }
        // Check if date is set
        if(isset($_POST["track_date"])){
            $track_date = $_POST["track_date"];
            if(isset($_POST["track_subject"])){
                $track_subject = $_POST["track_subject"];
                if(isset($_POST["track_name"])){
                    $track_name = $_POST["track_name"];
                    $is_silenced = isset($_POST["is_silenced"]);
                    
                    //do stuff with files 
                    $new_track_dir_name = rand(1000,10000)."_".$track_date."_".$track_subject."_".$track_name;
                    $db = new Database;
                    $db_conn = $db->getConnection();
                    $sql_insert = "INSERT INTO recordings (name,path,subj_ID,date,is_converted,is_transcribed,is_silenced,mp3_path,silenced_path,transcript_path,transcript_timestamps_path) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $db_conn->prepare($sql_insert);
                    $track_path = $uplaods_dest_folder."/".$new_track_dir_name;
                    //make new track directory
                    mkdir($track_path);
                    //make new track red directory
                    mkdir($track_path."/rec");
                    $mp3_new_path = $track_path."/rec/recording.mp3";
                    //move uploaded file
                    if(move_uploaded_file($mp3_tempDir,$mp3_new_path)){
                        echo "mp3 file successfully uploaded! :D<br>";
                    } else {
                        //delete newly created directories, using shell exec for recursive delete
                        $rm_command = "rm -r ".$track_path;
                        shell_exec($rm_command);
                        die("mp3 file upload failed :( <br>");
                    }
                    $transcript_new_path = null;
                    $transcript_timestamps_new_path = null;
                    //create transcript paths and move them
                    if($is_transcribed){
                        $transcript_new_path = $track_path."/Transcript.txt";
                        if(move_uploaded_file($trans_full_temp,$transcript_new_path)){
                            echo "transcript file successfully uploaded! :D<br>";
                        } else {
                            $rm_command = "rm -r ".$track_path;
                            shell_exec($rm_command);
                            die("transcript file upload failed :( <br>");
                        }
                        $transcript_timestamps_new_path = $track_path."/timestamp.txt";
                        if(move_uploaded_file($trans_timestamps_temp,$transcript_timestamps_new_path)){
                            echo "timestamp transcript file successfully uploaded! :D <br>";
                        } else {
                            $rm_command = "rm -r ".$track_path;
                            shell_exec($rm_command);
                            die("timestamp transcript file upload failed :( <br>");
                        }
                        $is_transcribed = 1;
                    }else{
                        $is_transcribed = 0;
                    }
                    $silenced_path = null;
                    if($is_silenced){
                        $silenced_path = $mp3_new_path;
                        $is_silenced = 1;
                    } else{
                        $is_silenced = 0;
                    }
                    $is_converted = $is_transcribed;
                    $insert_track_info = array(
                        $track_name,
                        $track_path,
                        $track_subject,
                        $track_date,
                        $is_converted,
                        $is_transcribed,
                        $is_silenced,
                        $mp3_new_path,
                        $silenced_path,
                        $transcript_new_path,
                        $transcript_timestamps_new_path
                    );
                    //$stmt->bind_param("sssssssssss",$insert_track_info);
                    print_r($stmt);
                    //everything has worked so db can be updated 
                    if ($stmt->execute($insert_track_info)){
                        echo "data successfully uploaded to DB! <br> Go ahead and enjoy your recording! <br>"; 
                    } else{
                        $rm_command = "rm -r ".$track_path;
                        shell_exec($rm_command);
                        echo "<br>";
                        print_r($stmt->errorInfo());
                        echo "<br>";
                        die("data upload to DB failed :( <br> please retry <br>");
                    }

                } else{
                    echo "Name is missing";
                }
            } else{
                echo "Subject is missing";
            }
        } else{
            echo "Date is missing";
        }
    } else{
        echo "Error with mp3 file: " . $_FILES["track_mp3"]["error"];
    }
}