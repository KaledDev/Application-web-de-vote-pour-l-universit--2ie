<?php
session_start(); // Démarrer la session
require_once '../../config/database.php';

// Initialiser une variable pour les messages d'erreur
$errorMessage = '';
$successMessage = '';

// Vérifier si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Requête pour vérifier si l'utilisateur existe
        $query = "SELECT * FROM admins WHERE username = :username";
        $stmt = $pdo->prepare($query);
        
        // Lier le paramètre
        $stmt->bindParam(':username', $username);
        
        // Exécuter la requête
        $stmt->execute();
        
        // Récupérer le résultat
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'administrateur existe et si le mot de passe est correct
        if ($admin && password_verify($password, $admin['password'])) {
            // L'administrateur est authentifié
            $successMessage = "Connexion réussie.";
            // Optionnel : stocker des informations sur l'utilisateur dans la session
            $_SESSION['admin'] = $admin; // Si besoin d'autres informations
            header('Location: ../admin/dashboard.php');
            exit(); 
        } else {
            $errorMessage = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Erreur lors de la connexion : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">Connexion Administrateur</h3>
        
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST"> <!-- Note: l'action est vide pour soumettre à la même page -->
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>
</div>

<!-- Inclusion de Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
