<?php
// skill management from here
include "Views/templates/header.php";

use App\Controllers\SkillController;
use App\Controllers\User_SkillController;
use App\Controllers\UserController;
use App\Models\UserSkillLevel;

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

if (isset($_GET['success_message'])){
    $success = $_GET['success_message'];
}

if (isset($_GET['error_message'])){
    $error = $_GET['error_message'];
}

$user_id = $_SESSION['user_id'];
$user_csrf_token = $_SESSION['csrf_token'];
$userController = new UserController();
$user_skillController = new User_SkillController();
$levelEnum = UserSkillLevel::cases();
$skillController = new SkillController();

$user = null;
$error_message = null;
$skillsNotInUserSkills = [];
$skillsInUserSkills = [];
$userSkills = [];
$skillList = $skillController->getAllSkills();

try {
    // Fetch user data
    $user = $userController->getUser($user_id);
    if ($user) {
        $userSkills = $user_skillController->getUserSkills($user_id);
        if (!$userSkills){
            $skillsNotInUserSkills = $skillList;
        } else {
            foreach ($skillList as $skill) {
                $isUserSkill = false;
                foreach ($userSkills as $userSkill) {
                    if ($skill->getId() == $userSkill['skill_id']) {
                        $userSkill['name'] = $skill->getName();
                        $userSkill['description'] = $skill->getDescription();
                        $skillsInUserSkills[] = $userSkill;
                        $isUserSkill = true;
                    }
                }
                if ($isUserSkill) {
                    $skillsNotInUserSkills[] = $skill;
                }
            }
        }
    }
} catch (Exception $e) {
    $error_message = "An unexpected error occurred while fetching your skills. Please try again later.";
    die();
}

?>

<div class="skill-management-container">
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <h3>Your Skills</h3>
        <ul>
            <?php if (!empty($skillsInUserSkills)): ?>
                <?php foreach ($skillsInUserSkills as $userSkill): ?>
                    <?php
                    $userSkillId = $userSkill['id'];
                    $skillId = $userSkill['skill_id'];
                    $skillName = $userSkill['name'];
                    $skillLevel = $userSkill['level'];
                    ?>
                    <li>
                        <p><?= htmlspecialchars($skillName); ?> - Level <?= htmlspecialchars($skillLevel); ?></p>
                        <form method="POST" action="/profile/updateSkill">
                            <!-- Modify Skill Form -->
                            <label for="new_level>">New Level</label>
                            <select id="new_level>" name="new_level" required>
                                <?php foreach ($levelEnum as $level): ?>
                                    <option value="<?= htmlspecialchars($level->value) ?>"><?= htmlspecialchars($level->value) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($user_csrf_token); ?>">
                            <input type="hidden" name="userSkillId" value="<?= $userSkillId ?>">
                            <button type="submit">Update Skill</button>
                        </form>

                        <form method="POST" action="/profile/deleteSkill">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($user_csrf_token); ?>">
                            <input type="hidden" name="userSkillId" value="<?= $userSkillId ?>">
                            <button type="submit" name="delete_skill" value="<?= $userSkillId ?>" onclick="return confirm('Are you sure you want to delete this skill?')">Delete Skill</button>
                        </form>

                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No skills available.</p>
            <?php endif; ?>
        </ul>






    <h3>Add a New Skill</h3>
    <form method="POST" action="/profile/addSkill">

        <label for="new_skill_id">Skill:</label>
        <select id="new_skill_id" name="new_skill_id" required>
            <?php foreach ($skillsNotInUserSkills as $skill): ?>
                <option value="<?= htmlspecialchars(is_object($skill) ? $skill->getId() : $skill['id']) ?>">
                    <?= htmlspecialchars(is_object($skill) ? $skill->getName() : $skill['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="new_skill_level">Level:</label>
        <select id="new_skill_level" name="new_skill_level" required>
            <?php foreach ($levelEnum as $level): ?>
                <option value="<?= htmlspecialchars($level->value) ?>"><?= htmlspecialchars($level->value) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($user_csrf_token); ?>">

        <button type="submit" name="add_skill">Add Skill</button>
    </form>
</div>

<?php
include 'Views/templates/footer.php';
?>
