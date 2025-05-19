<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

require_once 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname  = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email     = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone     = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

    if ($firstname && $lastname && $email && $phone) {
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, courriel, telephone)
                                   VALUES (:prenom, :nom, :courriel, :telephone)");
            $stmt->bindParam(':prenom', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $lastname, PDO::PARAM_STR);
            $stmt->bindParam(':courriel', $email, PDO::PARAM_STR);
            $stmt->bindParam(':telephone', $phone, PDO::PARAM_STR);
            $stmt->execute();

            echo "<h2>Inscription réussie !</h2>";
            echo "<p>Merci pour votre inscription, " . htmlspecialchars($firstname) . " " . htmlspecialchars($lastname) . ".</p>";
            echo "<p>Email : " . htmlspecialchars($email) . "<br> Téléphone : " . htmlspecialchars($phone) . "</p>";
        } catch (PDOException $e) {
            error_log("Erreur SQL : " . $e->getMessage());
            echo "<p style='color:red;'>Une erreur est survenue. Veuillez réessayer plus tard.</p>";
        }
    } else {
        echo "<p style='color:red;'>Tous les champs sont requis et doivent être valides.</p>";
    }
}
?>
