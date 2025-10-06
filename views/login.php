<?php
require_once '../config/DB.php';
require_once '../config/auth.php';
require_once '../config/functions.php';
if(is_logged_in()) {
    if($_SESSION['role'] === 'admin') {
        header('Location: users/admin.php');
    } else {
        header('Location: ../views/products/productPage.php');
    }
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try { 
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errors[] = "Erreur de sécurité. Veuillez réessayer.";
        } else {
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            if(empty($email) || empty($password)) {
                $errors[] = "L'adresse email et le mot de passe sont requis.";
            } else {
                $stmt = $pdo->prepare("
                    SELECT id, first_name, last_name, email, password, role, status 
                    FROM users 
                    WHERE email = ? 
                ");
                
                if($stmt->execute([$email])) {
                    $user = $stmt->fetch();
                    var_dump($user);
                    var_dump($password);
                    var_dump(password_verify($password, $user['password']));

                    if ($user && password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['first_name'] = $user['first_name'];
                        $_SESSION['last_name'] = $user['last_name'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];

                        $updateStmt = $pdo->prepare("
                            UPDATE users 
                            SET last_login = NOW() 
                            WHERE id = ?
                        ");
                        $updateStmt->execute([$user['id']]);
                        if($user['role'] === 'admin') {
                            header('Location: users/admin.php');
                        } else {
                            header('Location: ../views/products/productPage.php');
                        }
                        exit();
                    } else {
                        $errors[] = "Adresse email ou mot de passe incorrect.";
                    }
                } else {
                    throw new Exception("Erreur de base de données");
                }
            }
        }
    } catch (Exception $e) {
        $errors[] = "Une erreur s'est produite lors de la connexion. Veuillez réessayer.";
        error_log("Erreur de connexion: " . $e->getMessage());
    }
}

$csrf_token = generate_csrf_token();
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Mode Unique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <?php if(!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-3x text-muted mb-3"></i>
                            <h1 class="h3 fw-normal">Se connecter</h1>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="form-floating mb-3">
                                <input type="email" name="email" class="form-control" id="email" 
                                       placeholder="name@example.com" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                <label for="email">Adresse email</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control" 
                                       id="password" placeholder="Password" required>
                                <label for="password">Mot de passe</label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>
                            
                            <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" 
                                    style="color: #fff;background-color: #D4AF37;border-color: #D4AF37;">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                            
                            <div class="text-center">
                                <p>N'avez-vous pas de compte ? <a href="signup.php">S'inscrire ici</a></p>
                                <a href="forgot-password.php" class="text-muted">Mot de passe oublié ?</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <p class="text-muted">&copy; 2025 Mode Unique - Tous droits réservés</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>