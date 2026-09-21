async function api(action, options={}) {
  const url='api.php?action='+encodeURIComponent(action);
  const res=await fetch(url,{method:options.method||'GET',body:options.body||undefined,headers:options.body?{'Content-Type':'application/x-www-form-urlencoded'}:undefined,cache:'no-store'});
  return res.json();
}
const esc=s=>String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
const fmtDate=s=>s?new Date(s+'T00:00:00').toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'}):'';
const statusClass=s=>s.toLowerCase();

function historyHtml(history){
  if(!history?.length) return '<div class="history-item">No author action yet.</div>';
  return history.map(h=>`<div class="history-item"><b>${esc(h.author_name)}</b> • ${esc(h.action)}${h.note?' — '+esc(h.note):''}<span> · ${new Date(h.created_at).toLocaleString('en-IN')}</span></div>`).join('');
}

async function loadEmployee(){
  const r=await api('employee_leaves'); if(!r.ok)return;
  const leaves=r.leaves||[];
  document.getElementById('statTotal').textContent=leaves.length;
  document.getElementById('statPending').textContent=leaves.filter(x=>x.status==='Pending').length;
  document.getElementById('statApproved').textContent=leaves.filter(x=>x.status==='Approved').length;
  document.getElementById('statRejected').textContent=leaves.filter(x=>x.status==='Rejected').length;
  document.getElementById('inboxCount').textContent=leaves.filter(x=>x.status!=='Pending').length;
  const html=leaves.length?leaves.map(l=>`<article class="leave-card"><div class="leave-top"><div><div class="leave-reason">${esc(l.reason)}</div><div class="leave-date">${fmtDate(l.start_date)} → ${fmtDate(l.end_date)}</div></div><span class="status ${statusClass(l.status)}">${esc(l.status)}</span></div><div class="history">${historyHtml(l.history)}</div></article>`).join(''):'<div class="muted">No leave requests yet. Your submitted requests will appear here.</div>';
  document.getElementById('employeeLeaves').innerHTML=html;
  document.getElementById('drawerList').innerHTML=html;
}
if(window.VERACORE_ROLE==='employee'){
  document.getElementById('leaveForm').addEventListener('submit',async e=>{
    e.preventDefault(); const msg=document.getElementById('formMsg'); msg.innerHTML='<div class="muted">Submitting...</div>';
    const body=new URLSearchParams(new FormData(e.target)); const r=await api('apply_leave',{method:'POST',body});
    msg.innerHTML=`<div class="${r.ok?'alert':'alert error'}">${esc(r.message)}</div>`; if(r.ok){e.target.reset();loadEmployee();}
  });
  document.getElementById('inboxBtn').onclick=()=>document.getElementById('inboxDrawer').classList.remove('hidden');
  document.getElementById('closeDrawer').onclick=()=>document.getElementById('inboxDrawer').classList.add('hidden');
  loadEmployee(); setInterval(loadEmployee,3000);
}

let decisionId=null, decisionValue=null;
function openDecision(id,decision){
  decisionId=id;decisionValue=decision;
  document.getElementById('decisionKicker').textContent=decision==='Approved'?'APPROVE REQUEST':'REJECT REQUEST';
  document.getElementById('decisionTitle').textContent=decision==='Approved'?'Approve this request?':'Reject this request?';
  document.getElementById('confirmDecision').textContent=decision==='Approved'?'Confirm approval':'Confirm rejection';
  document.getElementById('decisionNote').value='';
  document.getElementById('decisionModal').classList.remove('hidden');
}
async function loadAuthor(){
  const r=await api('author_leaves'); if(!r.ok)return; const leaves=r.leaves||[];
  const p=leaves.filter(x=>x.status==='Pending').length,a=leaves.filter(x=>x.status==='Approved').length,j=leaves.filter(x=>x.status==='Rejected').length;
  document.getElementById('pendingCount').textContent=p; document.getElementById('aPending').textContent=p;document.getElementById('aApproved').textContent=a;document.getElementById('aRejected').textContent=j;
  document.getElementById('authorLeaves').innerHTML=leaves.length?leaves.map(l=>`<article class="author-card"><div class="author-head"><div><div class="employee-name">${esc(l.employee_name)}</div><div class="employee-email">${esc(l.employee_email)}</div></div><span class="status ${statusClass(l.status)}">${esc(l.status)}</span></div><div class="reason-box"><b>${esc(l.reason)}</b><br>${fmtDate(l.start_date)} → ${fmtDate(l.end_date)}</div><div class="history">${historyHtml(l.history)}</div>${l.status==='Pending'?`<div class="author-actions"><button class="approve" onclick="openDecision(${l.id},'Approved')">Approve</button><button class="reject" onclick="openDecision(${l.id},'Rejected')">Reject</button></div>`:''}</article>`).join(''):'<div class="muted">No leave requests found.</div>';
}
if(window.VERACORE_ROLE==='author'){
  document.getElementById('closeDecision').onclick=()=>document.getElementById('decisionModal').classList.add('hidden');
  document.getElementById('confirmDecision').onclick=async()=>{
    const body=new URLSearchParams({leave_id:decisionId,decision:decisionValue,note:document.getElementById('decisionNote').value});
    const r=await api('decide',{method:'POST',body});
    document.getElementById('decisionModal').classList.add('hidden');
    if(!r.ok)alert(r.message); loadAuthor();
  };
  loadAuthor(); setInterval(loadAuthor,3000);
}
