<?php

//Session starten
session_start();
session_regenerate_id(true);

//Datenbankverbindung
include('dbconfig.php');

$error = '';
$message = '';


// Formular wurde gesendet und Besucher ist noch nicht angemeldet.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    // username
    if (isset($_POST['username'])) {
        //trim
        $username = trim($_POST['username']);

        // prüfung benutzername
        if (empty($username) || !preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,30}/", $username)) {
            $error .= "Der Benutzername entspricht nicht dem geforderten Format.<br />";
        }
    } else {
        $error .= "Geben Sie bitte den Benutzername an.<br />";
    }
    // password
    if (isset($_POST['password'])) {
        //trim
        $password = trim($_POST['password']);
        // passwort gültig?
        if (empty($password) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
            $error .= "Das Passwort entspricht nicht dem geforderten Format.<br />";
        }
    } else {
        $error .= "Geben Sie bitte das Passwort an.<br />";
    }

    // kein fehler
    if (empty($error)) {
        // TODO SELECT Query erstellen, user und passwort mit Datenbank vergleichen
        $query = "SELECT username, password from users where username = ?";
        // TODO prepare()
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $error .= 'prepare() failed ' . $mysqli->error . '<br />';
        }
        // TODO bind_param()
        if (!$stmt->bind_param("s", $username)) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }

        // TODO execute()
        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        }
        // TODO Passwort auslesen und mit dem eingegeben Passwort vergleichen
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {

            // TODO: wenn Passwort korrekt:  $message .= "Sie sind nun eingeloggt"; 
            if(password_verify($password, $row['password'])){
                $message .= "Sie sind nun eingeloggt";

                //Sessionvariablen schreiben
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['username'];

                header("Location: admin.php");

                // TODO: wenn Passwort falsch, oder kein Benutzer mit diesem Benutzernamem in DB: $error .= "Benutzername oder Passwort sind falsch";
            } else {
                $error .= "Benutzername oder Password sind falsch";
            }
        }
    }
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
   
    <title>Registrierung</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="topnav">
        <form action="index.php" method="post">
            <a href="index.php">Home</a>
            <a href="registry.php">Registry</a>
            <a class="active" href="login.php">Login</a>
            <a href="admin.php">Benutzeroberfläche</a>
            <a href="about.php">About</a>
        </form>
    </div>
    <div class="container">
        <h1>Login</h1>
        <p>
            Bitte melden Sie sich mit Benutzernamen und Passwort an.
        </p>
        <?php
        // fehlermeldung oder nachricht ausgeben
        if (!empty($message)) {
            echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        } else if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        }
        ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Benutzername *</label>
                <input type="text" name="username" class="form-control" id="username" value="" placeholder="Gross- und Keinbuchstaben, min 6 Zeichen." pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}" title="Gross- und Keinbuchstaben, min 6 Zeichen." maxlength="30" required="true">
            </div>
            <!-- password -->
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute" pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 8 Zeichen lang,keine Umlaute." maxlength="255" required="true">
            </div>
            <button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
            <button type="reset" name="button" value="reset" class="btn btn-warning">Löschen</button>
        </form>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>

</html>