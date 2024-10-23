<?php
require_once './config/database.php';

// Récupération des partis politiques
$query = "SELECT id, nom, urlimage FROM parties";
$result = $pdo->query($query);
$partis = $result->fetchAll(PDO::FETCH_ASSOC);

// Gestion de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codeEtudiant = $_POST['codeEtudiant'];
    $partiId = $_POST['parti'];

    // Vérification si l'étudiant a déjà voté
    $checkVoteQuery = "SELECT * FROM votes WHERE code_etudiant = :codeEtudiant";
    $stmt = $pdo->prepare($checkVoteQuery);
    $stmt->execute([':codeEtudiant' => $codeEtudiant]);
    
    if ($stmt->rowCount() > 0) {
        echo "<div class='alert alert-danger'>Vous avez déjà voté !</div>";
    } else {
        // Insertion du vote
        $insertVoteQuery = "INSERT INTO votes (code_etudiant, partie_id, voted_At) VALUES (:codeEtudiant, :partieId, NOW())";
        $stmt = $pdo->prepare($insertVoteQuery);
        
        if ($stmt->execute([':codeEtudiant' => $codeEtudiant, ':partieId' => $partiId])) {
            echo "<div class='alert alert-success'>Votre vote a été enregistré avec succès !</div>";
        } else {
            echo "<div class='alert alert-danger'>Une erreur s'est produite lors de l'enregistrement de votre vote.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote - Université</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-light bg-light p-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="./assets/images/logo2IE.png" alt="Logo de l'Université" class="logo" style="height: 80px;">
                Élection du Nouveau Bureau Exécutif
            </a>
            <a href="./views/admin/login.php" class="btn btn-outline-secondary">Admin - Se connecter</a>
        </div>
    </nav>

    <div class="container mt-5">
        <header class="text-center mb-4">
            <h1>Votez pour votre Parti Politique</h1>
            <p class="lead">Veuillez entrer votre code étudiant et sélectionner un parti pour voter.</p>
        </header>

        <form action="" method="POST" class="vote-form">
            <div class="mb-4">
                <label for="student_code" class="form-label">Code Étudiant :</label>
                <input type="text" id="student_code" name="codeEtudiant" class="form-control" required placeholder="Entrez votre code étudiant">
            </div>

            <div class="mb-4">
                <label class="form-label">Choisissez un Parti Politique :</label>
                <div class="row">
                    <?php foreach ($partis as $parti): ?>
                        <div class="col-md-4 mb-4 text-center">
                            <div class="card">
                                <img src="./views/admin/<?= htmlspecialchars($parti['urlimage']) ?>" class="card-img-top" alt="<?= htmlspecialchars($parti['nom']) ?>" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($parti['nom']) ?></h5>
                                    <input type="radio" id="parti_<?= $parti['id'] ?>" name="parti" value="<?= $parti['id'] ?>" required>
                                    <label for="parti_<?= $parti['id'] ?>" class="form-label">Sélectionner</label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary btn-lg">Soumettre votre vote</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
