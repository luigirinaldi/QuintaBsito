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
            Upload Track
        </div>
    </div>
    <form action="upload_track.php" method="post" enctype="multipart/form-data">
        <h2>Upload new Track</h2>
        <label for="trackName">Name:</label><br>
        <input type="text" name="track_name" id="trackName"><br>
        <label for="trackSelectmp3">Track (mp3):</label>
        <input type="file" name="track_mp3" id="trackSelectmp3">
        <br>
        <label for="dateInfo">Date:</label>
        <input type="date" name="track_date" id="dateInfo">
        <br>  
        <label for="subject">Subject:</label>
        <select name="track_subject" id="subject">
            <?php                                
            //get subjects
            include_once './utils/database.util.php';
            $db = new Database;
            $db_conn = $db->getConnection();
            $query = "SELECT ID, name FROM Subjects";
            $result = $db_conn->query($query);                 
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                extract($row);                
                echo "<option value=".$ID.">".$name."</option>";                        
            }              
            ?>    
        </select><br>
        <label for="transcriptTimestamp">Transcript Timestamps:</label>
        <input type="file" name="track_trans_timestamps" id="transcriptTimestamp">
        <br>
        <label for="transcriptFull">Transcript Full:</label>
        <input type="file" name="track_trans_full" id="transcriptFull">
        <br>        
        <label for="silenced">Is it Silenced?</label>
        <input type="checkbox" id="silenced" name="is_silenced">
        <br>
        <input type="submit" name="uploadSubmit" value="Upload">
        <p><strong>Note:</strong> Only .mp3, .m4a, .wav formats allowed</p>
    </form>
    </div>
</body>
</html>