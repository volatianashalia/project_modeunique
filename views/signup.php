<?php
require_once __DIR__ '../config/DB.php';
require_once __DIR__ '../config/auth.php';
require_once __DIR__ '../config/functions.php'; 
$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $first_name = sanitize($_POST['first_name']); 
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];
        if(empty($first_name)) $errors[] = "Le nom est requis";
        if(empty($last_name)) $errors[] = "Le prénom est requis";   
        if(empty($email)) $errors[] = "L'email est requis";
        if(!validate_email($email)) $errors[] = "Format d'email invalide";
        $password_errors = validate_password($password);
        $errors = array_merge($errors, $password_errors);
        
        if($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas";

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if($stmt->fetch()) $errors[] = "L'email est déjà utilisé";


        if(empty($errors)){    
            $password_hashed = password_hash($password, PASSWORD_ARGON2ID);
            
            $stmt_count = $pdo->prepare("SELECT COUNT(*) as user_count FROM users");
            $stmt_count->execute();
            $result = $stmt_count->fetch(PDO::FETCH_ASSOC);

            $role = ($result['user_count'] == 0) ? 'admin' : 'client';
            
            $stmt = $pdo->prepare("INSERT INTO users(first_name, last_name, email, password, role, created_at, status) VALUES(?, ?, ?, ?, ?, NOW(), 'active')");     
            
            if($stmt->execute([$first_name, $last_name, $email, $password_hashed, $role])){        
                $success = true;
            } else {         
                $errors[] = "Erreur lors de la création du compte";     
            } 
        }
    }
}

$csrf_token = generate_csrf_token();
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - Mode Unique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
        .strength-weak { background-color: #dc3545; }
        .strength-medium { background-color: #ffc107; }
        .strength-strong { background-color: #28a745; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <?php if($success): ?>
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">Inscription réussie !</h4>
                                <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous <a href="login.php">connecter</a>.</p>
                            </div>
                        <?php else: ?>
                            <?php if(!empty($errors)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        <?php foreach($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <h2 class="text-center mb-4">Inscription</h2>
                            
                            <form method="POST" id="signupForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="first_name" placeholder="Votre nom" required>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="last_name" placeholder="Votre prénom" required>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="email" class="form-control" name="email" placeholder="Votre e-mail" required>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Saisissez un mot de passe" required>
                                    <div class="password-strength" id="passwordStrength"></div>
                                    <small class="text-muted">8 caractères min, avec majuscule, minuscule et chiffre</small>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="password" class="form-control" name="confirm-password" placeholder="Confirmez le mot de passe" required>
                                </div>
                                
                                <button type="submit" class="btn w-100 mb-3" style="color: #fff;background-color: #D4AF37;border-color: #D4AF37;">
                                    S'inscrire
                                </button>
                                
                                <div class="text-center">
                                    <span>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></span>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <script>
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength';
            if (strength <= 1) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
    </script>
</body>
</html>
