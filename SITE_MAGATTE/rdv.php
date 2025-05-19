<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $date_naissance = filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $date_rdv = filter_input(INPUT_POST, 'date_rdv', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $numero_registre = filter_input(INPUT_POST, 'numero_registre', FILTER_SANITIZE_STRING);

    if ($nom && $date_naissance && $telephone && $email && $date_rdv && $service) {
        try {
            $stmt = $pdo->prepare("INSERT INTO rendezvous 
                (nom_complet, date_naissance, telephone, email, date_rdv, service, numero_registre, date_enregistrement, statut)
                VALUES (:nom, :naissance, :tel, :email, :rdv, :service, :registre, NOW(), 'en attente')");

            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':naissance', $date_naissance, PDO::PARAM_STR);
            $stmt->bindParam(':tel', $telephone, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':rdv', $date_rdv, PDO::PARAM_STR);
            $stmt->bindParam(':service', $service, PDO::PARAM_STR);
            $stmt->bindParam(':registre', $numero_registre, PDO::PARAM_STR);
            $stmt->execute();

            $message = "<p style='color:green;'>Votre demande de rendez-vous a été enregistrée avec succès.</p>";
        } catch (PDOException $e) {
            error_log("Erreur insertion RDV : " . $e->getMessage());
            $message = "<p style='color:red;'>Erreur lors de l'enregistrement. Veuillez réessayer plus tard.</p>";
        }
    } else {
        $message = "<p style='color:red;'>Veuillez remplir tous les champs correctement.</p>";
    }
}
?>

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prendre un rendez-vous</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!--  Menu de navigation -->
    <nav class="navbar">
        <a href="index.html">Accueil</a>
        <a href="connexion.php">Connexion</a>
        <a href="mes_rendezvous.php">Mes rendez-vous</a>
    </nav>

    <main>
        <h2>Formulaire de rendez-vous</h2>
        <?= $message ?>
        <form method="POST">
            <label>Nom complet:</label><br>
            <input type="text" name="nom" required><br><br>

            <label>Date de naissance:</label><br>
            <input type="date" name="date_naissance" required><br><br>

            <label>Téléphone:</label><br>
            <input type="text" name="telephone" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Date du rendez-vous:</label><br>
            <input type="date" name="date_rdv" required><br><br>

            <label>Service souhaité:</label><br>
            <input type="text" name="service" required><br><br>

            <label>Numéro de registre:</label><br>
            <input type="text" name="numero_registre"><br><br>

            <button type="submit">Soumettre</button>
        </form>
    </main>
</body>
</html>

