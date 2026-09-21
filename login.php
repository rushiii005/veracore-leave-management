<?php
session_start();
require 'db.php';

$role = $_GET['role'] ?? $_POST['role'] ?? 'employee';

if (!in_array($role, ['employee', 'author'])) {
    $role = 'employee';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Any email + any password accepted
    if ($email !== '' && $password !== '') {

        // Check whether this email already exists for this role
        $stmt = $pdo->prepare(
            "SELECT id, name, email, role 
             FROM users 
             WHERE email = ? AND role = ?
             LIMIT 1"
        );

        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();

        // If user doesn't exist, create automatically
        if (!$user) {

            $name = explode('@', $email)[0];
            $name = ucwords(str_replace(['.', '_', '-'], ' ', $name));

            $demoPassword = password_hash('demo', PASSWORD_DEFAULT);

            $insert = $pdo->prepare(
                "INSERT INTO users 
                (name, email, password_hash, role)
                VALUES (?, ?, ?, ?)"
            );

            $insert->execute([
                $name,
                $email,
                $demoPassword,
                $role
            ]);

            $user = [
                'id' => $pdo->lastInsertId(),
                'name' => $name,
                'email' => $email,
                'role' => $role
            ];
        }

        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirect according to role
        if ($role === 'employee') {
            header('Location: employee.php');
        } else {
            header('Location: author.php');
        }

        exit;

    } else {
        $error = 'Please enter email and password.';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>VERACORE | <?= ucfirst($role) ?> Login</title>

<link rel="stylesheet" href="assets/style.css">
</head>

<body class="app-bg">

<div class="ambient">
    <span></span>
    <span></span>
    <span></span>
</div>

<div class="grid"></div>

<div class="auth-wrap">

<a href="index.php" class="back">← VERACORE</a>

<div class="auth-card">

<div class="brand-inline">

<div class="brand-icon">V</div>

<div>
<b>VERACORE</b>
<small>Leave Management</small>
</div>

</div>

<div class="modal-kicker">
<?= strtoupper($role) ?> WORKSPACE
</div>

<h1>Welcome back.</h1>

<p class="muted">
Enter any email and any password to continue.
</p>

<?php if ($error): ?>

<div class="alert error">
<?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<form method="post" class="form">

<input
type="hidden"
name="role"
value="<?= htmlspecialchars($role) ?>"
>

<label>
Work email

<input
type="email"
name="email"
placeholder="Enter any email"
required
>

</label>

<label>
Password

<input
type="password"
name="password"
placeholder="Enter any password"
required
>

</label>

<button class="primary-btn full">

Sign in <span>→</span>

</button>

</form>

<div class="demo-box">

<b>Demo Login</b><br>

Any email + any password will work.

</div>

</div>

</div>

</body>
</html>