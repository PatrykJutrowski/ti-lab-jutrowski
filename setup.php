
<?php

require __DIR__ . '/baza.php';

// Tabela usług
$pdo->exec('
    CREATE TABLE IF NOT EXISTS uslugi (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nazwa TEXT NOT NULL,
        cena INTEGER NOT NULL
    )
');

// Tabela użytkowników
$pdo->exec('
    CREATE TABLE IF NOT EXISTS uzytkownicy (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        login TEXT NOT NULL UNIQUE,
        haslo_hash TEXT NOT NULL
    )
');

// Dodaj usługi tylko wtedy, gdy tabela jest pusta
$liczbaUslug = (int) $pdo
    ->query('SELECT COUNT(*) FROM uslugi')
    ->fetchColumn();

if ($liczbaUslug === 0) {
    $uslugi = [
        ['Przegląd roweru', 120],
        ['Regulacja przerzutek', 60],
        ['Wymiana dętki', 40],
        ['Regulacja hamulców', 50],
        ['Czyszczenie napędu', 80]
    ];

    $z = $pdo->prepare(
        'INSERT INTO uslugi (nazwa, cena) VALUES (:nazwa, :cena)'
    );

    foreach ($uslugi as $usluga) {
        $z->execute([
            'nazwa' => $usluga[0],
            'cena' => $usluga[1]
        ]);
    }
}

// Konto administratora — hasło podawane w terminalu
$liczbaKont = (int) $pdo
    ->query('SELECT COUNT(*) FROM uzytkownicy')
    ->fetchColumn();

if ($liczbaKont === 0) {
    if (PHP_SAPI !== 'cli') {
        exit('Uruchom setup.php w terminalu.');
    }

    echo "Podaj hasło administratora: ";
    $haslo = trim(fgets(STDIN));

    if (strlen($haslo) < 12) {
        exit("Hasło musi mieć co najmniej 12 znaków.\n");
    }

    $z = $pdo->prepare(
        'INSERT INTO uzytkownicy (login, haslo_hash)
         VALUES (:login, :hash)'
    );

    $z->execute([
        'login' => 'admin',
        'hash' => password_hash($haslo, PASSWORD_DEFAULT)
    ]);
}

echo "Baza gotowa.\n";
