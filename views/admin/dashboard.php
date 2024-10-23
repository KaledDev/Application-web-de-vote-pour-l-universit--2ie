<?php
include '../../config/database.php';

// Créer un parti
if (isset($_POST['create'])) {
    $nom = $_POST['nom'];

    // Vérifier si une image a été téléchargée
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploads_dir = 'uploads/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }

        $tmp_name = $_FILES['image']['tmp_name'];
        $name = basename($_FILES['image']['name']);
        $file_path = $uploads_dir . $name;

        if (move_uploaded_file($tmp_name, $file_path)) {
            // Insérer le parti dans la base de données avec le chemin de l'image
            $stmt = $pdo->prepare("INSERT INTO parties (nom, urlimage) VALUES (?, ?)");
            $stmt->execute([$nom, $file_path]);

            // Rediriger pour éviter la resoumission
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erreur lors de l'upload de l'image.";
        }
    } else {
        echo "Aucune image téléchargée ou une erreur est survenue.";
    }
}

// Supprimer un parti
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM parties WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: dashboard.php");
    exit();
}

// Modifier un parti
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $current_image = $_POST['current_image'];

    // Vérifier si une nouvelle image a été téléchargée
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploads_dir = 'uploads/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }

        $tmp_name = $_FILES['image']['tmp_name'];
        $name = basename($_FILES['image']['name']);
        $file_path = $uploads_dir . $name;

        if (move_uploaded_file($tmp_name, $file_path)) {
            // Mettre à jour le parti avec la nouvelle image
            $stmt = $pdo->prepare("UPDATE parties SET nom = ?, urlimage = ? WHERE id = ?");
            $stmt->execute([$nom, $file_path, $id]);
        } else {
            echo "Erreur lors de l'upload de l'image.";
        }
    } else {
        // Si aucune nouvelle image n'est téléchargée, conserver l'image actuelle
        $stmt = $pdo->prepare("UPDATE parties SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $id]);
    }

    header("Location: dashboard.php");
    exit();
}

// Récupérer tous les partis avec le nombre d'électeurs
$stmt = $pdo->query("
    SELECT p.*, COUNT(v.partie_id) AS nombre_electeurs 
    FROM parties p 
    LEFT JOIN votes v ON p.id = v.partie_id 
    GROUP BY p.id
");
$partis = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Partis politiques</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-light bg-light p-3">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../../assets/images/logo2IE.png" alt="Logo" style="height: 80px;">
            Élection du Nouveau Bureau Exécutif - Interface Administrateur
        </a>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Administrateur
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#">Paramètres</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="logout.php">
                        <button class="dropdown-item" type="submit">Se déconnecter</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="mb-4 text-center">Gestion des élections</h1>

    <!-- Formulaire de création -->
    <h2>Créer un Parti</h2>
    <form method="POST" action="" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <input type="text" class="form-control" name="nom" placeholder="Nom du Parti" required>
        </div>
        <div class="mb-3">
            <input type="file" class="form-control" name="image" accept="image/*" required>
        </div>
        <button type="submit" name="create" class="btn btn-primary">Créer</button>
    </form>

    <!-- Liste des partis -->
    <h2>Liste des Partis Politiques</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Logo</th>
                <th>Nombre d'Électeurs</th> <!-- Nouvelle colonne pour le nombre d'électeurs -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($partis as $parti): ?>
            <tr>
                <td><?php echo htmlspecialchars($parti['nom']); ?></td>
                <td><img src="<?php echo htmlspecialchars($parti['urlimage']); ?>" alt="<?php echo htmlspecialchars($parti['nom']); ?>" width="100"></td>
                <td><?php echo htmlspecialchars($parti['nombre_electeurs']); ?></td> <!-- Afficher le nombre d'électeurs -->
                <td>
                    <!-- Formulaire pour supprimer -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $parti['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce parti ?');">Supprimer</button>
                    </form>

                    <!-- Formulaire pour modifier -->
                    <button class="btn btn-warning btn-sm" onclick="document.getElementById('edit-<?php echo $parti['id']; ?>').style.display='block'">Modifier</button>

                    <div id="edit-<?php echo $parti['id']; ?>" style="display:none;">
                        <h3>Modifier le Parti</h3>
                        <form method="POST" action="" enctype="multipart/form-data" class="mt-2">
                            <input type="hidden" name="id" value="<?php echo $parti['id']; ?>">
                            <div class="mb-3">
                                <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($parti['nom']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <input type="hidden" name="current_image" value="<?php echo $parti['urlimage']; ?>">
                            <button type="submit" name="edit" class="btn btn-success">Mettre à jour</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
