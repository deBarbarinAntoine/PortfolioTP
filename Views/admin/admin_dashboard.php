<?php
// Overview of site stats, users, and skills.
use App\Controllers\AdminController;
use App\Models\Level;
use App\Models\Logger;

include "Views/templates/header.php";

// Debug
$id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
Logger::log("id: $id | role: $role", __FILE__, Level::DEBUG);

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
        </tr>
        <?php foreach ($latest_users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user->getId()); ?></td>
                <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                <td><?php echo htmlspecialchars($user->getRole()->value); ?></td>
                <td><?php echo $user->getCreatedAt()->format("F j, Y, g:i a"); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Skills</h2>
    <ul>
        <?php foreach ($skills as $skill): ?>
            <li><strong><?php echo htmlspecialchars($skill['name']); ?></strong>: <?php echo htmlspecialchars($skill['description']); ?></li>
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
