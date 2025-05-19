<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'connexion.php';
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courriel = $_POST['courriel'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    if ($courriel && $mot_de_passe) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE courriel = ?");
        $stmt->execute([$courriel]);
        $utilisateur = $stmt->fetch();
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            header("Location: admin.php");
            exit();
        } else {
            $message = " Courriel ou mot de passe invalide.";
        }
    }
}
?>
<h2>Connexion</h2>
<form method="post">
  <input type="email" name="courriel" placeholder="Adresse courriel" required><br><br>
  <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br><br>
  <button type="submit">Se connecter</button>
</form>
<p><?php echo $message; ?></p>
