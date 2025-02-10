<?php
// List, edit, and delete user accounts.
use App\Controllers\AdminController;
use App\Controllers\SkillController;

include "Views/templates/header.php";

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login");
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
    header("Location: /admin/skills");
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
    <form method="GET" action="/admin/skills">
        <label>
            <input type="text" name="search" placeholder="Search skills..." value="<?= htmlspecialchars($search) ?>">
        </label>
        <button type="submit">Search</button>
    </form>

    <!-- Create Skill form -->
    <h2>Create Skill</h2>
    <form method="POST" action="/admin/skills">
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
                        <a href="/admin/skill/<?= $skill['id'] ?>/update?name=<?= $skill['name'] ?>&desc=<?= $skill['description'] ?>">Edit</a> |
                        <!-- Delete Skill -->
                        <form action="/admin/skill/<?= $skill['id'] ?>/delete" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this skill?')">Delete</button>
                        </form>
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
            <a href="/admin/skills?offset=<?php echo max(0, $offset - 10); ?>&search=<?= urlencode($search) ?>">Previous</a>
        <?php endif; ?>
        <?php if ($offset + 10 < $total_skills): ?>
            <a href="/admin/skills?offset=<?= $offset + 10 ?>&search=<?= urlencode($search) ?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'Views/templates/footer.php'; ?>
