<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'db.php';

function out($data, $code=200){ http_response_code($code); echo json_encode($data); exit; }
if (!isset($_SESSION['user_id'])) out(['ok'=>false,'message'=>'Unauthorized'],401);

$action=$_GET['action'] ?? $_POST['action'] ?? '';

if ($action==='employee_leaves' && $_SESSION['role']==='employee') {
    $stmt=$pdo->prepare("SELECT l.*, u.name employee_name FROM leaves l JOIN users u ON u.id=l.employee_id WHERE l.employee_id=? ORDER BY l.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $leaves=$stmt->fetchAll();
    foreach($leaves as &$leave){
        $s=$pdo->prepare("SELECT h.*,u.name author_name FROM leave_history h JOIN users u ON u.id=h.author_id WHERE h.leave_id=? ORDER BY h.created_at ASC");
        $s->execute([$leave['id']]); $leave['history']=$s->fetchAll();
    }
    out(['ok'=>true,'leaves'=>$leaves]);
}

if ($action==='apply_leave' && $_SESSION['role']==='employee') {
    $reason=trim($_POST['reason']??'');
    $start=$_POST['start_date']??''; $end=$_POST['end_date']??'';
    if(!$reason || !$start || !$end) out(['ok'=>false,'message'=>'Please fill all fields.'],422);
    if($end < $start) out(['ok'=>false,'message'=>'End date cannot be before start date.'],422);
    $stmt=$pdo->prepare("INSERT INTO leaves(employee_id,reason,start_date,end_date,status) VALUES(?,?,?,?, 'Pending')");
    $stmt->execute([$_SESSION['user_id'],$reason,$start,$end]);
    out(['ok'=>true,'message'=>'Leave request submitted.']);
}

if ($action==='author_leaves' && $_SESSION['role']==='author') {
    $stmt=$pdo->query("SELECT l.*,u.name employee_name,u.email employee_email FROM leaves l JOIN users u ON u.id=l.employee_id ORDER BY CASE WHEN l.status='Pending' THEN 0 ELSE 1 END, l.created_at DESC");
    $leaves=$stmt->fetchAll();
    foreach($leaves as &$leave){
        $s=$pdo->prepare("SELECT h.*,u.name author_name FROM leave_history h JOIN users u ON u.id=h.author_id WHERE h.leave_id=? ORDER BY h.created_at ASC");
        $s->execute([$leave['id']]); $leave['history']=$s->fetchAll();
    }
    out(['ok'=>true,'leaves'=>$leaves]);
}

if ($action==='decide' && $_SESSION['role']==='author') {
    $leaveId=(int)($_POST['leave_id']??0);
    $decision=$_POST['decision']??'';
    $note=trim($_POST['note']??'');
    if(!in_array($decision,['Approved','Rejected'],true)) out(['ok'=>false,'message'=>'Invalid decision.'],422);

    $pdo->beginTransaction();
    try{
        $s=$pdo->prepare("SELECT id,status FROM leaves WHERE id=? FOR UPDATE");
        $s->execute([$leaveId]); $leave=$s->fetch();
        if(!$leave) throw new Exception('Leave request not found.');
        if($leave['status']!=='Pending') throw new Exception('This request has already been decided.');
        $u=$pdo->prepare("UPDATE leaves SET status=?, decided_at=NOW() WHERE id=?");
        $u->execute([$decision,$leaveId]);
        $h=$pdo->prepare("INSERT INTO leave_history(leave_id,author_id,action,note) VALUES(?,?,?,?)");
        $h->execute([$leaveId,$_SESSION['user_id'],$decision,$note]);
        $pdo->commit();
        out(['ok'=>true,'message'=>'Decision recorded.']);
    }catch(Exception $e){
        if($pdo->inTransaction()) $pdo->rollBack();
        out(['ok'=>false,'message'=>$e->getMessage()],422);
    }
}

out(['ok'=>false,'message'=>'Unknown action.'],400);
?>
