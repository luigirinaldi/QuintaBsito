<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordings Player</title>
    <link rel="stylesheet" media="screen and (max-width:800px)" href="phoneStyle.css">
    <link rel="stylesheet" media="screen and (min-width:800px)" href="desktopStyle.css">
    <link rel="stylesheet" href="player.css">
</head>
<body class="background">
    <div class="centercontainer">       
        <div class="header">
            <?php 
            include 'db_connection.php';
            $connection = OpenCon();
            $queryGetRecordings = sprintf('SELECT * FROM recordings WHERE recordings.ID = %s',$_GET['recordingID']);
            $recording =  mysqli_query($connection,$queryGetRecordings)->fetch_all(MYSQLI_ASSOC)[0];
            $queryGetSubjInfo = sprintf('SELECT `ID`,`name` FROM Subjects WHERE Subjects.ID = %s',$recording['subj_ID']);
            $subjInfo =  mysqli_query($connection,$queryGetSubjInfo)->fetch_all(MYSQLI_ASSOC)[0];

            echo sprintf("<a href='recordingSelector.php?subjectID=%s&subjectName=%s'>",$subjInfo['ID'],$subjInfo['name']); 
            ?>
                <div class="backBttn">
                    <?php include("Icons/backArrow1.svg") ?>
                </div>
            <?php echo "</a>"; ?>
            <div class="title">
                <?php
                echo $recording['name'];                
                ?>
            </div>
        </div>
        <div class="transcript">
            <div class="text">
                <?php
                    if ($recording['is_transcribed'] && ($recording['transcript_timestamps_path']!=NULL)){
                        $timestamps = fopen($recording['transcript_timestamps_path'], "r");
                        if($timestamps){
                            $i = 0;
                            while(!feof($timestamps)){
                                $data = explode(" ",fgets($timestamps));
                                echo "<span class='word' id='ts".$i."' onClicked='seekToWord(this.id)'>".$data[5]."</span> ";
                                $i+=1;
                            }
                        } else{
                            echo "error, unable to open file";
                        }
                        fclose($timestamps);
                    } else {
                        echo "recording is not yet Transcribed";
                    }
                ?>
            </div>
            <div class="timestamps">
            </div>
        </div>        
    </div> 
    <div class="player-container player">
        <div class="buttons">
            <div class="rewind_btn">
            </div>
            <div class="playpause-track">
            </div>
            <div class="fastFwd_btn">
            </div>
        </div>
        <div class="slider-container">
            <div class="current-time">00:00</div>
            <div class="playback-speed"></div>
            <input type="range" min="1" max="100" value="0" class="seek_slider" onchange="seekTo()">
            <div class="total-duration">00:00</div>    
        </div>
    </div>

    <script type="text/template" id="pauseBtn">
        <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->    
        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 409.6 409.6" style="enable-background:new 0 0 409.6 409.6;" xml:space="preserve">
        <g>
            <g>
                <path d="M204.8,0C91.648,0,0,91.648,0,204.8s91.648,204.8,204.8,204.8s204.8-91.648,204.8-204.8S317.952,0,204.8,0z M182.272,256
                    c0,12.8-10.24,22.528-22.528,22.528c-12.8,0-22.528-10.24-22.528-22.528V153.6c-0.512-12.288,9.728-22.528,22.016-22.528
                    c12.8,0,23.04,10.24,23.04,22.528V256z M272.896,256c0,12.8-10.24,22.528-22.528,22.528c-12.8,0-22.528-10.24-22.528-22.528V153.6
                    c-0.512-12.288,9.728-22.528,22.016-22.528c12.8,0,23.04,10.24,23.04,22.528V256z" onclick="playpauseTrack()"/>
            </g>
        </g>
        </svg>
    </script>

    <script type="text/template" id="playBtn">
        <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
            <g>
                <g>
                    <path d="M256,0C114.511,0,0,114.497,0,256c0,141.49,114.495,256,256,256c141.49,0,256-114.497,256-256C512,114.51,397.503,0,256,0
                        z M348.238,284.418l-120.294,69.507c-10.148,5.864-22.661,5.874-32.826,0.009c-10.158-5.862-16.415-16.699-16.415-28.426V186.493
                        c0-11.728,6.258-22.564,16.415-28.426c5.076-2.93,10.741-4.395,16.406-4.395c5.67,0,11.341,1.468,16.42,4.402l120.295,69.507
                        c10.149,5.864,16.4,16.696,16.4,28.418C364.639,267.722,358.387,278.553,348.238,284.418z" onclick="playpauseTrack()"/>
                </g>
            </g>
        </svg>
    </script>

    <script type="text/template" id="fastFwdBtnIcon">
        <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
            <g>
                <g>
                    <path d="M150,0C67.157,0,0,67.157,0,150c0,82.841,67.157,150,150,150c82.838,0,150-67.159,150-150C300,67.157,232.838,0,150,0z
                        M235.188,157.228l-71.532,41.301c-1.289,0.744-2.731,1.123-4.171,1.123s-2.879-0.379-4.171-1.123
                        c-2.583-1.489-4.173-4.246-4.173-7.226v-33.899l-71.239,41.125c-1.292,0.744-2.734,1.123-4.173,1.123
                        c-1.439,0-2.879-0.379-4.171-1.123c-2.583-1.489-4.173-4.246-4.173-7.226v-82.605c0-2.977,1.59-5.74,4.173-7.228
                        c2.583-1.489,5.76-1.489,8.346,0l71.237,41.132v-33.904c0-2.977,1.587-5.74,4.171-7.228c2.583-1.489,5.766-1.489,8.349,0
                        l71.532,41.304c2.583,1.486,4.178,4.248,4.168,7.226C239.364,152.98,237.771,155.74,235.188,157.228z" onclick="fastFwdTrack()"/>
                </g>
            </g>     
        </svg>
    </script>

    <script type="text/template" id="rewindBtnIcon">
        <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
            <g>
                <g>
                    <path d="M150.003,0C67.162,0,0,67.159,0,150c0,82.838,67.162,150,150.003,150S300,232.838,300,150C300,67.157,232.843,0,150.003,0
                        z M228.439,198.529c-1.292,0.744-2.731,1.12-4.171,1.12s-2.882-0.376-4.173-1.12l-71.239-41.127v33.899
                        c0,2.983-1.59,5.74-4.173,7.228c-1.292,0.744-2.734,1.12-4.173,1.12c-1.439,0-2.882-0.376-4.171-1.12l-71.54-41.301
                        c-2.583-1.489-4.173-4.251-4.173-7.228c0-2.98,1.59-5.74,4.173-7.228l71.535-41.304c2.583-1.489,5.763-1.489,8.346,0
                        s4.173,4.251,4.173,7.228V142.6l71.237-41.132c2.586-1.489,5.763-1.489,8.346,0c2.583,1.489,4.173,4.251,4.173,7.228v82.605h0.003
                        C232.612,194.284,231.022,197.041,228.439,198.529z" onclick="slowTrack()"/>
                </g>
            </g>
        </svg>
    </script>

    <?php
        echo "<script type='text/template' id='timestamps'>";        
        if ($recording['is_transcribed'] && ($recording['transcript_timestamps_path']!=NULL)){
            $timestamps = fopen($recording['transcript_timestamps_path'], "r");
            $i = 0;
            if($timestamps){
                while(!feof($timestamps)){
                    //echo fgets($timestamps);
                    $data = explode(" ",fgets($timestamps));
                    echo $data[1]."_".$data[3]."_".$data[5]."#";
                }
            } else{
                echo "error, unable to open file";
            }
            fclose($timestamps);
        }
        ?>
    </script>
    <script>
        var audioRecordingPath = <?php /*replace initial bit of path to make it public*/echo json_encode(str_replace("/var/www/quintaB.xyz/public_html","",$recording['mp3_path']), JSON_HEX_TAG); ?>; // Don't forget the extra semicolon!
    </script>
    <script src="main.js"></script>
</body>
</html>