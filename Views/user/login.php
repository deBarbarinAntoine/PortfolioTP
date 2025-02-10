<?php
// Login form with "Remember Me" option.
use App\Controllers\UserController;
use Random\RandomException;

include "Views/templates/header.php";

$error = "";
$message = "";

// Check if the 'message' parameter is set in the URL
if (isset($_GET['message'])) {
    // Get the message from the URL query parameter
    $message = htmlspecialchars($_GET['message']);
}

if (isset($_GET['error_message'])) {
    $error = htmlspecialchars($_GET['error_message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // Check if Remember Me is checked

    $loginController = new UserController();
    $user = $loginController->login($email, $password);

    if ($user) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (RandomException $e) {
            $error = "Couldn't set csrf_token" . $e->getMessage();
        }
        $_SESSION['user_id'] = $loginController->getUserId($user);// Store user ID in session
        $_SESSION['user_role'] = $loginController->getUserRole($user);
        $_SESSION['LAST_ACTIVITY'] = time();

        if ($remember) {
            setcookie("user_email", $email, time() + (86400 * 30), "/"); // Store email for 30 days
        }
        if ($error){
            $_SESSION = [];
        } else {
            header("Location: /"); // Redirect on successful login
            exit();
        }

    } else {
        $error = "Email or password incorrect";
    }
}
?>

    <form method="POST" action="/login">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if (!empty($message)) echo "<p style='color: green;'>$message</p>"; ?>

        <label>
            <input type="email" name="email" placeholder="Email"
                   value="<?= htmlspecialchars($_COOKIE['user_email'] ?? '') ?>" required>
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
include 'Views/templates/footer.php';
?>