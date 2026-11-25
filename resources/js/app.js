import Chart from 'chart.js/auto';
const sidebar=document.querySelector('#sidebar');
const navigationMedia=window.matchMedia('(max-width: 1023px)');
function setSidebar(open){
 if(!sidebar)return;
 const wasOpen=sidebar.classList.contains('open');open=Boolean(open&&navigationMedia.matches);
 sidebar.classList.toggle('open',open);sidebar.inert=navigationMedia.matches&&!open;
 sidebar.setAttribute('aria-hidden',String(navigationMedia.matches&&!open));
 if(open){sidebar.setAttribute('role','dialog');sidebar.setAttribute('aria-modal','true');}else{sidebar.removeAttribute('role');sidebar.removeAttribute('aria-modal');}
 document.querySelector('#sidebar-backdrop')?.classList.toggle('open',open);
 document.querySelector('#sidebar-toggle')?.setAttribute('aria-expanded',String(open));
 document.documentElement.classList.toggle('navigation-open',open);document.body.classList.toggle('navigation-open',open);
 const shell=document.querySelector('.app-shell');if(shell)shell.inert=open;
 if(open)document.querySelector('#sidebar-close')?.focus({preventScroll:true});
 else if(wasOpen&&navigationMedia.matches)document.querySelector('#sidebar-toggle')?.focus({preventScroll:true});
}
document.querySelector('#sidebar-toggle')?.addEventListener('click',()=>setSidebar(!sidebar.classList.contains('open')));
document.querySelector('#sidebar-backdrop')?.addEventListener('click',()=>setSidebar(false));
document.querySelector('#sidebar-close')?.addEventListener('click',()=>setSidebar(false));
sidebar?.querySelectorAll('nav a').forEach(link=>link.addEventListener('click',()=>setSidebar(false)));
navigationMedia.addEventListener('change',()=>setSidebar(false));setSidebar(false);
document.addEventListener('keydown',e=>{
 if(e.key==='Escape')setSidebar(false);
 if(e.key!=='Tab'||!sidebar?.classList.contains('open'))return;
 const items=[...sidebar.querySelectorAll('a[href],button:not([disabled])')].filter(item=>item.getClientRects().length);
 if(e.shiftKey&&document.activeElement===items[0]){e.preventDefault();items.at(-1)?.focus();}
 else if(!e.shiftKey&&document.activeElement===items.at(-1)){e.preventDefault();items[0]?.focus();}
});

// Preserve the same data and controls while presenting each row as a mobile card.
document.querySelectorAll('.table-wrap').forEach(wrapper=>{
 const table=wrapper.querySelector('table');if(!table)return;
 if(wrapper.hasAttribute('data-wide-table')){
  table.classList.add('wide-table');wrapper.tabIndex=0;wrapper.setAttribute('role','region');wrapper.setAttribute('aria-label','Rekapitulasi pelayanan, geser untuk melihat seluruh kolom');
  const hint=document.createElement('p');hint.className='table-scroll-hint';hint.textContent='Geser tabel ke samping untuk melihat seluruh kolom.';wrapper.before(hint);return;
 }
 const headers=[...table.querySelectorAll('thead th')].map(th=>th.textContent.trim());
 if(!headers.length)return;table.classList.add('mobile-cards');table.setAttribute('role','table');
 table.querySelectorAll('thead,tbody').forEach(group=>group.setAttribute('role','rowgroup'));
 table.querySelectorAll('tr').forEach(row=>row.setAttribute('role','row'));
 table.querySelectorAll('th').forEach(cell=>cell.setAttribute('role','columnheader'));
 table.querySelectorAll('tbody tr').forEach(row=>[...row.children].forEach((cell,index)=>{
  cell.setAttribute('role','cell');cell.dataset.label=headers[index]??'';if(cell.querySelector('form,.table-actions'))cell.classList.add('mobile-action-cell');
  const value=document.createElement('div');value.className='mobile-cell-value';while(cell.firstChild)value.append(cell.firstChild);cell.append(value);
 }));
});
document.querySelector('#toggle-password')?.addEventListener('click',e=>{const input=document.querySelector('#password'); input.type=input.type==='password'?'text':'password';e.currentTarget.setAttribute('aria-label',input.type==='password'?'Tampilkan kata sandi':'Sembunyikan kata sandi');});
document.querySelectorAll('form[data-confirm]').forEach(form=>form.addEventListener('submit',e=>{if(!confirm(form.dataset.confirm))e.preventDefault();}));
document.querySelectorAll('form').forEach(form=>form.addEventListener('submit',e=>{if(e.defaultPrevented || form.method.toLowerCase()==='get')return; const button=e.submitter; if(button){setTimeout(()=>{button.disabled=true;},0);setTimeout(()=>{button.disabled=false;},10000);}}));

