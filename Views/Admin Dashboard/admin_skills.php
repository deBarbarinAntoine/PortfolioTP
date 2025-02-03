<?php
// List, edit, and delete user accounts.
use App\Controllers\AdminController;
use App\Controllers\SkillController;

include "../Authentication & User Management/header.php";

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$adminController = new AdminController();
$skillController = new SkillController();

$total_skills = $skillController->getSkillsNumber();
$search = $_GET['search'] ?? '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
if ($offset < 0) $offset = 0;

if ($offset > max(0, $total_skills - 10)) {
    $offset = max(0, $total_skills - 10);
}

$admin_dashboard = $adminController->get_admin_skills($search, $offset);

// Handle Create Skill
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_skill'])) {
    $name = $_POST['name'];
    $description = $_POST['description'] ?? '';

    // Create the skill and capture the result
    $success = $skillController->createSkill($name, $description);

    // Store the success message in the session if creation was successful
    if ($success > 0) {
        $_SESSION['success_message'] = 'Skill created successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to create skill. Please try again.';
    }

    // Redirect back to the admin_skills.php page to show the success or error message
    header("Location: admin_skills.php");
    exit();
}

// Handle Delete Skill
if (isset($_GET['delete_skill'])) {
    $skill_id = $_GET['delete_skill'];
    $success = $skillController->deleteSkill($skill_id);

    // Store the success message in the session if creation was successful
    if ($success > 0) {
        $_SESSION['success_message'] = "Skill deleted successfully! (Deleted Rows: $success)";
    } else {
        $_SESSION['error_message'] = "Failed to delete skill. (Affected Rows: $success) Please try again.";
    }

    header("Location: admin_skills.php"); // Refresh the page to see the updated list
    exit();
}

// Check if there's a success or error message in the session
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    // Clear the message after displaying it
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    // Clear the message after displaying it
    unset($_SESSION['error_message']);
}
?>

<div class="container">
    <h1>Admin Dashboard</h1>

    <!-- Search form -->
    <form method="GET" action="admin_skills.php">
        <label>
            <input type="text" name="search" placeholder="Search skills..." value="<?= htmlspecialchars($search) ?>">
        </label>
        <button type="submit">Search</button>
    </form>

    <!-- Create Skill form -->
    <h2>Create Skill</h2>
    <form method="POST" action="admin_skills.php">
        <label>
            <input type="text" name="name" placeholder="Skill Name" required>
        </label>
        <label>
            <textarea name="description" placeholder="Skill Description"></textarea>
        </label>
        <button type="submit" name="create_skill">Create Skill</button>
    </form>

    <!-- List Skills -->
    <h2>Skills</h2>
    <table>
        <thead>
        <tr>
            <th>Skill Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($admin_dashboard)): ?>
            <?php foreach ($admin_dashboard as $skill): ?>
                <tr>
                    <td><?= htmlspecialchars($skill['name']) ?></td>
                    <td><?= htmlspecialchars($skill['description']) ?></td>
                    <td>
                        <!-- Edit Skill -->
                        <a href="edit_skill.php?id=<?= $skill['id'] ?>&name=<?= $skill['name'] ?>&desc=<?= $skill['description'] ?>">Edit</a> |
                        <!-- Delete Skill -->
                        <a href="admin_skills.php?delete_skill=<?= $skill['id'] ?>" onclick="return confirm('Are you sure you want to delete this skill?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No skills found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($offset > 0): ?>
            <a href="admin_skills.php?offset=<?php echo max(0, $offset - 10); ?>&search=<?= urlencode($search) ?>">Previous</a>
        <?php endif; ?>
        <?php if ($offset + 10 < $total_skills): ?>
            <a href="admin_skills.php?offset=<?= $offset + 10 ?>&search=<?= urlencode($search) ?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<?php include "../Authentication & User Management/footer.php"; ?>
