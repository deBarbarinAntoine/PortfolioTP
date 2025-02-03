<?php
// Overview of site stats, users, and skills.
use App\Controllers\AdminController;

include "../Authentication & User Management/header.php";

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$adminController = new AdminController();
$admin_dashboard = $adminController->getAdminDashboard();
extract($admin_dashboard);
// >> if extract don't work <<
//$users_count = $admin_dashboard['users_count'];
//$skills_count = $admin_dashboard['skills_count'];
//$projects_count = $admin_dashboard['projects_count'];
//$users_count_last24hours = $admin_dashboard['users_count_last24hours'];
//$projects_count_last24hours = $admin_dashboard['projects_count_last24hours'];
//$latest_users = $admin_dashboard['latest_users'];
//$skills = $admin_dashboard['skills'];
?>

    <body>

    <h1>Admin Dashboard</h1>

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
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo date("F j, Y, g:i a", strtotime($user['created_at'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Skills</h2>
    <ul>
        <?php foreach ($skills as $skill): ?>
            <li><strong><?php echo htmlspecialchars($skill['name']); ?></strong>: <?php echo htmlspecialchars($skill['description']); ?></li>
        <?php endforeach; ?>
    </ul>

    </body>


<?php
include "../Authentication & User Management/footer.php";
