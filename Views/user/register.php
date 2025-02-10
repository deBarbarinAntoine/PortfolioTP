<?php
// User registration form.

use App\Controllers\UserController;
use App\Models\User;

include "Views/templates/header.php";

$errorMessages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST["username"] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password'] ?? '');
    $confirm_password = htmlspecialchars($_POST['confirm_password'] ?? '');

    if ($username != "") {
        setcookie("username", $username, time() + (5 * 60), "/", "", false, true);
    }
    if ($email != "") {
        setcookie("email", $email, time() + (5 * 60), "/", "", false, true);
    }

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errorMessages[] = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errorMessages[] = "Passwords do not match.";
    } else {
        $registerController = new UserController();

        $msg = $registerController->checkUsernameValidity($username);
        if (!empty($msg)) {
            $errorMessages[] = $msg;
        }

        if (!$registerController->checkEmailValidity($email)) {
            $errorMessages[] = 'Mail address already exists.';
        }

        $msg = User::validatePassword($password);
        if (is_string($msg)) {
            $errorMessages[] = $msg;
        }

        if (empty($errorMessages)) {
            if (!$registerController->createUser($username, $email, $password)) {
                $errorMessages[] = "Error creating user.";
            } else {
                setcookie("username", "", time() - 3600, "/");
                setcookie("email", "", time() - 3600, "/");
                header("Location: /login?message=User created");
                die();
            }
        }
    }
}

?>

<form method="POST" action="/register">
    <h2>Register</h2>
    <?php foreach ($errorMessages as $error) : ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>
    <label>
        <input type="text" name="username" placeholder="Username"
               value="<?= htmlspecialchars($_COOKIE['username'] ?? '') ?>" required>
    </label>
    <label>
        <input type="email" name="email" placeholder="Email"
               value="<?= htmlspecialchars($_COOKIE['email'] ?? '') ?>" required>
    </label>
    <label>
        <input type="password" name="password" placeholder="Password" required>
    </label>
    <label>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    </label>
    <button type="submit">Register</button>
</form>

<?php
include 'Views/templates/footer.php';
?>
