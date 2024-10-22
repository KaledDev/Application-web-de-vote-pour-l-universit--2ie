<?php
// Connexion à la base de données
require_once '../../config/database.php';

// Requête pour récupérer tous les partis politiques
$query = "SELECT nom, urlImage FROM parties";
$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote - Université</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Toolbar -->
    <div class="toolbar">
        <div class="logo-container">
            <img src="/assets/images/logo2IE.png" alt="Logo de l'Université" class="logo">
        </div>
        <h2>Élection du Nouveau Bureau Exécutif</h2>
    </div>

    <div class="container">
        <header>
            <h1>Votez pour votre Parti Politique</h1>
            <p>Veuillez entrer votre code étudiant et sélectionner un parti pour voter.</p>
        </header>

        <form action="../../controllers/StudentController.php" method="POST" class="vote-form">
            <!-- Champ pour le code étudiant -->
            <div class="form-group">
                <label for="student_code">Code Étudiant :</label>
                <input type="text" id="student_code" name="codeEtudiant" required placeholder="Entrez votre code étudiant">
            </div>

            <!-- Section des partis politiques -->
            <div class="form-group parties-section">
                <label>Choisissez un Parti Politique :</label>
                <div class="parties-list">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="party">
                            <input type="radio" id="party_<?php echo $row['id']; ?>" name="party_id" value="<?php echo $row['id']; ?>" required>
                            <label for="party_<?php echo $row['id']; ?>">
                                <img src="/assets/images/<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>" class="party-img">
                                <p><?php echo $row['name']; ?></p>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <button type="submit" class="btn">Soumettre votre vote</button>
        </form>

        <footer>
            <a href="../admin/login.php" class="admin-link">Admin - Se connecter</a>
        </footer>
    </div>
</body>
</html>
