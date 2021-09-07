<?php 
include('dbconfig.php');

$postsToShow = "";
$message = '';

$query = "Select * from content_text";
        // TODO: Query vorbereiten mit prepare();
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        
        // TODO: query ausführen mit execute();
        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        }
        // Kein Fehler
        if (empty($error)) {
            $message .= "Postquery erfolgreich<br/ >";
        }
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {

            //$postCount = $row['id'];
            $user = $row['iduser'];

            $postsToShow .= "<h3>" . $user . ": " . $row['text'] . "</h3><br />";
            
        }
        $myPosts = $result->fetch_assoc();
        $stmt -> close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <title>Clubm8</title>
</head>

<body>
    <div class="topnav">
        <form action="index.php" method="post">
            <a class="active" href="index.php">Home</a>
            <a href="registry.php">Registry</a>
            <a href="login.php">Login</a>
            <a href="admin.php">Benutzeroberfläche</a>
            <a href="about.php">About</a>
        </form>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-9 pt5">
                <h1 style="color: purple">Clubm8</h1>
                <br>
                <br>
                <p style="color: red;">Clubm8 hilft dir den nächsten Rave zu finden!</p>
                <p style="color: red;">Tausche dich aus und sei ein Clubm8</p>
                <br>
                <?php echo $postsToShow ?>
                <img src="images/KWZ.jpg" width="300" height="200"> |
                <img src="images/nordstern_venue_pic.jpg" width="300" height="200"> 
                <img src="images/Download.jpg" width="300" height="200"> |
                <img src="images/FB_IMG_1525024168798-01.jpeg" width="300" height="200">
            </div>
        </div>
    </div>

</body>

</html>