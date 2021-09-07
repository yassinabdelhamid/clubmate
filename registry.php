<?php

// TODO: Verbindung zur Datenbank einbinden
include('dbconfig.php');

// Initialisierung
$error = $message =  '';
$firstname = $lastname = $email = $username = '';

// Wurden Daten mit "POST" gesendet?
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Ausgabe des gesamten $_POST Arrays
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // vorname ausgefüllt?
    if (isset($_POST['firstname'])) {
        //trim and sanitize
        $firstname = trim(htmlspecialchars($_POST['firstname']));

        //mindestens 1 Zeichen und maximal 30 Zeichen lang
        if (empty($firstname) || strlen($firstname) > 30) {
            $error .= "Geben Sie bitte einen korrekten Vornamen ein.<br />";
        }
    } else {
        $error .= "Geben Sie bitte einen Vornamen ein.<br />";
    }

    // nachname ausgefüllt?
    if (isset($_POST['lastname'])) {
        //trim and sanitize
        $lastname = trim(htmlspecialchars($_POST['lastname']));

        //mindestens 1 Zeichen und maximal 30 Zeichen lang
        if (empty($lastname) || strlen($lastname) > 30) {
            $error .= "Geben Sie bitte einen korrekten Nachname ein.<br />";
        }
    } else {
        $error .= "Geben Sie bitte einen Nachname ein.<br />";
    }

    // email ausgefüllt?
    if (isset($_POST['email'])) {
        //trim
        $email = trim($_POST['email']);

        //mindestens 1 Zeichen und maximal 100 Zeichen lang, gültige Emailadresse
        if (empty($email) || strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $error .= "Geben Sie bitte eine korrekten Emailadresse ein.<br />";
        }
    } else {
        $error .= "Geben Sie bitte eine Emailadresse ein.<br />";
    }

    // username ausgefüllt?
    if (isset($_POST['username'])) {
        //trim and sanitize
        $username = trim($_POST['username']);

        //mindestens 1 Zeichen , entsprich RegEX
        if (empty($username) || !preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,30}/", $username)) {
            $error .= "Geben Sie bitte einen korrekten Usernamen ein.<br />";
        }
    } else {
        $error .= "Geben Sie bitte einen Username ein.<br />";
    }

    // passwort ausgefüllt
    if (isset($_POST['password'])) {
        //trim and sanitize
        $password = trim($_POST['password']);

        $password = password_hash($password, PASSWORD_DEFAULT);

        //mindestens 1 Zeichen , entsprich RegEX
        if (empty($password) || !preg_match("/(?=^.{8,255}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
            $error .= "Geben Sie bitte einen korrektes Password ein.<br />";
        }
    } else {
        $error .= "Geben Sie bitte ein Password ein.<br />";
    }

    // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank
    if (empty($error)) {
        // Query = Datenabfrage in Datenbank
        // TODO: INPUT Query erstellen, welches firstname, lastname, username, password, email in die Datenbank schreibt
        $query = "Insert into users (firstname, lastname, username, password, email) values (?, ?, ?, ?, ?) ";
        // TODO: Query vorbereiten mit prepare();
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        // TODO: Parameter an Query binden mit bind_param(); Bindet Variablen als Parameter
        if (!$stmt->bind_param('sssss', $firstname, $lastname, $username, $password, $email)) {
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
        // TODO: Verbindung schliessen
        $mysqli->close();
        // TODO: Weiterleitung auf login.php
        header('location: login.php');
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
    <title>Clubm8</title>
</head>

<body>
    <div class="topnav">
        <form action="index.php" method="post">
            <a href="index.php">Home</a>
            <a class="active" href="registry.php">Registry</a>
            <a href="login.php">Login</a>
            <a href="admin.php">Benutzeroberfläche</a>
            <a href="about.php">About</a>
        </form>
    </div>
    <div class="container">
        <h1>Registrierung</h1>
        <p>
            Bitte registrieren Sie sich, damit Sie diesen Dienst benutzen können.
        </p>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)) {
            echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
        ?>
        <form action="" method="post">
            <!-- vorname -->
            <div class="form-group">
                <label for="firstname">Vorname *</label>
                <input type="text" name="firstname" class="form-control" id="firstname" value="<?php echo $firstname ?>" placeholder="Geben Sie Ihren Vornamen an." maxlength="30" required="true">
            </div>
            <!-- nachname -->
            <div class="form-group">
                <label for="lastname">Nachname *</label>
                <input type="text" name="lastname" class="form-control" id="lastname" value="<?php echo $lastname ?>" placeholder="Geben Sie Ihren Nachnamen an" maxlength="30" required="true">
            </div>
            <!-- email -->
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" class="form-control" id="email" value="<?php echo $email ?>" placeholder="Geben Sie Ihre Email-Adresse an." maxlength="100" required="true">
            </div>
            <!-- benutzername -->
            <div class="form-group">
                <label for="username">Benutzername *</label>
                <input type="text" name="username" class="form-control" id="username" value="<?php echo $username ?>" placeholder="Gross- und Keinbuchstaben, min 6 Zeichen." pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}" title="Gross- und Keinbuchstaben, min 6 Zeichen." maxlength="30" required="true">
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