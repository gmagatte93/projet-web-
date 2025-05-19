<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

require 'connexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_complet = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $date_naissance = filter_input(INPUT_POST, 'naissance', FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $date_rdv = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $numero_registre = filter_input(INPUT_POST, 'registre', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($nom_complet && $date_naissance && $telephone && $email && $date_rdv && $numero_registre && $service) {
        try {
            $sql = "INSERT INTO rendezvous (nom_complet, date_naissance, telephone, email, date_rdv, numero_registre, service)
                    VALUES (:nom, :naissance, :tel, :email, :date, :registre, :service)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom_complet, PDO::PARAM_STR);
            $stmt->bindParam(':naissance', $date_naissance, PDO::PARAM_STR);
            $stmt->bindParam(':tel', $telephone, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date_rdv, PDO::PARAM_STR);
            $stmt->bindParam(':registre', $numero_registre, PDO::PARAM_STR);
            $stmt->bindParam(':service', $service, PDO::PARAM_STR);
            $stmt->execute();

            echo "<p style='color:green;'>Rendez-vous enregistré avec succès.</p>";
        } catch (PDOException $e) {
            error_log("Erreur lors de l'enregistrement : " . $e->getMessage());
            echo "<p style='color:red;'>Une erreur est survenue. Veuillez réessayer plus tard.</p>";
        }
    } else {
        echo "<p style='color:red;'>Tous les champs sont requis et doivent être valides.</p>";
    }
}
?>
