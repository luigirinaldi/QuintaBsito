<?php include_once './header.php' ?>
    <link rel="stylesheet" href="./css/home.css">
</head>
<body class="background">
    <div class="centercontainer">
        <a href="upload_form.php"> 
            <div class="uploadBttn">
                <?php include("Icons/uploadIcon.svg") ?>
            </div>
        </a>

        <?php
        include 'db_connection.php';
        $connection = OpenCon();
        #display list of subjects from subjects in db
        $queryGetSubjects = 'SELECT `ID`,`name` FROM Subjects';
        $subjects = mysqli_query($connection,$queryGetSubjects)->fetch_all(MYSQLI_ASSOC);
        foreach($subjects as $subject){
            #set link to player attaching name of folder to explore
            $linkTag = sprintf("<a href='recordingSelector.php?subjectID=%s&subjectName=%s'>",$subject['ID'],$subject['name']);
            echo $linkTag;
            ?>
            <div class="subjList">
                <?php 
                    echo "<div class='subjTitle'>".$subject['name']."</div>";
                ?>
            </div>   
            </a>         
            <?php
        }
        closeCon($connection);
        ?>
    </div>
</body>
</html>