const lookup=document.querySelector('#participant-search');
if(lookup){let timer,controller;const results=document.querySelector('#participant-results');const hidden=document.querySelector('#participant_id');const selected=document.querySelector('#selected-participant');let version=0;
lookup.addEventListener('input',()=>{clearTimeout(timer);controller?.abort();hidden.value='';selected.hidden=true;results.replaceChildren();const q=lookup.value.trim();const current=++version;if(q.length<2){results.hidden=true;return;}
timer=setTimeout(async()=>{controller=new AbortController();results.hidden=false;results.textContent='Mencari peserta…';try{const response=await fetch(`${lookup.dataset.url}?q=${encodeURIComponent(q)}`,{headers:{Accept:'application/json'},signal:controller.signal});if(!response.ok)throw new Error();const items=await response.json();if(current!==version)return;results.replaceChildren();if(!items.length){results.textContent='Peserta belum ditemukan. Daftarkan melalui Data peserta.';return;}
for(const p of items){const button=document.createElement('button');button.type='button';button.setAttribute('role','option');const name=document.createElement('strong');name.textContent=p.name;const meta=document.createElement('small');meta.textContent=`${p.nik} · ${p.region}`;button.append(name,meta);button.addEventListener('click',()=>{hidden.value=p.id;lookup.value=p.name;selected.textContent=`${p.name} · ${p.nik} · ${p.region}`;selected.hidden=false;results.hidden=true;});results.append(button);}}catch(error){if(error.name!=='AbortError')results.textContent='Pencarian gagal. Muat ulang halaman atau coba kembali.';}},250);});
lookup.closest('form').addEventListener('submit',e=>{if(!hidden.value){e.preventDefault();lookup.setCustomValidity('Pilih peserta dari hasil pencarian terlebih dahulu.');lookup.reportValidity();}});lookup.addEventListener('input',()=>lookup.setCustomValidity(''));document.addEventListener('click',e=>{if(!e.target.closest('.lookup'))results.hidden=true;});}

