
<?php
session_start();
require __DIR__ . '/baza.php';

$blad = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $z = $pdo->prepare(
        'SELECT login, haslo_hash FROM uzytkownicy WHERE login = :login'
    );

    $z->execute([
        'login' => trim($_POST['login'] ?? '')
    ]);

    $u = $z->fetch();

    if ($u && password_verify($_POST['haslo'] ?? '', $u['haslo_hash'])) {
        session_regenerate_id(true);
        $_SESSION['uzytkownik'] = $u['login'];

        header('Location: admin.php');
        exit;
    }

    $blad = 'Błędny login lub hasło.';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie | Szprycha</title>

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
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm login-card">
                <div class="card-body p-4">
                    <p class="login-logo mb-2">szprycha.</p>
                    <h1 class="h3 mb-4">Logowanie — Szprycha</h1>

                    <?php if ($blad): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($blad) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input type="text" id="login" name="login"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="haslo" class="form-label">Hasło</label>
                            <input type="password" id="haslo" name="haslo"
                                   class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Zaloguj się
                        </button>
                    </form>

                    <p class="mt-3 mb-0">
                        <a href="index.php">Powrót na stronę główną</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
