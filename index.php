<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'employee') {
        header('Location: employee.php'); exit;
    }
    header('Location: author.php'); exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>VERACORE | Leave Management</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="landing">
<div class="ambient"><span></span><span></span><span></span></div>
<div class="grid"></div>

<main class="hero-shell">
  <div class="brand-mark">
    <div class="brand-icon">V</div>
    <div>
      <div class="brand-name">VERACORE</div>
      <div class="brand-sub">People Operations Platform</div>
    </div>
  </div>

  <section class="hero-card">
    <div class="eyebrow">SMART • SECURE • CONNECTED</div>
    <h1>Leave management,<br><em>reimagined.</em></h1>
    <p>One professional workspace for employees and authors to submit, review and track leave approvals.</p>
    <button class="primary-btn" id="openLogin">Login <span>→</span></button>
    <div class="hero-meta">
      <span><i></i> Live approval updates</span>
      <span><i></i> Secure role access</span>
      <span><i></i> Approval history</span>
    </div>
  </section>

  <footer>© <?=date('Y')?> VERACORE · Internal Leave Management</footer>
</main>

<div class="modal hidden" id="loginChooser">
  <div class="modal-card">
    <button class="modal-close" id="closeLogin">×</button>
    <div class="modal-kicker">ACCESS PORTAL</div>
    <h2>Choose your workspace</h2>
    <p class="muted">Continue with the role assigned to your VERACORE account.</p>
    <div class="role-grid">
      <a class="role-card" href="login.php?role=employee">
        <div class="role-icon">E</div><div><strong>Employee Login</strong><small>Apply & track your leaves</small></div><span>→</span>
      </a>
      <a class="role-card" href="login.php?role=author">
        <div class="role-icon author">A</div><div><strong>Author Login</strong><small>Review & approve requests</small></div><span>→</span>
      </a>
    </div>
  </div>
</div>
<script>
const modal=document.getElementById('loginChooser');
document.getElementById('openLogin').onclick=()=>modal.classList.remove('hidden');
document.getElementById('closeLogin').onclick=()=>modal.classList.add('hidden');
modal.addEventListener('click',e=>{if(e.target===modal)modal.classList.add('hidden')});
</script>
</body>
</html>
