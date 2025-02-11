<?php
// Overview of site stats, users, and skills.
use App\Controllers\AdminController;
use App\Models\Level;
use App\Models\Logger;

include "Views/templates/header.php";

if (isset($_GET['success_message'])){
    $success = $_GET['success_message'];
}

if (isset($_GET['error_message'])){
    $error = $_GET['error_message'];
}

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login");
    exit();
}

// Fetch search term and pagination parameters
$search = $_GET['search'] ?? '';
$current_page = isset($_GET['current_page']) ? (int) $_GET['current_page'] : 1;
$limit = 10; // Number of skills to show per page
$offset = ($current_page - 1) * $limit;


$adminController = new AdminController();
$admin_dashboard = $adminController->getAdminDashboard($search,$offset);
extract($admin_dashboard);
$total_pages = $skills_count / $limit;

// >> if extract don't work <<
//$users_count = $admin_dashboard['users_count'];
//$skills_count = $admin_dashboard['skills_count'];
//$projects_count = $admin_dashboard['projects_count'];
//$users_count_last24hours = $admin_dashboard['users_count_last24hours'];
//$projects_count_last24hours = $admin_dashboard['projects_count_last24hours'];
//$latest_users = $admin_dashboard['latest_users'];
//$skills = $admin_dashboard['skills'];
?>

    <h1>Admin Dashboard</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <!-- Search Form -->
    <form method="GET" action="">
        <label>
            <input type="text" name="search" placeholder="Search skills..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </label>
        <button type="submit">Search</button>
    </form>

    <div class="stats">
        <p><strong>Total Users:</strong> <?php echo $users_count; ?></p>
        <p><strong>Total Skills:</strong> <?php echo $skills_count; ?></p>
        <p><strong>Total Projects:</strong> <?php echo $projects_count; ?></p>
    </div>

    <h2>Recent Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Change User Role</th>
        </tr>
        <?php foreach ($latest_users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user->getId()); ?></td>
                <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                <td><?php echo htmlspecialchars($user->getRole()->value); ?></td>
                <td><?php echo $user->getCreatedAt()->format("F j, Y, g:i a"); ?></td>
                <td>
                    <form action="/admin/users/changeRole" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user->getId()); ?>">
                        <input type="hidden" name="new_role" value="<?php echo ($user->getRole()->value === 'admin') ? 'user' : 'admin'; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']?>">
                        <button type="submit">Change to <?php echo ($user->getRole()->value === 'admin') ? 'User' : 'Admin'; ?></button>
                    </form>
                </td>

            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Skills</h2> <a href="/admin/skills"><button>Create New Skill</button>
</a>
    <ul>
        <?php foreach ($skills as $skill): ?>
            <li><strong><?php echo htmlspecialchars($skill->getName()); ?></strong>: <?php echo htmlspecialchars($skill->getDescription()); ?></li>
            <form action="/admin/skill/<?php echo $skill->getId(); ?>/update" method="GET">
                <input type="hidden" name="skill_id" value="<?php echo $skill->getId(); ?>">
                <input type="hidden" name="skill_name" value="<?php echo $skill->getName(); ?>">
                <input type="hidden" name="skill_description" value="<?php echo $skill->getDescription(); ?>">
                <button type="submit">Modify Skill</button>
            </form>
            <form action="/admin/skill/<?php echo $skill->getId(); ?>/delete" method="POST">
                <input type="hidden" name="skill_id" value="<?php echo $skill->getId(); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">
                <button type="submit">Delete Skill</button>
            </form>
        <?php endforeach; ?>
    </ul>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search_term); ?>">Previous</a>
        <?php endif; ?>
        <span>Page <?php echo $current_page; ?></span>
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search_term); ?>">Next</a>
        <?php endif; ?>
    </div>


<?php
include 'Views/templates/footer.php';
