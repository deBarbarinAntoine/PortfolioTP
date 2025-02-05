<?php
// skill management from here
include 'header.php';

use App\Controllers\SkillController;
use App\Controllers\User_SkillController;
use App\Controllers\UserController;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_csrf_token = $_SESSION['csrf_token'];
$userController = new UserController();
$user_skillController = new User_SkillController();
$levelEnum = App\Models\UserSkillLevel::cases();
$skillController = new SkillController();

$user = null;
$error_message = null;
$skillsNotInUserSkills = [];
$userSkills = [];
$skillList = $skillController->getAllSkills();

try {
    // Fetch user data
    $user = $userController->getUser($user_id);
    if ($user) {
        $userSkills = $user_skillController->getUserSkills($user_id);
        // Compare $skillList with $userSkills to find skills not in $userSkills
        $skillsNotInUserSkills = array_diff($skillList, $userSkills);
    }
} catch (Exception $e) {
    $error_message = "An unexpected error occurred while fetching your skills. Please try again later.";
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = htmlspecialchars($_POST['csrf_token']);

    if ($csrf_token !== $user_csrf_token) {
        $error_message = "An unexpected token error occurred. Please try again later.";
    }

    // Handle deleting skill
    if (isset($_POST['delete_skill'])) {
        $UserSkillId = intval($_POST['delete_skill']);
        try {
            $success = $user_skillController->deleteSkillFromUser($UserSkillId);
            if ($success === true) {
                header("Location: edit_profile.php?message=Skill deleted successfully!");
                exit();
            } else {
                $error_message = "Failed to delete the skill: " . $success;
            }
        } catch (Exception $e) {
            $error_message = "An unexpected error occurred while deleting the skill.";
        }
    }

    // Handle modifying skill level
    if (isset($_POST['modify_skill'])) {
        // Loop through all the submitted new level inputs
        foreach ($_POST as $key => $value) {
            // Check if the key is related to the skill level (starts with 'new_level_')
            if (str_starts_with($key, 'new_level_')) {
                // Extract the skill ID from the key (e.g., 'new_level_3' -> 3)
                $skillId = str_replace('new_level_', '', $key);

                // Get the new level value
                $newLevel = htmlspecialchars($value);

                // Get the associated user skill ID from the hidden input
                $userSkillId = $_POST['user_skill_id'] ?? null;

                if ($skillId && $newLevel && $userSkillId) {
                    $success = $user_skillController->updateUserSkillLevel($userSkillId,$skillId, $newLevel);
                    if ($success === true) {
                        header("Location: edit_profile.php?message=Skill updated successfully!");
                        exit();
                    } else {
                        $error_message = "Failed to update the skill: " . $success;
                    }
                }
            }
        }
    }

    // Handle adding new skill
    if (isset($_POST['add_skill'])) {
        $newSkillId = intval($_POST['new_skill_id']);
        $newSkillLevel = htmlspecialchars($_POST['new_skill_level']);

        // Check if the user already has this skill
        $existingSkills = array_column($userSkills, 'skill_id');
        if (in_array($newSkillId, $existingSkills)) {
            $error_message = "You already have this skill!";
        } else {
            try {
                $success = $user_skillController->addSkillToUser($user_id, $newSkillId, $newSkillLevel);
                if ($success === true) {
                    header("Location: edit_profile.php?message=New skill added successfully!");
                    exit();
                } else {
                    $error_message = "Failed to add the new skill." . $success;
                }
            } catch (Exception $e) {
                $error_message = "An unexpected error occurred while adding the skill.";
            }
        }
    }

}
?>

<div class="skill-management-container">
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <h3>Your Skills</h3>
    <form method="POST">
        <ul>
            <?php if (!empty($userSkills)): ?>
                <?php foreach ($userSkills as $userSkill): ?>
                    <?php
                    $userSkillId = $userSkill['id'];
                    $skillId = $userSkill['skill_id'];
                    $skillName = $userSkill['name'];
                    $skillLevel = $userSkill['level'];
                    ?>
                    <li>
                        <p><?= htmlspecialchars($skillName); ?> - Level <?= htmlspecialchars($skillLevel); ?></p>

                        <!-- Modify Skill Form -->
                        <label for="new_level_<?= $skillId ?>">New Level:</label>
                        <select id="new_level_<?= $skillId ?>" name="new_level_<?= $skillId ?>" required>
                            <?php foreach ($levelEnum as $level): ?>
                                <option value="<?= htmlspecialchars($level->value) ?>"><?= htmlspecialchars($level->value) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="user_skill_id" value="<?= $userSkillId ?>">

                        <!-- Delete Skill Form -->
                        <button type="submit" name="delete_skill" value="<?= $userSkillId ?>" onclick="return confirm('Are you sure you want to delete this skill?')">Delete Skill</button>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No skills available.</p>
            <?php endif; ?>
        </ul>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($user_csrf_token); ?>">

        <!-- Submit Button -->
        <button type="submit">Submit</button>
    </form>

    <h3>Add a New Skill</h3>
    <form method="POST">
        <label for="new_skill_id">Skill:</label>
        <select id="new_skill_id" name="new_skill_id" required>
            <?php
            foreach ($skillsNotInUserSkills as $skill):
                ?>
                <option value="<?= htmlspecialchars($skill['id']) ?>"><?= htmlspecialchars($skill['name']) ?></option>
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
include 'footer.php';
?>
