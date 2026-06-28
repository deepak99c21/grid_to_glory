const $ = s => document.querySelector(s);
const tokenKey = 'sq_admin_token';
const apiUrl = url => url.startsWith('/api/') ? 'api/index.php?path=' + encodeURIComponent(url) : url;
const api = async (url, options={}) => {
  const res = await fetch(apiUrl(url), {headers:{'Content-Type':'application/json', ...(options.headers||{})}, ...options});
  const data = await res.json().catch(()=>({}));
  if(!res.ok) throw new Error(data.error || 'Request failed');
  return data;
};
const authHeaders = () => ({'Content-Type':'application/json', Authorization:`Bearer ${localStorage.getItem(tokenKey)}`});
function safe(s){return String(s??'').replace(/[&<>"]/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]))}
function money(v){return Number(v||0).toLocaleString(undefined,{maximumFractionDigits:2})}
function msg(text){const m=$('#msg');m.textContent=text;m.classList.remove('hidden');setTimeout(()=>m.classList.add('hidden'),4200)}
let state={quarters:[],teams:[],members:[],settings:{}};
const CATEGORY_DEFS=[
  {category:'Press Release with Clients',points:100,details:'Collaborate with clients to feature them in a press release, showcasing their partnership and mutual achievements.',bonus:'10 points for doing more than 1 advocacy asset at a time'},
  {category:'Client testimonial / case study',points:80,details:'Named client testimonial or case study published publicly – Infosys or external site (e.g. WSJ, FT etc.)',bonus:'10 points for doing more than 1 advocacy asset at a time'},
  {category:'Quarterly results quote or Feature Note with client or Strategic Partner',points:50,details:'Quotes from clients, which get published during quarterly results (IFRS document) on our website OR a Feature Note on our website.',bonus:'10 points for C-level execs'},
  {category:'Client Speaker at Events',points:50,details:'Client speaker at event. Bonus can be earned if event is public, involves endorsement and exec is C-level.',bonus:'10 points for endorsing Infosys + 10 points for public event + 10 points for C-level speaker'},
  {category:'Thought Leadership with Clients',points:50,details:'Externally published thought leadership content with clients e.g. Fireside chat, Interview, POV, etc.',bonus:'10 points for endorsement, 10 points for C-level exec, 10 points for endorsement mentioning Topaz, Cobalt or Aster name'},
  {category:'Client Attendee/meeting at Events',points:30,details:'Secure client attendance or meetings at events.',bonus:'10 points for C-level execs'},
  {category:'Analyst or deal advisor references by clients',points:30,details:'Named client testimonial or case study published publicly – Infosys or external site (e.g. WSJ, FT etc.)',bonus:'10 points for C-level execs'},
  {category:'MSA clause for Marketing',points:30,details:'Signed MSA with Marketing clause to do public advocacy (new deal signing or renewal).',bonus:'10 points if commitment on number of assets per year'},
  {category:'MSD opportunity Tagging',points:2,details:'Points awarded on opportunity tagged: 2 points - $1 Mn to $5 Mn; 5 points - $5 Mn to $20 Mn; 10 points - $20 Mn+ to $50 Mn; 20 points - Above $50 Mn.',bonus:'Bonus points for MKTG originated tagging: 2 points - $1 Mn to $5 Mn; 5 points - $5 Mn to $20 Mn; 10 points - $20 Mn+ to $50 Mn; 20 points - Above $50 Mn'},
  {category:'Social Media Tagging Clients & Endorsing Infosys',points:20,details:'Encourage clients to mention Infosys on social media platforms. Bonus for client tagging Infosys.',bonus:'10 points if clients posts from their handle, endorsing Infosys; 10 points for endorsing Aster, Topaz, Cobalt'},
  {category:'Insurance Advisory Council-Membership',points:50,details:'Points are earned by MCOs when a new member is added in the council.',bonus:'NA'},
  {category:'Social Media Tagging Strategic Partners & Endorsing Infosys',points:10,details:'Encourage clients to mention Infosys on social media platforms. Bonus for client tagging Infosys.',bonus:'10 points if partners posts from their handle, endorsing Infosys'}
];
const DESIGN_DEFAULTS={button_gradient_start:'#e91e63',button_gradient_end:'#00aeea',button_opacity:'1',button_radius:'8',leader_gradient_start:'#229ee9',leader_gradient_end:'#f80059',leader_row_opacity:'0.08',leader_table_scale:'100',quarter_card_width:'150',quarter_card_height:'132',quarter_card_opacity:'0.70',q1_x:'5.2',q1_y:'76',q2_x:'17',q2_y:'76',q3_x:'28.8',q3_y:'76',q4_x:'40.6',q4_y:'76',admin_button_bg:'#ffffff',admin_button_text:'#111111',admin_button_top:'18',admin_button_right:'22',admin_button_width:'92',admin_button_height:'38',admin_button_font_size:'13',home_button_bg:'#f0fbff',home_button_text:'#075985',home_button_x:'0',home_button_y:'0',home_button_width:'132',home_button_height:'56',home_button_font_size:'13',logout_button_bg:'#ffe8ee',logout_button_text:'#b0002f',logout_button_x:'0',logout_button_y:'0',logout_button_width:'146',logout_button_height:'56',logout_button_font_size:'13',back_button_bg:'#ffffff',back_button_text:'#111111',back_button_top:'18',back_button_right:'7.5',back_button_width:'92',back_button_height:'38',back_button_font_size:'13'};
function applyAdminButtonDesign(settings={}){
  const r=document.documentElement.style;
  const val=k=>(settings && settings[k] !== undefined && settings[k] !== null && settings[k] !== '') ? settings[k] : (DESIGN_DEFAULTS[k] || '');
  r.setProperty('--btn-grad-start',val('button_gradient_start'));
  r.setProperty('--btn-grad-end',val('button_gradient_end'));
  r.setProperty('--btn-opacity',val('button_opacity'));
  r.setProperty('--btn-radius',val('button_radius')+'px');
  r.setProperty('--leader-grad-start',val('leader_gradient_start'));
  r.setProperty('--leader-grad-end',val('leader_gradient_end'));
  r.setProperty('--leader-row-opacity',val('leader_row_opacity'));
  r.setProperty('--leader-table-scale',val('leader_table_scale')+'%');
  r.setProperty('--quarter-card-w',val('quarter_card_width')+'px');
  r.setProperty('--quarter-card-h',val('quarter_card_height')+'px');
  r.setProperty('--quarter-card-opacity',val('quarter_card_opacity'));
  r.setProperty('--admin-btn-bg',val('admin_button_bg'));r.setProperty('--admin-btn-text',val('admin_button_text'));r.setProperty('--admin-btn-top',val('admin_button_top')+'px');r.setProperty('--admin-btn-right',val('admin_button_right')+'px');r.setProperty('--admin-btn-width',val('admin_button_width')+'px');r.setProperty('--admin-btn-height',val('admin_button_height')+'px');r.setProperty('--admin-btn-font-size',val('admin_button_font_size')+'px');
  r.setProperty('--home-btn-bg',val('home_button_bg'));r.setProperty('--home-btn-text',val('home_button_text'));r.setProperty('--home-btn-x',val('home_button_x')+'px');r.setProperty('--home-btn-y',val('home_button_y')+'px');r.setProperty('--home-btn-width',val('home_button_width')+'px');r.setProperty('--home-btn-height',val('home_button_height')+'px');r.setProperty('--home-btn-font-size',val('home_button_font_size')+'px');
  r.setProperty('--logout-btn-bg',val('logout_button_bg'));r.setProperty('--logout-btn-text',val('logout_button_text'));r.setProperty('--logout-btn-x',val('logout_button_x')+'px');r.setProperty('--logout-btn-y',val('logout_button_y')+'px');r.setProperty('--logout-btn-width',val('logout_button_width')+'px');r.setProperty('--logout-btn-height',val('logout_button_height')+'px');r.setProperty('--logout-btn-font-size',val('logout_button_font_size')+'px');
  r.setProperty('--back-btn-bg',val('back_button_bg'));r.setProperty('--back-btn-text',val('back_button_text'));r.setProperty('--public-btn-top',val('back_button_top')+'px');r.setProperty('--public-btn-right',val('back_button_right')+'vw');r.setProperty('--back-btn-width',val('back_button_width')+'px');r.setProperty('--public-btn-height',val('back_button_height')+'px');r.setProperty('--public-btn-font-size',val('back_button_font_size')+'px');
}

function setAdminUiState(isLoggedIn){
  const loginPanel = $('#loginPanel');
  const adminPanel = $('#adminPanel');
  const logoutBtn = $('#adminLogoutBtn');
  document.body.classList.toggle('admin-logged-in', !!isLoggedIn);
  document.body.classList.toggle('admin-logged-out', !isLoggedIn);
  if(loginPanel) loginPanel.classList.toggle('hidden', !!isLoggedIn);
  if(adminPanel) adminPanel.classList.toggle('hidden', !isLoggedIn);
  if(logoutBtn) logoutBtn.classList.toggle('hidden', !isLoggedIn);
}

async function login(){try{const d=await api('/api/admin/login',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({adminKey:$('#adminKey').value})});localStorage.setItem(tokenKey,d.token);setAdminUiState(true);await loadDash();showTab('lookTab');}catch(e){setAdminUiState(false);msg(e.message)}}
async function loadDash(){try{state=await api('/api/admin/dashboard',{headers:authHeaders()});setAdminUiState(true);applyAdminButtonDesign(state.settings||{});renderLook();renderDesign();renderQuarters();renderTeams();renderMembers();fillSelects();renderCategoryReference();showTab(document.querySelector('.tab:not(.hidden)')?.id || 'lookTab');loadSystemInfo().catch(()=>{});}catch(e){localStorage.removeItem(tokenKey);setAdminUiState(false);msg(e.message)}}
function adminLogout(ev){
  if(ev && ev.preventDefault) ev.preventDefault();
  if(ev && ev.stopPropagation) ev.stopPropagation();
  try{
    localStorage.removeItem(tokenKey);
    sessionStorage.removeItem(tokenKey);
    localStorage.removeItem('adminToken');
    localStorage.removeItem('sq_token');
  }catch(e){}
  setAdminUiState(false);
  document.querySelectorAll('.tab').forEach(x=>x.classList.add('hidden'));
  document.querySelectorAll('.navbtn').forEach(btn=>btn.classList.remove('active'));
  const keyInput=$('#adminKey');
  if(keyInput){ keyInput.value=''; setTimeout(()=>keyInput.focus(),50); }
  msg('Logged out successfully');
}
function logout(ev){ return adminLogout(ev); }
window.adminLogout=adminLogout;
window.logout=logout;
function renderLook(){const f=$('#lookForm');if(!f)return;['leaderboard_date','team_page_subtitle'].forEach(k=>{if(f[k])f[k].value=state.settings[k]||''})}
function renderDesign(){const f=$('#designForm');if(!f)return;Object.entries(DESIGN_DEFAULTS).forEach(([k,v])=>{if(f[k])f[k].value=(state.settings[k]!==undefined&&state.settings[k]!==null&&state.settings[k]!==''?state.settings[k]:v);});applyAdminButtonDesign(state.settings||{});}
function renderCategoryReference(){const box=$('#categoryReferenceRows');if(!box)return;box.innerHTML=CATEGORY_DEFS.map(c=>`<tr><td>${safe(c.category)}</td><td>${money(c.points)}</td><td>${safe(c.details)}</td><td>${safe(c.bonus)}</td></tr>`).join('')}
function applyCategoryDefault(){const input=$('#resultCategory'), points=$('#resultPoints'), help=$('#categoryHelp');if(!input||!points)return;const c=CATEGORY_DEFS.find(x=>x.category===input.value);if(c){points.value=c.points;if(help)help.textContent=`Default points: ${c.points}. ${c.bonus}`;}}
async function saveDesign(ev){ev.preventDefault();const fd=new FormData(ev.target);const previewSettings=Object.fromEntries(fd.entries());applyAdminButtonDesign(previewSettings);await fetch(apiUrl('/api/admin/settings'),{method:'POST',headers:{Authorization:`Bearer ${localStorage.getItem(tokenKey)}`},body:fd}).then(async r=>{if(!r.ok)throw new Error((await r.json()).error)});msg('Design settings saved and applied');loadDash();}
function resetDesignDefaults(){const f=$('#designForm');if(!f)return;Object.entries(DESIGN_DEFAULTS).forEach(([k,v])=>{if(f[k])f[k].value=v;});applyAdminButtonDesign(DESIGN_DEFAULTS);msg('Default values filled. Click Save Design Settings to apply.');}
async function saveLook(ev){ev.preventDefault();const fd=new FormData(ev.target);await fetch(apiUrl('/api/admin/settings'),{method:'POST',headers:{Authorization:`Bearer ${localStorage.getItem(tokenKey)}`},body:fd}).then(async r=>{if(!r.ok)throw new Error((await r.json()).error)});msg('Page text saved');loadDash()}
function renderQuarters(){const box=$('#quarterAdmin');box.innerHTML=state.quarters.map(q=>`<div class="list-item"><form onsubmit="saveQuarter(event,${q.id})" enctype="multipart/form-data"><div class="form-grid"><div class="field"><label>${q.code} Name</label><input name="title" value="${safe(q.title)}"></div><div class="field"><label>Status</label><select name="is_unlocked"><option value="1" ${q.is_unlocked?'selected':''}>Unlocked</option><option value="0" ${!q.is_unlocked?'selected':''}>Locked</option></select></div><div class="field"><label>Background PNG/JPG/WEBP</label><input name="backgroundImage" type="file" accept=".png,.jpg,.jpeg,.webp"><small class="muted">Maximum upload size is controlled from .env.</small></div><div class="field"><label>Current</label><span class="badge ${q.is_unlocked?'open':'lock'}">${q.is_unlocked?'🔓 Unlocked':'🔒 Locked'}</span></div></div><br><button class="btn primary">Save ${q.code}</button></form></div>`).join('')}
async function saveQuarter(ev,id){ev.preventDefault();const fd=new FormData(ev.target);fd.append('_method','PUT');await fetch(apiUrl(`/api/admin/quarters/${id}`),{method:'POST',headers:{Authorization:`Bearer ${localStorage.getItem(tokenKey)}`},body:fd}).then(async r=>{if(!r.ok)throw new Error((await r.json()).error)});msg('Quarter updated');loadDash()}
function renderTeams(){const box=$('#teamList');box.innerHTML=state.teams.map(t=>`<div class="list-item"><div class="row"><img class="avatar" src="${t.leader_image||''}" onerror="this.outerHTML='<div class=&quot;avatar placeholder&quot;>${safe((t.leader_name||'?').slice(0,1))}</div>'"><div><b>${safe(t.name)}</b><div class="muted">Leader: ${safe(t.leader_name)}</div><div class="muted">Leaderboard: ${safe(t.leaderboard_name||t.leader_name||t.name)}</div><div class="muted">Subtitle: ${safe(t.subtitle||'')}</div><div class="muted">Color: ${safe(t.card_color||'')}</div></div><button class="btn" onclick="editTeam(${t.id})">Edit</button><button class="btn danger" onclick="deleteTeam(${t.id})">Delete</button></div></div>`).join('')||'<div class="message">No team leaders added yet.</div>'}
async function saveTeam(ev){ev.preventDefault();const f=ev.target;const id=f.team_id.value;const fd=new FormData(f);fd.delete('team_id');const url=id?`/api/admin/teams/${id}`:'/api/admin/teams';const method='POST';if(id)fd.append('_method','PUT');await fetch(apiUrl(url),{method,headers:{Authorization:`Bearer ${localStorage.getItem(tokenKey)}`},body:fd}).then(async r=>{if(!r.ok)throw new Error((await r.json()).error)});resetTeamForm();msg(id?'Team leader updated':'Team leader added');loadDash()}
function editTeam(id){const t=state.teams.find(x=>x.id===id);if(!t)return;showTab('teamsTab');const f=$('#teamForm');f.team_id.value=t.id;f.name.value=t.name||'';f.leader_name.value=t.leader_name||'';f.leaderboard_name.value=t.leaderboard_name||'';f.subtitle.value=t.subtitle||'';f.card_color.value=t.card_color||'#00b8b0';f.sort_order.value=t.sort_order||0;$('#teamFormTitle').textContent='Edit Team Leader';$('#teamSaveBtn').textContent='Update Team Leader';$('#teamCancelBtn').classList.remove('hidden');$('#teamImageNote').textContent='Leave image blank to keep existing image.';f.scrollIntoView({behavior:'smooth',block:'start'});}
function resetTeamForm(){const f=$('#teamForm');f.reset();f.team_id.value='';f.card_color.value='#00b8b0';$('#teamFormTitle').textContent='Add Team Leader';$('#teamSaveBtn').textContent='Add Team Leader';$('#teamCancelBtn').classList.add('hidden');$('#teamImageNote').textContent='PNG/JPG/WEBP only. Rectangular image also fits.';}
async function deleteTeam(id){if(!confirm('Delete this team and all members/results?'))return;await api(`/api/admin/teams/${id}`,{method:'DELETE',headers:authHeaders()});msg('Team deleted');loadDash()}
function fillSelects(){const opts=state.teams.map(t=>`<option value="${t.id}">${safe(t.name)}</option>`).join('');$('#memberTeam').innerHTML=opts;$('#resultTeam').innerHTML=opts;$('#resultQuarter').innerHTML=state.quarters.map(q=>`<option value="${q.id}">${safe(q.title||q.code)}</option>`).join('');fillMemberSelect()}
function fillMemberSelect(){const teamId=$('#resultTeam').value;const members=state.members.filter(m=>String(m.team_id)===String(teamId));$('#resultMember').innerHTML=members.map(m=>`<option value="${m.id}">${safe(m.name)}</option>`).join('')}
function renderMembers(){const box=$('#memberList');box.innerHTML=state.members.map(m=>{const t=state.teams.find(x=>x.id===m.team_id);return `<div class="list-item row"><b>${safe(m.name)}</b><span class="muted">${safe(m.role||'')} | ${safe(t?t.name:'')}</span><button class="btn" onclick="editMember(${m.id})">Edit</button><button class="btn danger" onclick="deleteMember(${m.id})">Delete</button></div>`}).join('')||'<div class="message">No members added yet.</div>'}
async function saveMember(ev){ev.preventDefault();const f=ev.target;const id=f.member_id.value;const body={team_id:f.team_id.value,name:f.name.value,role:f.role.value};if(id){await api(`/api/admin/members/${id}`,{method:'PUT',headers:authHeaders(),body:JSON.stringify(body)});}else{await api('/api/admin/members',{method:'POST',headers:authHeaders(),body:JSON.stringify(body)});}resetMemberForm();msg(id?'Member updated':'Member added');loadDash()}
function editMember(id){const m=state.members.find(x=>x.id===id);if(!m)return;showTab('membersTab');const f=$('#memberForm');f.member_id.value=m.id;f.team_id.value=m.team_id;f.name.value=m.name||'';f.role.value=m.role||'';$('#memberFormTitle').textContent='Edit Team Member';$('#memberSaveBtn').textContent='Update Member';$('#memberCancelBtn').classList.remove('hidden');f.scrollIntoView({behavior:'smooth',block:'start'});}
function resetMemberForm(){const f=$('#memberForm');f.reset();f.member_id.value='';$('#memberFormTitle').textContent='Add Team Member';$('#memberSaveBtn').textContent='Add Member';$('#memberCancelBtn').classList.add('hidden');}
async function deleteMember(id){if(!confirm('Delete this member and result entries?'))return;await api(`/api/admin/members/${id}`,{method:'DELETE',headers:authHeaders()});msg('Member deleted');loadDash()}
async function saveResult(ev){ev.preventDefault();const f=ev.target;const body={quarter_id:f.quarter_id.value,member_id:f.member_id.value,category:f.category.value,points:f.points.value,quantity:f.quantity.value,bonus:f.bonus.value};const d=await api('/api/admin/results',{method:'POST',headers:authHeaders(),body:JSON.stringify(body)});msg(`Result saved. Total: ${d.calc.total}`);loadAdminResults().catch(()=>{})}
async function loadAdminResults(){try{const d=await api('/api/admin/results',{headers:authHeaders()});const box=$('#adminResultTable');if(!box)return;box.innerHTML=`<table><thead><tr><th>Quarter</th><th>Team</th><th>Leader</th><th>Member</th><th>Role</th><th>Category</th><th>Points</th><th>Quantity</th><th>Bonus</th><th>Total</th></tr></thead><tbody>${d.rows.map(r=>`<tr><td>${safe(r.quarter)}</td><td>${safe(r.team_name)}</td><td>${safe(r.leader_name)}</td><td>${safe(r.member_name)}</td><td>${safe(r.role||'')}</td><td>${safe(r.category||'')}</td><td>${money(r.points)}</td><td>${money(r.quantity)}</td><td>${money(r.bonus)}</td><td><b>${money(r.total)}</b></td></tr>`).join('')}</tbody></table>`;}catch(e){msg(e.message)}}
async function downloadAdminCsv(){const res=await fetch(apiUrl('/api/admin/export/results.csv'),{headers:{Authorization:`Bearer ${localStorage.getItem(tokenKey)}`}});if(!res.ok){msg('CSV download failed');return}downloadBlob(await res.blob(),'sales_quarter_results.csv')}
function downloadBlob(blob, filename){const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download=filename;a.click();URL.revokeObjectURL(url)}
async function downloadDatabaseBackup(){const res=await fetch(apiUrl('/api/admin/backup/database'),{headers:{Authorization:`Bearer ${localStorage.getItem(tokenKey)}`}});if(!res.ok){msg((await res.json().catch(()=>({error:'Database backup failed'}))).error);return}downloadBlob(await res.blob(),'grid_to_glory_mysql_backup.sql');msg('MySQL backup downloaded')}
async function downloadFullBackupZip(){const res=await fetch(apiUrl('/api/admin/backup/full.zip'),{headers:{Authorization:`Bearer ${localStorage.getItem(tokenKey)}`}});if(!res.ok){msg((await res.json().catch(()=>({error:'Full backup failed'}))).error);return}downloadBlob(await res.blob(),'grid_to_glory_full_backup.zip');msg('Full backup ZIP downloaded')}
async function loadSystemInfo(){const box=$('#systemInfo');if(!box)return;const d=await api('/api/admin/system-info',{headers:authHeaders()});box.innerHTML=`Mode: <b>${safe(d.node_env)}</b> | Port: <b>${safe(d.port)}</b> | Upload limit: <b>${safe(d.upload_max_mb)} MB</b> | Allowed: <b>${safe(d.allowed_uploads.join(', '))}</b>`}
async function downloadScreenshot(targetSelector, filename){const el=document.querySelector(targetSelector);if(!el)return msg('Screenshot area not found');const clone=el.cloneNode(true);function inlineStyles(src,dst){const cs=getComputedStyle(src);let cssText='';for(const prop of cs)cssText+=`${prop}:${cs.getPropertyValue(prop)};`;dst.setAttribute('style',cssText);Array.from(src.children).forEach((child,i)=>inlineStyles(child,dst.children[i]));}inlineStyles(el,clone);clone.style.width=el.scrollWidth+'px';clone.style.minHeight=el.scrollHeight+'px';const xhtml=new XMLSerializer().serializeToString(clone);const svg=`<svg xmlns="http://www.w3.org/2000/svg" width="${el.scrollWidth}" height="${el.scrollHeight}"><foreignObject width="100%" height="100%">${xhtml}</foreignObject></svg>`;const img=new Image();const url='data:image/svg+xml;charset=utf-8,'+encodeURIComponent(svg);img.onload=()=>{const canvas=document.createElement('canvas');canvas.width=el.scrollWidth*2;canvas.height=el.scrollHeight*2;const ctx=canvas.getContext('2d');ctx.scale(2,2);ctx.drawImage(img,0,0);const a=document.createElement('a');a.download=filename||'screenshot.png';a.href=canvas.toDataURL('image/png');a.click();};img.onerror=()=>msg('Screenshot capture failed.');img.src=url;}
async function changeKey(ev){ev.preventDefault();const k=$('#newKey').value;if(k!==$('#confirmKey').value){msg('New key and confirm key do not match');return}await api('/api/admin/change-key',{method:'POST',headers:authHeaders(),body:JSON.stringify({newKey:k})});ev.target.reset();msg('Admin key changed')}
function showTab(id){document.querySelectorAll('.tab').forEach(x=>x.classList.add('hidden'));$('#'+id).classList.remove('hidden');document.querySelectorAll('.navbtn').forEach(btn=>btn.classList.toggle('active',btn.dataset.tab===id));}
window.onload=()=>{fetch(apiUrl('/api/public/settings')).then(r=>r.json()).then(applyAdminButtonDesign).catch(()=>{});if(localStorage.getItem(tokenKey)){setAdminUiState(true);loadDash();showTab('lookTab');}else{setAdminUiState(false);}}


document.addEventListener('DOMContentLoaded',()=>{
  const btn=document.getElementById('adminLogoutBtn');
  if(btn){ btn.addEventListener('click', adminLogout); }
});
