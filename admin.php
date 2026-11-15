
<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/baza.php';

$blad = '';

// Obsługa dodawania usługi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    if ($akcja === 'dodaj') {
        $nazwa = trim($_POST['nazwa'] ?? '');
        $cena = filter_var(
            $_POST['cena'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($nazwa === '' || mb_strlen($nazwa) > 100 || $cena === false || $cena < 1) {
            $blad = 'Podaj poprawną nazwę (maks. 100 znaków) i cenę większą od zera.';
        } else {
            $z = $pdo->prepare(
                'INSERT INTO uslugi (nazwa, cena) VALUES (:nazwa, :cena)'
            );

            $z->execute([
                'nazwa' => $nazwa,
                'cena' => $cena
            ]);

            header('Location: admin.php?ok=dodano');
            exit;
        }
    }
}

// Pobranie usług z bazy
$uslugi = $pdo->query(
    'SELECT id, nazwa, cena FROM uslugi ORDER BY id'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel CMS | Szprycha</title>
</head>
<body>
    <h1>Panel administratora — Szprycha</h1>

    <p>
        Witaj, <?= htmlspecialchars($_SESSION['uzytkownik']) ?>!
        <a href="wyloguj.php">Wyloguj się</a>
    </p>

    <?php if (($_GET['ok'] ?? '') === 'dodano'): ?>
        <p style="color: green;">Usługa została dodana.</p>
    <?php endif; ?>

    <?php if ($blad): ?>
        <p style="color: red;">
            <?= htmlspecialchars($blad) ?>
        </p>
    <?php endif; ?>

    <h2>Lista usług</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usługa</th>
                <th>Cena</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($uslugi as $usluga): ?>
                <tr>
                    <td><?= (int) $usluga['id'] ?></td>
                    <td><?= htmlspecialchars($usluga['nazwa']) ?></td>
                    <td><?= (int) $usluga['cena'] ?> zł</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Dodaj nową usługę</h2>

    <form method="post">
        <input type="hidden" name="akcja" value="dodaj">

        <p>
            <label for="nazwa">Nazwa usługi:</label>
            <input
                type="text"
                id="nazwa"
                name="nazwa"
                maxlength="100"
                required
            >
        </p>

        <p>
            <label for="cena">Cena (zł):</label>
            <input
                type="number"
                id="cena"
                name="cena"
                min="1"
                step="1"
                required
            >
        </p>

        <button type="submit">Dodaj usługę</button>
    </form>

    <p><a href="index.php">Zobacz stronę główną</a></p>
</body>
</html>
