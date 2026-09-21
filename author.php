<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') { header('Location:index.php'); exit; }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VERACORE | Author</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="app-bg">
<div class="ambient"><span></span><span></span><span></span></div><div class="grid"></div>
<header class="topbar">
  <div class="brand-inline"><div class="brand-icon">V</div><div><b>VERACORE</b><small>People Operations</small></div></div>
  <div class="top-actions"><span class="live-pill"><i></i> Live queue</span><span class="user-chip"><?=htmlspecialchars($_SESSION['name'])?></span><a href="logout.php" class="ghost-btn">Logout</a></div>
</header>
<main class="dashboard">
  <section class="dash-head">
    <div><div class="modal-kicker">AUTHOR WORKSPACE</div><h1>Approval control center.</h1><p>Review employee requests and record a decision with an audit trail.</p></div>
    <div class="queue-badge"><strong id="pendingCount">0</strong><span>pending requests</span></div>
  </section>

  <section class="stat-row">
    <div class="stat"><span>Pending queue</span><strong id="aPending">0</strong></div>
    <div class="stat"><span>Approved</span><strong id="aApproved">0</strong></div>
    <div class="stat"><span>Rejected</span><strong id="aRejected">0</strong></div>
  </section>

  <section class="panel wide">
    <div class="panel-title"><div><span class="modal-kicker">LIVE QUEUE</span><h2>Leave requests</h2></div><span class="live-pill"><i></i> Auto refresh</span></div>
    <div id="authorLeaves" class="author-list"></div>
  </section>
</main>
<div class="decision-modal hidden" id="decisionModal">
 <div class="modal-card small">
  <button class="modal-close" id="closeDecision">×</button>
  <div class="modal-kicker" id="decisionKicker">DECISION</div>
  <h2 id="decisionTitle">Approve request?</h2>
  <p class="muted">Add an optional note that the employee will see in the approval timeline.</p>
  <textarea id="decisionNote" rows="4" placeholder="Optional note..."></textarea>
  <button class="primary-btn full" id="confirmDecision">Confirm</button>
 </div>
</div>
<script>window.VERACORE_ROLE='author';</script>
<script src="assets/app.js"></script>
</body></html>
