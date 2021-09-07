<?php

//Session startet
session_start();
session_regenerate_id(true);

//Datenbankverbindung
include('dbconfig.php');

//Variablen initialisieren 
$error = $message = '';
$text ='';
$myPostsToEdit = '';
$myPosts;
$postId = 0;
$newPost = '';

//Funktion um Querys auszuführen
function executeQuery($mysqli, $query, $dataTypes, $params) {
    $returnValue = [];
    $error = "";
    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
    }

    if(sizeof($params) > 0) {
        //Teilt Array in einzelne Parameter auf
        $stmt->bind_param($dataTypes, ...$params);
    }

    // TODO: query ausführen mit execute();
    if (!$stmt->execute()) {
        $error .= 'execute() failed ' . $mysqli->error . '<br />';
    }

    if (strpos(strtolower($query), "select") === false) {
        return [];
    }

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        //Array auffüllung
        array_push($returnValue, $row);
    }

    return $returnValue;
}


//Dynamische Postauflistung generieren
       
        $result = executeQuery($mysqli, "Select * from content_text where iduser = ?", 's', [$_SESSION['username']]);

        $myPosts = [];
        foreach ($result as $row) {
            $postId = $row['id'];
            $myPosts[$postId] = $row;
            //String wird zusammengefügt
            $myPostsToEdit .= '<div class="form-group\"
                                <label for="text">Text #' . $postId . '</label>
                                <input type="text" name="' . $postId . '" class="form-control" id="' . $postId . '" value="' . $row['text'] . '" placeholder="">
                            </div> 
                            <br />';
        }
    


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Ausgabe des gesamten $_POST Arrays
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";


    //TextEdit Vergleich Posts
    foreach($myPosts as $singleOldPost) {
        $id = $singleOldPost['id'];
        $newPost = trim(htmlspecialchars($_POST[$id]));

        if($newPost != $singleOldPost['text']) {
            if(trim(htmlspecialchars($newPost)) == '' || $newPost == null) {
                executeQuery($mysqli, "DELETE FROM content_text WHERE id = ?", 'i', [$id]);
            } else {
                //edit
                executeQuery($mysqli, "Update content_text set text = ? WHERE id = ?", 'si', [$newPost, $id]);
            }
        }
    }



    // Textupload
    if (isset($_POST['text'])) {
        //trim and sanitize
        $text = trim(htmlspecialchars($_POST['text']));
    } else {
        $error .= "Geben Sie einen gültigen Text ein.<br />";
    }

    // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank - Textupload
    if (empty($error)) {
        // Query = Datenabfrage in Datenbank
        // TODO: INPUT Query erstellen
        $query = "Insert into content_text (text, iduser) values (?, ?) ";
        // TODO: Query vorbereiten mit prepare();
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        // TODO: Parameter an Query binden mit bind_param(); Bindet Variablen als Parameter
        if (!$stmt->bind_param('ss', $text, $_SESSION['username'])) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        // TODO: query ausführen mit execute();
        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        }
        // Kein Fehler
        if (empty($error)) {
            $message .= "Die Daten wurden erfolgreich gespeichert<br/ >";
        }
        $stmt -> close();
    }

    //Bildupload
    if(isset($POST['image'])){
        //Versuch den Imageupload zum laufen zu bringen
        $image = file_get_contents($_FILES['image']['./']);
    }

    // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank
    if (empty($error)) {
        // Query = Datenabfrage in Datenbank
        // TODO: INPUT Query erstellen
        $query = "Insert into content_image (image, iduser) values (?, ?) ";
        // TODO: Query vorbereiten mit prepare();
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        // TODO: Parameter an Query binden mit bind_param(); Bindet Variablen als Parameter
        if (!$stmt->bind_param('bs', /*$image*/ $POST['image'], $_SESSION['username'])) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        // TODO: query ausführen mit execute();
        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        }
        // Kein Fehler
        if (empty($error)) {
            $message .= "Bildupload erfolgreich<br/ >";
        }
        $stmt -> close();
        
        
    }
    $mysqli->close();
        header('location: admin.php');
}



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
    <title>Administrationsbereich</title>
</head>

<body>
<div class="topnav">
        <form action="index.php" method="post">
            <a  href="index.php">Home</a>
            <a href="registry.php">Registry</a>
            <a href="login.php">Login</a>
            <a class="active" href="admin.php">Benutzeroberfläche</a>
            <a href="about.php">About</a>
        </form>
</div>
    <h1>Adminpage</h1>
    <?php
    //Session prüfen
    if (isset($_SESSION['loggedin'])) {
        echo $message .= "Hallo " . $_SESSION['username'] . ", du bist angemeldet."; ?>
        <form action="" method="post">
            <!-- Text -->
            <div class="form-group">
                <label for="text">Text</label>
                <input type="text" name="text" class="form-control" id="text" value="<?php echo $text ?>" placeholder="Geben Sie Ihren Text ein." required="true">
            </div>
            <div class="col-md-12 col-xs-12" id="image" >
                              <input type="file" name="image" id="fileToUpload" accept=".jpeg,.jpg,.png,.pdf">
                              <input type="hidden" name="MAX_FILE_SIZE" value="4000000">
                    </div>

            <?php echo $myPostsToEdit?>
            <button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
            <button type="reset" name="button" value="reset" class="btn btn-warning">Löschen</button>
        </form>
        <a href="logout.php" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true">Logout</a>
        <a href="changepw.php" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true">Passwort ändern</a>
    <?php
    } else {
       echo $error .= "Sie sind nicht angemeldet, bitte <a href='login.php'>hier</a> anmelden!";
    }
    ?>


</body>

</html>