<?php
// List, edit, and delete user accounts.
use App\Controllers\AdminController;
use App\Controllers\UserController;

include "../Authentication & User Management/header.php";

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$adminController = new AdminController();
$userController = new UserController();


$total_users = $userController->getUserNumber();
$search = $_GET['search'] ?? '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
if ($offset < 0)
    $offset = 0;

if ($offset > max(0, $total_users - 10)) {
    $offset = max(0, $total_users - 10);
}


try {
    $admin_dashboard = $adminController->get_admin_users($search, $offset);
} catch (DateMalformedStringException $e) {
    echo $e->getMessage();
}

?>

    <body>

    <div class="container">
        <h1>User Management</h1>

        <!-- Search Bar -->
        <form method="GET" action="admin_users.php">
            <label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Search by name, username, or email">
            </label>
            <button type="submit">Search</button>
        </form>

        <!-- User List -->
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Avatar</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($admin_dashboard->num_rows > 0) {
                while ($row = $admin_dashboard->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>
    <form method='POST' action='update_role.php'>
        <input type='hidden' name='user_id' value='" . $row['id'] . "'>
        <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
        <select name='new_role' onchange='this.form.submit()'>
            <option value='admin' " . ($row['role'] === 'admin' ? 'selected' : '') . ">Admin</option>
            <option value='user' " . ($row['role'] === 'user' ? 'selected' : '') . ">User</option>
        </select>
    </form>
</td>";
                    $avatar = !empty($row['avatar']) ? htmlspecialchars($row['avatar']) : 'default-avatar.png';
                    echo "<td><img src='$avatar' alt='Avatar' width='50' height='50'></td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>" . $row['updated_at'] . "</td>";
                    echo "<td>
                <form method='POST' action='delete_user.php' onsubmit=\"return confirm('Are you sure you want to delete this user?');\">
                    <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                    <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                    <button type='submit' class='delete-btn' style='background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;'>Delete</button>
                </form>
            </td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No users found</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div>
            <?php if ($offset > 0): ?>
                <a href="?search=<?php echo urlencode($search); ?>&offset=<?php echo max(0, $offset - 10); ?>">Previous</a>
            <?php endif; ?>

            <?php if (($offset + 10) < $total_users): ?>
                <a href="?search=<?php echo urlencode($search); ?>&offset=<?php echo $offset + 10; ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

    </body>

<?php
include "../Authentication & User Management/footer.php";
?>