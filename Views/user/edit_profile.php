<?php
include "Views/templates/header.php";

use App\Controllers\UserController;

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_csrf_token = $_SESSION['csrf_token'];
$_SESSION['previous_page'] = '/profile/update';
$userController = new UserController();

$user = null;
$error_message = null;
$nextPageError = null;
$nextPageSuccess = null;
if (isset($_GET['$error_message'])) {
    $nextPageError = $_GET['$error_message'];
}
if (isset($_GET['$success_message'])) {
    $nextPageSuccess = $_GET['$success_message'];
}
$userName = "";
$userEmail = "";
$userAvatar = "";

try {
    // Fetch user data
    $user = $userController->getUser($user_id);
    if ($user) {
        $userName = $userController->getUserName($user);
        $userEmail = $userController->getUserEmail($user);
        $userAvatar = $userController->getUserAvatar($user);
    }
} catch (Exception $e) {
    $error_message = "An unexpected error occurred. Please try again later.";
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form submission handling
    $updatedName = htmlspecialchars($_POST['name']);
    $updatedEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($updatedEmail, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
        die();
    }
    $updatedAvatar = $_FILES['avatar'];
    $oldPassword = htmlspecialchars($_POST['current_password']);
    $newPassword = htmlspecialchars($_POST['new_password']);
    $csrf_token = htmlspecialchars($_POST['csrf_token']);

    if ($csrf_token !== $user_csrf_token) {
        $error_message = "An unexpected token error occurred. Please try again later.";
        die();
    }

    // Validation
    if ((empty($updatedName) || empty($updatedEmail)) && !$error_message) {
        $error_message = "Name and email are required.";
        die();
    }

    // Process avatar upload (if new file is selected)
    if ($updatedAvatar['error'] === UPLOAD_ERR_OK && !$error_message) {
        // Validate file type and extension
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        $fileType = mime_content_type($updatedAvatar['tmp_name']);
        $fileExtension = strtolower(pathinfo($updatedAvatar['name'], PATHINFO_EXTENSION));

        $isAllowedType = in_array($fileType, $allowedTypes);
        $isAllowedExtension = in_array($fileExtension, $allowedExtensions);

        $isAllowed = true;
        $isSize = true;

        if (!$isAllowedType || !$isAllowedExtension) {
            $error_message = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
            die();
        }

        // Validate file size (2MB max)
        // Validate file size (Max 2MB)
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        if ($updatedAvatar['size'] > $maxFileSize) {
            $error_message = "File is too large. Maximum size is 2MB.";
            die();
        }

        $avatarPath = 'public/img/' . $user_id . '.' . pathinfo($updatedAvatar['name'], PATHINFO_EXTENSION);

    } else {
        // If no avatar is uploaded or error, use the current one
        $avatarPath = $userAvatar;
    }

    // If no errors, process the upload
    if (!$error_message) {
        // Update the user data in the database
        try {
            $success = $userController->updateUser($user_id, $updatedName, $updatedEmail, $avatarPath);
            if ($success) {
                move_uploaded_file($updatedAvatar['tmp_name'], $avatarPath);
                header("Location: /profile?message=Profile updated successfully!");
                exit();
            } else {
                $error_message = "An unexpected error occurred while updating your profile. Please try again later.";
            }
        } catch (Exception $e) {
            $error_message = "Failed to update the profile. Please try again later.";
        }
    }
}

?>

<div class="profile-edit-container">
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <?php if ($nextPageError): ?>
        <p style="color: red;"><?= htmlspecialchars($nextPageError); ?></p>
    <?php endif; ?>
    <?php if ($nextPageSuccess): ?>
        <p style="color: green;"><?= htmlspecialchars($nextPageSuccess); ?></p>
    <?php endif; ?>

    <h2>Edit Profile</h2>
    <form method="POST" action="/profile/update" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($userName); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($userEmail); ?>" required><br>

        <label for="avatar">Avatar:</label>
        <input type="file" id="avatar" name="avatar"><br>
        <img src="public/img/<?= htmlspecialchars($userAvatar); ?>" alt="Current Avatar" width="100"><br>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($user_csrf_token); ?>">

        <button type="submit">Save Changes</button>
    </form>

    <form method="GET" action="/reset/mail">
        <input type="hidden" name="email" value="<?= htmlspecialchars($userEmail); ?>">
        <button type="submit">Reset Password</button>
    </form>
</div>


<?php
include 'Views/templates/footer.php';
?>
