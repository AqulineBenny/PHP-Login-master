<?php
require_once('../public/config.php');

if(isset($_SESSION['Active'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if($_POST) {
    $username = trim($_POST['Username']);
    $password = $_POST['Password'];
    $confirm = $_POST['Confirm_Password'];

    // Basic validation
    if(empty($username) || empty($password) || empty($confirm)) {
        $error = "All fields are required";
    } elseif($password !== $confirm) {
        $error = "Passwords don't match";
    } else {
        try {
            // Check if username exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if($stmt->rowCount() > 0) {
                $error = "Username taken";
            } else {
                // Create account
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $hash]);
                
                // Auto-login
                $_SESSION['user_id'] = $db->lastInsertId();
                $_SESSION['Username'] = $username;
                $_SESSION['Active'] = true;
                
                header("Location: index.php");
                exit;
            }
        } catch(PDOException $e) {
            $error = "System error. Please try later.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../css/signin.css">
    <link rel="stylesheet" type="text/css" href="../css/stylesheet.css">
    <title>Register</title>
</head>
<body>
<div class="container">
    <form method="post" name="Register_Form" class="form-signin">
        <h2 class="form-signin-heading">Register</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <label for="inputUsername">Username</label>
        <input name="Username" type="text" id="inputUsername" class="form-control" 
               placeholder="Username" required autofocus value="<?= htmlspecialchars($username ?? '') ?>">
        
        <label for="inputPassword">Password</label>
        <input name="Password" type="password" id="inputPassword" class="form-control" 
               placeholder="Password" required>
        
        <label for="inputConfirmPassword">Confirm Password</label>
        <input name="Confirm_Password" type="password" id="inputConfirmPassword" 
               class="form-control" placeholder="Confirm Password" required>
        
        <button name="Register" value="Register" class="button" type="submit">Register</button>
        <p class="text-center">Already have an account? <a href="login.php">Login here</a></p>
    </form>
</div>
</body>
</html>