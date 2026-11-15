
<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/baza.php';

$blad = '';

// Obsługa formularzy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    if ($akcja === 'dodaj') {
        $nazwa = trim($_POST['nazwa'] ?? '');
        $cena = filter_var($_POST['cena'] ?? null, FILTER_VALIDATE_INT);

        if ($nazwa === '' || mb_strlen($nazwa) > 100 || $cena === false || $cena < 1) {
            $blad = 'Podaj poprawną nazwę i cenę większą od zera.';
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

    if ($akcja === 'usun') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id !== false && $id > 0) {
            $z = $pdo->prepare('DELETE FROM uslugi WHERE id = :id');
            $z->execute(['id' => $id]);

            header('Location: admin.php?ok=usunieto');
            exit;
        }

        $blad = 'Nieprawidłowy identyfikator usługi.';
    }
}

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

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="admin-style.css">
</head>
<body class="admin-page">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Panel CMS — Szprycha</h1>
            <p class="text-muted mb-0">
                Witaj, <?= htmlspecialchars($_SESSION['uzytkownik']) ?>!
            </p>
        </div>

        <a href="wyloguj.php" class="btn btn-outline-secondary">
            Wyloguj się
        </a>
    </div>

    <?php if (($_GET['ok'] ?? '') === 'dodano'): ?>
        <div class="alert alert-success">Usługa została dodana.</div>
    <?php elseif (($_GET['ok'] ?? '') === 'usunieto'): ?>
        <div class="alert alert-success">Usługa została usunięta.</div>
    <?php endif; ?>

    <?php if ($blad): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($blad) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">Lista usług</h2>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usługa</th>
                                    <th>Cena</th>
                                    <th>Akcja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($uslugi as $usluga): ?>
                                    <tr>
                                        <td><?= (int) $usluga['id'] ?></td>
                                        <td><?= htmlspecialchars($usluga['nazwa']) ?></td>
                                        <td><?= (int) $usluga['cena'] ?> zł</td>
                                        <td>
                                            <form method="post"
                                                  onsubmit="return confirm('Usunąć tę usługę?');">
                                                <input type="hidden" name="akcja" value="usun">
                                                <input type="hidden" name="id"
                                                       value="<?= (int) $usluga['id'] ?>">
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger">
                                                    Usuń
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">Dodaj usługę</h2>

                    <form method="post">
                        <input type="hidden" name="akcja" value="dodaj">

                        <div class="mb-3">
                            <label for="nazwa" class="form-label">Nazwa usługi</label>
                            <input type="text" id="nazwa" name="nazwa"
                                   class="form-control" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="cena" class="form-label">Cena (zł)</label>
                            <input type="number" id="cena" name="cena"
                                   class="form-control" min="1" step="1" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Dodaj usługę
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-4">
        <a href="index.php">← Powrót na stronę główną</a>
    </p>
</div>

</body>
</html>
