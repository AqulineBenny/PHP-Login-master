<?php 
require_once ('../public/config.php');

if(isset($_SESSION['Username'])) {
    header("location: index.php");
    exit;
}

if(isset($_POST['Submit'])) {
    $username = trim($_POST['Username']);
    $password = trim($_POST['Password']);
    
    if(empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Check against database
        try {
            $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['Username'] = $user['username'];
                $_SESSION['Active'] = true;
                header("location: index.php");
                exit;
            } else {
                $error = "Incorrect Username or Password";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
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
    <title>Sign in</title>
</head>

<body>
    
<div class="container">
    <form action="" method="post" name="Login_Form" class="form-signin" onsubmit="return validateForm()">
        <h2 class="form-signin-heading">Please sign in</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <label for="inputUsername">Username</label>
        <input name="Username" type="text" id="inputUsername" class="form-control" placeholder="Username" required autofocus>
        
        <label for="inputPassword">Password</label>
        <input name="Password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        
        <div class="checkbox">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        
        <button name="Submit" value="Login" class="button" type="submit">Sign in</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</div>

<script>
function validateForm() {
    var username = document.getElementById('inputUsername').value.trim();
    var password = document.getElementById('inputPassword').value.trim();
    
    if(username === "" || password === "") {
        alert("Please enter both username and password");
        return false;
    }
    
    return true;
}
</script>
</body>
</html>