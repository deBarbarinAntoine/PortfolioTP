<?php
// Login form with "Remember Me" option.
use App\Controllers\UserController;

include 'header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // Check if Remember Me is checked

    $loginController = new UserController();
    $user = $loginController->login($email, $password);

    if ($user) {
        try {
            $_SESSION['user_id'] = $loginController->getUserId($user);
        } catch (DateMalformedStringException $e) {
            echo $error = $e->getMessage();
        } // Store user ID in session
        $_SESSION['role'] = $loginController->getUserRole($user);
        if ($remember) {
            setcookie("user_email", $email, time() + (86400 * 30), "/"); // Store email for 30 days
        }

        header("Location: index.php"); // Redirect on successful login
        exit();
    } else {
        $error = "Email or password incorrect";
    }
}
?>

<form method="post">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <label>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_COOKIE['user_email'] ?? '') ?>" required>
    </label>
    <label>
        <input type="password" name="password" placeholder="Password" required>
    </label>
    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>
    <button type="submit">Login</button>
</form>


<?php
include 'footer.php';
?>