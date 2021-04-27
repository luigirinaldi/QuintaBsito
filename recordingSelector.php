<?php include_once './header.php' ?>
    <link rel="stylesheet" href="./css/home.css">
</head>
<body class="background">
    <div class="centercontainer">
        <div class="header">
            <a href="index.php">
                <div class="backBttn">
                    <?php include("Icons/backArrow1.svg") ?>
                </div>
            </a>
            <div class="header-title">
                <?php
                echo $_GET['subjectName'];                
                ?>
            </div>
        </div>
        <?php
        #fetch list of recordings for that subject from db
        #each recording contains mp3 file, full audio transcript,only text transcript and small desc txt file
        include 'db_connection.php';
        $connection = OpenCon();
        $queryGetRecordings = sprintf('SELECT * FROM recordings WHERE recordings.subj_ID = %s ORDER BY recordings.date ASC',$_GET['subjectID']);
        $recordings =  mysqli_query($connection,$queryGetRecordings)->fetch_all(MYSQLI_ASSOC);
        $queryGetSubjInfo = sprintf('SELECT `prof_name` FROM Subjects WHERE Subjects.ID = %s',$_GET['subjectID']);
        $profName =  mysqli_query($connection,$queryGetSubjInfo)->fetch_all(MYSQLI_ASSOC)[0]['prof_name'];
        foreach($recordings as $rec){
            #set link to player attaching name of folder to explore
            $name = $rec['name'];
            $linkTag = sprintf("<a href='player.php?recordingID=%s'>",$rec['ID']);
            echo $linkTag;
            ?>
            <div class='listElement'>
                <?php                 
                echo "<div class='name'>";
                echo $rec['name']; 
                echo "</div>";
                echo "<div class='details'>";
                echo "<div class='.profText'>".$profName."</div> <div class='date'>".$rec['date']."</div>";
                echo "</div>";      
                if ($rec['is_transcribed']){
                    if ($rec['transcript_path']!=NULL){
                        $transcriptFile = fopen($rec['transcript_path'], "r");
                        if ($transcriptFile){                    
                            $textData = fgets($transcriptFile);
                        }
                        fclose($transcriptFile);

                        echo "<div class='text'>";
                        echo $textData;
                        echo "</div>";
                    }
                }   else {
                    echo "recording is not yet Transcribed";
                }

                ?>
            </div>   
            </a>         
            <?php
        }
        ?>
    </div>
</body>
</html>