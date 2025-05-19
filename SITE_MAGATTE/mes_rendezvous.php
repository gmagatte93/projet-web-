<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start();

require_once 'config.php';

if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['utilisateur_id'])) {
    $_SESSION['retour_apres_login'] = $_SERVER['REQUEST_URI'];
    header("Location: connexion.php");
    exit();
}

$email = $_SESSION['utilisateur'];
$utilisateur_id = $_SESSION['utilisateur_id'];
$service_selectionne = filter_input(INPUT_GET, 'service', FILTER_SANITIZE_STRING);

$message = "";
if (isset($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $date_naissance = filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $date_rdv = filter_input(INPUT_POST, 'date_rdv', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $numero_registre = filter_input(INPUT_POST, 'numero_registre', FILTER_SANITIZE_STRING);

    if ($nom && $date_naissance && $telephone && $date_rdv && $service) {
        try {
            $req = $pdo->prepare("SELECT id FROM services WHERE nom = :nom");
            $req->bindParam(':nom', $service, PDO::PARAM_STR);
            $req->execute();
            $service_id = $req->fetchColumn();

            if (!$service_id) {
                $message = "<p class='error'>Service inconnu sélectionné.</p>";
            } else {
                $verif = $pdo->prepare("SELECT COUNT(*) FROM rendezvous 
                    WHERE id_utilisateur = :id AND date_rdv = :rdv AND service_id = :service");
                $verif->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);
                $verif->bindParam(':rdv', $date_rdv, PDO::PARAM_STR);
                $verif->bindParam(':service', $service_id, PDO::PARAM_INT);
                $verif->execute();
                $existe = $verif->fetchColumn();

                if ($existe > 0) {
                    $message = "<p class='error'>Vous avez déjà un rendez-vous pour ce service à cette date.</p>";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO rendezvous 
                        (nom_complet, date_naissance, telephone, id_utilisateur, date_rdv, service_id, numero_registre, date_enregistrement, statut_id)
                        VALUES (:nom, :naissance, :tel, :id_user, :rdv, :service, :registre, NOW(), 1)");

                    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                    $stmt->bindParam(':naissance', $date_naissance, PDO::PARAM_STR);
                    $stmt->bindParam(':tel', $telephone, PDO::PARAM_STR);
                    $stmt->bindParam(':id_user', $utilisateur_id, PDO::PARAM_INT);
                    $stmt->bindParam(':rdv', $date_rdv, PDO::PARAM_STR);
                    $stmt->bindParam(':service', $service_id, PDO::PARAM_INT);
                    $stmt->bindParam(':registre', $numero_registre, PDO::PARAM_STR);
                    $stmt->execute();

                    $_SESSION['flash'] = "<p class='success'>Rendez-vous enregistré avec succès.</p>";
                    header("Location: mes_rendezvous.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $message = "<p class='error'>Erreur lors de l'enregistrement. Veuillez réessayer plus tard.</p>";
        }
    } else {
        $message = "<p class='error'>Tous les champs requis doivent être valides.</p>";
    }
}

try {
    $stmt = $pdo->prepare("SELECT r.*, s.nom as service_nom, st.nom as statut_nom 
        FROM rendezvous r
        JOIN services s ON r.service_id = s.id
        JOIN statuts st ON r.statut_id = st.id
        WHERE r.id_utilisateur = :id
        ORDER BY r.date_enregistrement DESC");
    $stmt->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);
    $stmt->execute();
    $rendezvous = $stmt->fetchAll();
} catch (PDOException $e) {
    $rendezvous = [];
    $message .= "<p class='error'>Impossible d'afficher vos rendez-vous actuellement.</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes rendez-vous</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <span>Connecté en tant que : <strong><?= htmlspecialchars($email) ?></strong></span>
        <a href="index.html">Accueil</a>
        <a href="mes_rendezvous.php">Prendre un RDV</a>
        <a href="logout.php">Déconnexion</a>
    </nav>

    <main>
        <div class="form-container">
            <?= $message ?>

            <form method="post">
                <label>Nom complet :</label><br>
                <input type="text" name="nom" required><br><br>

                <label>Date de naissance :</label><br>
                <input type="date" name="date_naissance" required><br><br>

                <label>Téléphone :</label><br>
                <input type="text" name="telephone" required><br><br>

                <label>Date du rendez-vous :</label><br>
                <input type="date" name="date_rdv" required><br><br>

                <label>Service demandé :</label><br>
                <select name="service" required>
                    <option value="extrait_naissance" <?= $service_selectionne === 'extrait_naissance' ? 'selected' : '' ?>>Extrait de naissance</option>
                    <option value="bulletin_naissance" <?= $service_selectionne === 'bulletin_naissance' ? 'selected' : '' ?>>Bulletin de naissance</option>
                    <option value="certificat_celibat" <?= $service_selectionne === 'certificat_celibat' ? 'selected' : '' ?>>Certificat de célibat</option>
                    <option value="bulletin_deces" <?= $service_selectionne === 'bulletin_deces' ? 'selected' : '' ?>>Bulletin de décès</option>
                    <option value="certificat_mariage" <?= $service_selectionne === 'certificat_mariage' ? 'selected' : '' ?>>Certificat de mariage</option>
                    <option value="certificat_divorce" <?= $service_selectionne === 'certificat_divorce' ? 'selected' : '' ?>>Certificat de divorce</option>
                    <option value="extrait_reconnaissance" <?= $service_selectionne === 'extrait_reconnaissance' ? 'selected' : '' ?>>Extrait de reconnaissance</option>
                    <option value="certificat_residence" <?= $service_selectionne === 'certificat_residence' ? 'selected' : '' ?>>Certificat de résidence</option>
                </select><br><br>

                <label>Numéro de registre :</label><br>
                <input type="text" name="numero_registre"><br><br>

                <button type="submit">Envoyer</button>
            </form>
        </div>

        <div class="form-container">
            <?php if (count($rendezvous) === 0): ?>
                <p>Aucun rendez-vous enregistré.</p>
            <?php else: ?>
                <?php foreach ($rendezvous as $rdv): ?>
                    <div class="rdv">
                        <strong>Date RDV :</strong> <?= htmlspecialchars($rdv['date_rdv']) ?><br>
                        <strong>Service :</strong> <?= htmlspecialchars($rdv['service_nom']) ?><br>
                        <strong>Statut :</strong>
                        <span class="statut <?= htmlspecialchars($rdv['statut_nom']) ?>">
                            <?= htmlspecialchars($rdv['statut_nom']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
