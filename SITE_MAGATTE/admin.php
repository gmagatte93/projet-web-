<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['utilisateur_id'])) {
    echo "Accès refusé. Vous devez être connecté.";
    exit();
}

$emailConnecte = $_SESSION['utilisateur'];
$utilisateur_id = $_SESSION['utilisateur_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a href="index.html">Accueil</a>
    <a href="mes_rendezvous.php">Mes rendez-vous</a>
    <a href="logout.php">Déconnexion</a>
</nav>

<main>
<?php
if ($emailConnecte === 'gueye@420.techinfo') {
    if (isset($_GET['accepter'])) {
        $id = filter_input(INPUT_GET, 'accepter', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare("UPDATE rendezvous SET statut_id = 2 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    if (isset($_GET['refuser'])) {
        $id = filter_input(INPUT_GET, 'refuser', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare("UPDATE rendezvous SET statut_id = 3 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    echo "<h2>Liste des rendez-vous (admin)</h2>";

    $stmt = $pdo->query("SELECT r.*, u.courriel, s.nom AS service_nom, st.nom AS statut_nom
                         FROM rendezvous r
                         JOIN utilisateurs u ON r.id_utilisateur = u.id
                         JOIN services s ON r.service_id = s.id
                         JOIN statuts st ON r.statut_id = st.id
                         ORDER BY r.date_enregistrement DESC");

    while ($rdv = $stmt->fetch()) {
        echo "<div class='rdv'>";
        echo "<strong>Nom :</strong> " . htmlspecialchars($rdv['nom_complet']) . "<br>";
        echo "<strong>Email :</strong> " . htmlspecialchars($rdv['courriel']) . "<br>";
        echo "<strong>Date RDV :</strong> " . htmlspecialchars($rdv['date_rdv']) . "<br>";
        echo "<strong>Service :</strong> " . htmlspecialchars($rdv['service_nom']) . "<br>";
        echo "<strong>Statut :</strong> " . htmlspecialchars($rdv['statut_nom']) . "<br>";

        if ($rdv['statut_id'] != 2) {
            echo "<a href='admin.php?accepter={$rdv['id']}'>Accepter</a> ";
        }
        if ($rdv['statut_id'] != 3) {
            echo "| <a href='admin.php?refuser={$rdv['id']}'>Refuser</a>";
        }
        echo "</div>";
    }

} else {
    $stmt = $pdo->prepare("SELECT r.*, s.nom AS service_nom, st.nom AS statut_nom
        FROM rendezvous r
        JOIN services s ON r.service_id = s.id
        JOIN statuts st ON r.statut_id = st.id
        WHERE r.id_utilisateur = :id ORDER BY r.date_enregistrement DESC");
    $stmt->bindParam(':id', $utilisateur_id);
    $stmt->execute();
    $rendezvous = $stmt->fetchAll();

    echo "<h2>Mes rendez-vous</h2>";

    foreach ($rendezvous as $rdv) {
        echo "<div class='rdv'>";
        echo "<strong>Date RDV :</strong> " . htmlspecialchars($rdv['date_rdv']) . "<br>";
        echo "<strong>Service :</strong> " . htmlspecialchars($rdv['service_nom']) . "<br>";
        echo "<strong>Statut :</strong> " . htmlspecialchars($rdv['statut_nom']) . "<br>";
        echo "</div>";
    }
}
?>
</main>
</body>
</html>
