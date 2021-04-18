<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quinta Bs</title>
    <link rel="stylesheet" media="screen and (max-width:800px)" href="phoneStyle.css">
    <link rel="stylesheet" media="screen and (min-width:800px)" href="desktopStyle.css">
    <link rel="stylesheet" href="home.css">
</head>
<body class="background">
    <div class="centercontainer">
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