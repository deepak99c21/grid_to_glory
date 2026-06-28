const $ = s => document.querySelector(s);
const qs = new URLSearchParams(location.search);
const apiUrl = url => url.startsWith('/api/') ? 'api/index.php?path=' + encodeURIComponent(url) : url;
const api = async (url, options={}) => { const res = await fetch(apiUrl(url), {headers:{'Content-Type':'application/json', ...(options.headers||{})}, ...options}); const data = await res.json().catch(()=>({})); if(!res.ok) throw new Error(data.error || 'Request failed'); return data; };
function initials(name){return (name||'?').split(' ').map(x=>x[0]).join('').slice(0,2).toUpperCase()}
function money(v){return Number(v||0).toLocaleString(undefined,{maximumFractionDigits:2})}
function safe(s){return String(s??'').replace(/[&<>"]/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]))}
const designDefaults={
  button_gradient_start:'#e91e63',button_gradient_end:'#00aeea',button_opacity:'1',button_radius:'8',leader_gradient_start:'#229ee9',leader_gradient_end:'#f80059',leader_row_opacity:'0.08',leader_table_scale:'100',quarter_card_width:'150',quarter_card_height:'132',quarter_card_opacity:'0.70',q1_x:'5.2',q1_y:'76',q2_x:'17',q2_y:'76',q3_x:'28.8',q3_y:'76',q4_x:'40.6',q4_y:'76',
  admin_button_bg:'#ffffff',admin_button_text:'#111111',admin_button_top:'18',admin_button_right:'22',admin_button_width:'92',admin_button_height:'38',admin_button_font_size:'13',
  home_button_bg:'#ffffff',home_button_text:'#111111',home_button_x:'0',home_button_y:'0',home_button_width:'92',home_button_height:'38',home_button_font_size:'13',
  logout_button_bg:'#ffe8ee',logout_button_text:'#b0002f',logout_button_x:'0',logout_button_y:'0',logout_button_width:'105',logout_button_height:'38',logout_button_font_size:'13',
  back_button_bg:'#ffffff',back_button_text:'#111111',back_button_top:'18',back_button_right:'7.5',back_button_width:'92',back_button_height:'38',back_button_font_size:'13'
};
function setting(settings,key){return (settings && settings[key]) || designDefaults[key] || ''}
function applyDesignSettings(settings){
  const r=document.documentElement.style;
  r.setProperty('--btn-grad-start',setting(settings,'button_gradient_start'));
  r.setProperty('--btn-grad-end',setting(settings,'button_gradient_end'));
  r.setProperty('--btn-opacity',setting(settings,'button_opacity'));
  r.setProperty('--btn-radius',setting(settings,'button_radius')+'px');
  r.setProperty('--leader-grad-start',setting(settings,'leader_gradient_start'));
  r.setProperty('--leader-grad-end',setting(settings,'leader_gradient_end'));
  r.setProperty('--leader-row-opacity',setting(settings,'leader_row_opacity'));
  r.setProperty('--leader-table-scale',setting(settings,'leader_table_scale')+'%');
  r.setProperty('--quarter-card-w',setting(settings,'quarter_card_width')+'px');
  r.setProperty('--quarter-card-h',setting(settings,'quarter_card_height')+'px');
  r.setProperty('--quarter-card-opacity',setting(settings,'quarter_card_opacity'));
  r.setProperty('--admin-btn-bg',setting(settings,'admin_button_bg'));
  r.setProperty('--admin-btn-text',setting(settings,'admin_button_text'));
  r.setProperty('--admin-btn-top',setting(settings,'admin_button_top')+'px');
  r.setProperty('--admin-btn-right',setting(settings,'admin_button_right')+'px');
  r.setProperty('--admin-btn-width',setting(settings,'admin_button_width')+'px');
  r.setProperty('--admin-btn-height',setting(settings,'admin_button_height')+'px');
  r.setProperty('--admin-btn-font-size',setting(settings,'admin_button_font_size')+'px');
  r.setProperty('--back-btn-bg',setting(settings,'back_button_bg'));
  r.setProperty('--back-btn-text',setting(settings,'back_button_text'));
  r.setProperty('--public-btn-top',setting(settings,'back_button_top')+'px');
  r.setProperty('--public-btn-right',setting(settings,'back_button_right')+'vw');
  r.setProperty('--public-btn-height',setting(settings,'back_button_height')+'px');
  r.setProperty('--public-btn-padding-x','18px');
  r.setProperty('--public-btn-font-size',setting(settings,'back_button_font_size')+'px');
  r.setProperty('--back-btn-width',setting(settings,'back_button_width')+'px');
}
function quarterStyle(q,settings){const code=String(q.code||'').toLowerCase();const x=setting(settings,code+'_x'), y=setting(settings,code+'_y');return `left:${x}%;top:${y}%;width:var(--quarter-card-w);min-height:var(--quarter-card-h);`; }
async function downloadScreenshot(targetSelector, filename){
  const el = document.querySelector(targetSelector); if(!el) return alert('Screenshot area not found.');
  const clone = el.cloneNode(true);
  function inlineStyles(src, dst){ const cs = getComputedStyle(src); let cssText=''; for (const prop of cs) cssText += `${prop}:${cs.getPropertyValue(prop)};`; dst.setAttribute('style',cssText); Array.from(src.children).forEach((child,i)=>inlineStyles(child,dst.children[i])); }
  inlineStyles(el, clone); clone.style.width = el.scrollWidth + 'px'; clone.style.minHeight = el.scrollHeight + 'px';
  const xhtml = new XMLSerializer().serializeToString(clone);
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${el.scrollWidth}" height="${el.scrollHeight}"><foreignObject width="100%" height="100%">${xhtml}</foreignObject></svg>`;
  const img = new Image(); const url = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
  img.onload = () => { const canvas = document.createElement('canvas'); canvas.width=el.scrollWidth*2; canvas.height=el.scrollHeight*2; const ctx=canvas.getContext('2d'); ctx.scale(2,2); ctx.drawImage(img,0,0); const a=document.createElement('a'); a.download=filename||'screenshot.png'; a.href=canvas.toDataURL('image/png'); a.click(); };
  img.onerror = () => alert('Screenshot capture failed. Please use Windows Snipping Tool as backup.'); img.src = url;
}
function setHomeTitle(text){ const el=$('#homeTitle'); if(!el) return; let raw=String(text||'GRID TO\nGLORY').trim(); let parts=raw.split(/\n|\|/).map(x=>x.trim()).filter(Boolean); if(parts.length<2){ const m=raw.match(/^(.*?GRID\s*TO)\s*(GLORY)$/i); if(m) parts=[m[1].replace(/GRID\s*TO/i,'GRID TO'), 'GLORY']; } el.innerHTML = `${safe(parts[0]||'GRID TO')}<span class="bold">${safe(parts[1]||'GLORY')}</span>`; }
async function renderHome(){
  if(!$('#quarterGrid')) return;
  const [quarters, settings] = await Promise.all([api('/api/public/quarters'), api('/api/public/settings')]);
  applyDesignSettings(settings); setHomeTitle(settings.home_title); if($('#homeTagline')) $('#homeTagline').textContent = settings.home_tagline || 'A GAMIFIED FRIENDLY COMPETITION TO #DRIVEIMPACT';
  if($('#heroArt')) $('#heroArt').innerHTML = settings.hero_image ? `<img src="${settings.hero_image}" alt="hero">` : 'GTG';
  $('#quarterGrid').innerHTML = quarters.map(q => {
    const locked = !q.is_unlocked; const href = locked ? '#' : `teams.html?quarter=${q.id}`; const bg = q.background_image ? `<div class="bg" style="background-image:url('${q.background_image}')"></div>` : '<div class="bg"></div>';
    return `<a class="quarter-card ${locked?'locked':''}" style="${quarterStyle(q,settings)}" href="${href}" onclick="${locked?'alert(\'Quarter locked\');return false;':''}">${bg}<span class="lock-icon">${locked?'🔒':'🔓'}</span><div class="quarter-name">${safe(q.title || q.code)}</div></a>`
  }).join('');
}
async function renderTeams(){
  if(!$('#teamGrid')) return; const qid = qs.get('quarter') || 1;
  const [data, settings] = await Promise.all([api(`/api/public/quarters/${qid}/teams`), api('/api/public/settings')]);
  applyDesignSettings(settings);
  if($('#teamSub')) $('#teamSub').textContent = settings.team_page_subtitle || 'CHOOSE A NAME FOR YOUR TEAM, CHOOSE A TEAM COLOR';
  if($('#leaderboardLink')) $('#leaderboardLink').href = `leaderboard.html?quarter=${qid}`;
  $('#teamGrid').innerHTML = data.teams.map(t => `<a class="team-card" style="--teamColor:${safe(t.card_color || '#00b8b0')}" href="result.html?quarter=${qid}&team=${t.id}"><div class="band"></div><h2>${safe(t.name)}</h2><p>${safe(t.subtitle || t.leader_name || '')}</p><div class="visual-panel">${t.leader_image ? `<img class="leader-img" src="${t.leader_image}" alt="${safe(t.leader_name)}">` : `<div class="placeholder-person">${initials(t.leader_name)}</div>`}</div><div class="score-pill">${money(t.team_total)}</div></a>`).join('');
}
async function renderResult(){
  if(!$('#resultTable')) return; const qid = qs.get('quarter') || 1, teamId = qs.get('team');
  const [data, settings] = await Promise.all([api(`/api/public/quarters/${qid}/teams/${teamId}/results`), api('/api/public/settings')]); applyDesignSettings(settings);
  if($('#backTeams')) $('#backTeams').href = `teams.html?quarter=${qid}`; $('#pageTitle').textContent = data.team.name; $('#pageSub').textContent = data.quarter.title + ' | ' + data.team.leader_name; $('#teamTotal').textContent = money(data.teamTotal);
  $('#resultTable').innerHTML = `<table><thead><tr><th>Member</th><th>Role</th><th>Category</th><th>Points</th><th>Quantity</th><th>Bonus</th><th>Total</th></tr></thead><tbody>${data.rows.map(r=>`<tr><td>${safe(r.name)}</td><td>${safe(r.role||'')}</td><td>${safe(r.category||'')}</td><td>${money(r.points)}</td><td>${money(r.quantity)}</td><td>${money(r.bonus)}</td><td><b>${money(r.total)}</b></td></tr>`).join('')}</tbody></table>`;
}
async function renderLeaderboard(){
  if(!$('#leaderboardTable')) return; const qid = qs.get('quarter') || 1; const data = await api(`/api/public/quarters/${qid}/leaderboard`); applyDesignSettings(data.settings||{});
  if($('#backTeams')) $('#backTeams').href = `teams.html?quarter=${qid}`; $('#leaderDate').textContent = data.settings.leaderboard_date || 'AS OF JUNE 26';
  const teams = data.teams; const rows = data.matrix;
  $('#leaderboardTable').innerHTML = `<table class="leaderboard-table"><thead><tr><th>Activities</th>${teams.map(t=>`<th>${safe(t.leaderboard_name || t.leader_name || t.name)}</th>`).join('')}</tr></thead><tbody>${rows.map(r=>`<tr><td>${safe(r.category)}</td>${teams.map(t=>`<td>${money(r.values[t.id]||0)}</td>`).join('')}</tr>`).join('') || `<tr><td colspan="${teams.length+1}">No scores added</td></tr>`}</tbody><tfoot><tr><td>TOTAL POINTS</td>${teams.map(t=>`<td>${money(data.totals[t.id]||0)}</td>`).join('')}</tr></tfoot></table>`;
}
renderHome(); renderTeams(); renderResult(); renderLeaderboard();
