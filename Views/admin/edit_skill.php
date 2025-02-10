<?php
use App\Controllers\SkillController;

include "../user/header.php";

$skillController = new SkillController();

$skill_id = $ParamID ?? '';
$name = $_GET['name'] ?? '';
$desc = $_GET['desc'] ?? '';

// Handle Skill Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_skill'])) {
    $name = $_POST['name'];
    $description = $_POST['description'] ?? '';

    $success = $skillController->updateSkill($skill_id, $name, $description);

    if ($success > 0) {
        $_SESSION['success_message'] = "Skill updated successfully! (Affected Rows: $success)";
    } else {
        $_SESSION['error_message'] = "Failed to update skill. (Affected Rows: $success) Please try again.";
    }

    header("Location: /admin/skills");
    exit();
}
?>

<div class="container">
    <h1>Edit Skill</h1>

    <form method="POST" action="/admin/skill/<?= $skill_id ?>/update">
        <label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </label>
        <label>
            <textarea name="description"><?= htmlspecialchars($desc) ?></textarea>
        </label>
        <button type="submit" name="update_skill">Update Skill</button>
    </form>
</div>

<?php include "../user/footer.php"; ?>
