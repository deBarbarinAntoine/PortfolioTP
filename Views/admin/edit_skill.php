<?php

use App\Controllers\SkillController;

include "Views/templates/header.php";

$skillController = new SkillController();

$skill_id = $_GET['skill_id'] ?? "";
$skill_name = $_GET['skill_name'] ?? "";
$description = $_GET['skill_description'] ?? "";

// Handle Skill Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $skill_id = $_POST['skill_id'];
    $skill_name = $_POST['skill_name'];
    $description = $_POST['skill_description'];

    $success = $skillController->updateSkill($skill_id, $skill_name, $description);

    if ($success > 0) {
        $success_message = "Skill updated successfully! (Affected Rows: $success)";
        header("Location: /admin/skills?success_message=$success_message");
        exit();
    } else {
        $error = "Failed to update skill. (Affected Rows: $success) Please contact the administrator.";
    }


}
?>

<div class="container">
    <h1>Edit Skill</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="POST" action="">
        <input type="hidden" name="skill_id" value="<?= htmlspecialchars($skill_id) ?>">
        <label>
            <input type="text" name="skill_name" value="<?= htmlspecialchars($skill_name) ?>" required>
        </label>
        <label>
            <textarea name="skill_description"><?= htmlspecialchars($description) ?></textarea>
        </label>
        <button type="submit" name="update_skill">Update Skill</button>
    </form>
</div>

<?php include 'Views/templates/footer.php'; ?>
