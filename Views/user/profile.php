<?php
// User profile display with a button to go to management of his account (edit name, email, etc.)
include "Views/templates/header.php";

// Ensure the user is logged in
use App\Controllers\SkillController;
use App\Controllers\User_SkillController;
use App\Controllers\UserController;

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

$userController = new UserController();
$skillController = new SkillController();
$user_skillController = new User_SkillController();
$user = null;
$error_message = null;

try {
    $user = $userController->getUser($user_id);
} catch (DateMalformedStringException $e) {
    $error_message = "Error: Invalid date format in user data.";
} catch (Exception $e) {
    $error_message = "An unexpected error occurred. Please try again later.";
}

// If user data is missing or initialization failed, show an error
if (!$user) {
    $error_message = $error_message ?: "User not found. Please try logging in again.";
} else {
    $userName = $userController->getUserName($user);
    $userAvatar = $userController->getUserAvatar($user);
    $userEmail = $userController->getUserEmail($user);
    $userRole = $user_role;
    $userSince = $userController->getUserCreationDate($user);
    $userModified = $userController->getUserModificationDate($user);
    $userSkills = $user_skillController->getUserSkills($user_id);
    foreach ($userSkills as $userSkill) {
        $skill = $skillController->getSkill($userSkill['skill_id']);
        $userSkill['name'] = $skill->getName();
        $userSkill['description'] = $skill->getDescription();
        $skillsInUserSkills[] = $userSkill;
    }

}
?>
    <div class="profile-container">
        <?php if (isset($_GET['message'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success_message'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success_message']); ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
            <a href="/login"><button>Login Again</button></a>
        <?php else: ?>
            <h2>Welcome, <?= htmlspecialchars($userName); ?></h2>
            <img src="<?= htmlspecialchars($userAvatar); ?>" alt="Avatar" width="100">
            <p>Email: <?= htmlspecialchars($userEmail); ?></p>
            <p>Role: <?= htmlspecialchars($userRole); ?></p>
            <p>Member since: <?= $userSince ? $userSince->format('Y-m-d') : 'Date not available'; ?></p>
            <p>Last updated: <?= $userModified ? $userModified->format('Y-m-d') : 'Date not available'; ?></p>

            <h3>Skills:</h3>
            <ul>
                <?php if (!empty($skillsInUserSkills)): ?>
                    <?php foreach ($skillsInUserSkills as $skillsInUserSkill): ?>
                        <li><?= htmlspecialchars($skillsInUserSkill['name']); ?> - Level <?= htmlspecialchars($skillsInUserSkill['level']); ?> - Description : <?= htmlspecialchars($skillsInUserSkill['description']) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>

                    <p>You haven't added any skills yet. Update your profile to add skills.</p>
                <?php endif; ?>
            </ul>

            <a href="/profile/update"><button>Edit Profile</button></a>
            <a href="/profile/skills"><button>Edit Skills</button> </a>
        <?php endif; ?>
    </div>

<?php
include 'Views/templates/footer.php';
?>
