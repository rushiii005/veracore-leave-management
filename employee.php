<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') { header('Location:index.php'); exit; }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VERACORE | Employee</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="app-bg">
<div class="ambient"><span></span><span></span><span></span></div><div class="grid"></div>
<header class="topbar">
  <div class="brand-inline"><div class="brand-icon">V</div><div><b>VERACORE</b><small>People Operations</small></div></div>
  <div class="top-actions"><span class="live-pill"><i></i> Live</span><span class="user-chip"><?=htmlspecialchars($_SESSION['name'])?></span><a href="logout.php" class="ghost-btn">Logout</a></div>
</header>
<main class="dashboard">
  <section class="dash-head">
    <div><div class="modal-kicker">EMPLOYEE WORKSPACE</div><h1>Good to see you, <?=htmlspecialchars(explode(' ',$_SESSION['name'])[0])?>.</h1><p>Submit a leave request and follow every approval in real time.</p></div>
    <button class="inbox-btn" id="inboxBtn"><span>◎</span> Inbox <b id="inboxCount">0</b></button>
  </section>

  <section class="stat-row">
    <div class="stat"><span>Requests</span><strong id="statTotal">0</strong></div>
    <div class="stat"><span>Pending</span><strong id="statPending">0</strong></div>
    <div class="stat"><span>Approved</span><strong id="statApproved">0</strong></div>
    <div class="stat"><span>Rejected</span><strong id="statRejected">0</strong></div>
  </section>

  <section class="content-grid">
    <div class="panel">
      <div class="panel-title"><div><span class="modal-kicker">NEW REQUEST</span><h2>Apply for leave</h2></div><span class="panel-dot"></span></div>
      <form id="leaveForm" class="form">
        <label>Reason<textarea name="reason" rows="4" placeholder="Tell us why you need leave..." required></textarea></label>
        <div class="two-col">
          <label>From<input type="date" name="start_date" required></label>
          <label>To<input type="date" name="end_date" required></label>
        </div>
        <button class="primary-btn full">Submit request <span>→</span></button>
        <div id="formMsg"></div>
      </form>
    </div>

    <div class="panel">
      <div class="panel-title"><div><span class="modal-kicker">MY REQUESTS</span><h2>Leave inbox</h2></div></div>
      <div id="employeeLeaves" class="list"></div>
    </div>
  </section>
</main>

<div class="drawer hidden" id="inboxDrawer">
 <div class="drawer-card">
  <button class="modal-close" id="closeDrawer">×</button>
  <div class="modal-kicker">INBOX</div><h2>Approval activity</h2><p class="muted">Every action by an author appears here automatically.</p>
  <div id="drawerList" class="list"></div>
 </div>
</div>
<script>window.VERACORE_ROLE='employee';</script>
<script src="assets/app.js"></script>
</body></html>