const dashboard=document.querySelector('#dashboard-data');
if(dashboard){const {charts:c,summary:s}=JSON.parse(dashboard.textContent);const theme=getComputedStyle(document.documentElement);const colors=Array.from({length:7},(_,i)=>theme.getPropertyValue(`--method-${i}`).trim());const green=colors[0],blue=colors[2];
Chart.defaults.font.family="'Segoe UI', sans-serif";Chart.defaults.font.size=11;Chart.defaults.color='#87968b';Chart.defaults.plugins.legend.display=false;Chart.defaults.plugins.tooltip.backgroundColor='#274d5b';Chart.defaults.plugins.tooltip.padding=11;Chart.defaults.plugins.tooltip.cornerRadius=7;
const scales={x:{grid:{display:false},border:{display:false},ticks:{maxRotation:0}},y:{beginAtZero:true,border:{display:false,dash:[4,4]},grid:{color:'#edf1f2',drawTicks:false},ticks:{padding:10,precision:0,maxTicksLimit:6}}};
const options={responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},scales};
new Chart(document.querySelector('#monthly-chart'),{type:'bar',data:{labels:c.labels,datasets:[{label:'Realisasi peserta unik',data:c.monthly,backgroundColor:green,borderRadius:4,maxBarThickness:22},{label:'Target peserta unik',data:c.targets,backgroundColor:'#e1e9e5',borderRadius:4,maxBarThickness:22}]},options});
new Chart(document.querySelector('#trend-chart'),{type:'line',data:{labels:c.labels,datasets:[{label:'Jumlah pelayanan',data:c.visits,borderColor:blue,borderWidth:2,backgroundColor:ctx=>{const gradient=ctx.chart.ctx.createLinearGradient(0,0,0,250);gradient.addColorStop(0,`${blue}25`);gradient.addColorStop(1,`${blue}00`);return gradient;},fill:true,tension:.38,pointRadius:3,pointBackgroundColor:'white',pointBorderWidth:2,pointHoverRadius:5}]},options});
const center={id:'centerText',afterDraw(chart){const {ctx,chartArea:{top,bottom,left,right}}=chart;ctx.save();ctx.textAlign='center';ctx.textBaseline='middle';ctx.font="700 29px 'Segoe UI', sans-serif";ctx.fillStyle='#243f48';ctx.fillText(s.visits.toLocaleString('id-ID'),(left+right)/2,(top+bottom)/2-8);ctx.font="11px 'Segoe UI', sans-serif";ctx.fillStyle='#94a0a7';ctx.fillText('Total pelayanan',(left+right)/2,(top+bottom)/2+19);ctx.restore();}};
new Chart(document.querySelector('#method-chart'),{type:'doughnut',plugins:[center],data:{labels:s.visits?c.methodLabels:['Belum ada pelayanan'],datasets:[{data:s.visits?c.methods:[1],backgroundColor:s.visits?colors:['#edf2ef'],borderWidth:4,borderColor:'white',hoverOffset:3,borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,cutout:'76%',plugins:{tooltip:{enabled:!!s.visits,callbacks:{label:ctx=>` ${ctx.label}: ${ctx.raw} pelayanan (${(ctx.raw/s.visits*100).toFixed(1)}%)`}}}}});
new Chart(document.querySelector('#target-chart'),{type:'bar',data:{labels:['Target','Realisasi'],datasets:[{data:[s.target,s.unique],backgroundColor:['#e1e9e5',green],borderRadius:4,maxBarThickness:13}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',scales:{x:{beginAtZero:true,display:false},y:{grid:{display:false},border:{display:false},ticks:{font:{size:11}}}},plugins:{tooltip:{callbacks:{label:ctx=>`${ctx.raw??'Belum diatur'} peserta unik`}}}}});
const regional={};for(const key of ['region','facility']){const data=key==='region'?c.regions:c.facilities;const canvas=document.querySelector(`#${key}-chart`);canvas.parentElement.style.height=`${Math.max(230,data.length*35)}px`;regional[key]=new Chart(canvas,{type:'bar',data:{labels:data.map(x=>x.name),datasets:[{label:'Peserta unik',data:data.map(x=>x.total),backgroundColor:key==='region'?green:blue,borderRadius:4,maxBarThickness:15}]},options:{...options,indexAxis:'y',scales:{x:{...scales.y,ticks:{precision:0,maxTicksLimit:8}},y:{grid:{display:false},border:{display:false},ticks:{font:{size:11}}}}}});}
document.querySelectorAll('[data-chart-tab]').forEach(button=>button.addEventListener('click',()=>{const key=button.dataset.chartTab;document.querySelectorAll('[data-chart-tab]').forEach(b=>{b.classList.toggle('active',b===button);b.setAttribute('aria-pressed',String(b===button));});for(const item of ['region','facility'])document.querySelector(`#${item}-panel`).hidden=item!==key;regional[key].resize();}));
}
