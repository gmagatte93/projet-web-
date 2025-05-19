<?php
// Sécurisation session
date_default_timezone_set("America/Toronto"); 
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $motdepasse = filter_input(INPUT_POST, "motdepasse");

    if ($email && $motdepasse) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE courriel = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($motdepasse, $user['mot_de_passe'])) {
            // Infos session
            $_SESSION["utilisateur"] = $user["courriel"];
            $_SESSION["utilisateur_id"] = $user["id"];

            // Journalisation connexion réussie
            file_put_contents("logs/acces-reussis.log", date("Y-m-d H:i:s") . " - Succès : $email\n", FILE_APPEND);

            // Génération code 2FA
            $code = rand(100000, 999999);
            $_SESSION["code_2fa"] = $code;

            // Simuler l'envoi du code (dans un log temporaire)
            file_put_contents("logs/code-2fa.txt", "Code pour $email : $code\n", FILE_APPEND);

            // Rediriger vers page de vérification
            header("Location: verification.php");
            exit;
        } else {
            $erreur = "Identifiants incorrects.";

            // Journalisation échec
            file_put_contents("logs/acces-refuses.log", date("Y-m-d H:i:s") . " - Échec : $email\n", FILE_APPEND);
        }
    } else {
        $erreur = "Champs invalides.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.html">Accueil</a>
        <a href="register.php">Inscription</a> 
        <a href="mes_rendezvous.php">Prendre un RDV</a>
    </nav>

    <main>
        <?php if (!empty($erreur)): ?>
            <p class="error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Adresse courriel" required><br><br>
            <input type="password" name="motdepasse" placeholder="Mot de passe" required><br><br>
            <button type="submit">Se connecter</button>
        </form>
    </main>
</body>
</html>
