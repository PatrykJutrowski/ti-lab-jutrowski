
<?php
require __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administratora | Szprycha</title>
</head>
<body>
    <h1>Panel administratora</h1>

    <p>
        Witaj, <?= htmlspecialchars($_SESSION['uzytkownik']) ?>!
    </p>

    <p>Jesteś zalogowany do panelu Szprychy.</p>

    <a href="wyloguj.php">Wyloguj się</a>
</body>
</html>
