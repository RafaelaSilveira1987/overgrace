import { clientService } from "../../services/clientService.js";
import { debounce } from "../../utils/debounce.js";
import { dataUtil, valorUtil } from "../../utils/normalize.js";
import { getInitials } from "../../utils/inicials.js";

let currentPage = 1;
const limit = 10;
let lastFilters = {};
const esc = (v='') => String(v ?? '').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
function tagClass(perfil='') { return perfil === 'VIP' ? 'tag-vip' : perfil === 'Novo' ? 'tag-new' : 'tag-regular'; }
function getFilters(){
  const [order_by,order_dir]=(document.getElementById('filter-order')?.value || 'created_at:DESC').split(':');
  return {descricao:document.getElementById('filter-descricao')?.value.trim()||'', perfil:document.getElementById('filter-perfil')?.value||'', order_by, order_dir, page:currentPage, limit};
}
export async function carregarClientes(){
  const container=document.getElementById('lista-clientes');
  try{
    lastFilters=getFilters(); const res=await clientService.listar(lastFilters); const rows=Array.isArray(res.data)?res.data:[];
    container.innerHTML=rows.length?rows.map(client=>{
      const nome=[client.nome,client.sobrenome].filter(Boolean).join(' ');
      return `<tr>
        <td><div class="customer-cell"><div class="customer-avatar">${esc(getInitials(nome))}</div><div><div class="customer-name">${esc(nome)}</div><div class="customer-email">${esc(client.email)}</div></div></div></td>
        <td style="font-size:12px;color:var(--ink-3)">${dataUtil(client.created_at,'format','d/m/Y')||'—'}</td>
        <td>${Number(client.pedidos||0)}</td>
        <td style="font-weight:500">R$ ${valorUtil(client.valor_gasto)}</td>
        <td style="font-size:12px;color:var(--ink-3)">${dataUtil(client.ultimo_pedido,'format','d/m/Y')||'—'}</td>
        <td><span class="customer-tag ${tagClass(client.perfil)}">${esc(client.perfil||'Regular')}</span></td>
        <td><button class="detail-btn js-client-detail" data-id="${client.id}">Ver perfil</button></td>
      </tr>`;
    }).join(''):`<tr><td colspan="7" style="text-align:center;padding:30px;color:#999">Nenhum cliente encontrado</td></tr>`;
    document.getElementById('qt_clients').textContent=res.totals?.qt_clients??0;
    document.querySelector('.table-footer span').textContent=`Mostrando ${rows.length} de ${res.pagination?.total??0} clientes`;
    renderPagination(res.pagination||{page:1,pages:1});
  }catch(e){console.error(e);container.innerHTML=`<tr><td colspan="7" style="text-align:center;padding:30px;color:#999">Erro ao carregar clientes</td></tr>`;}
}
function renderPagination({page=1,pages=1}){const w=document.querySelector('.pagination');if(!w)return;let h=page>1?`<button class="page-btn" data-page="${page-1}">←</button>`:'';for(let i=1;i<=pages;i++)h+=`<button class="page-btn ${i===page?'active':''}" data-page="${i}">${i}</button>`;if(page<pages)h+=`<button class="page-btn" data-page="${page+1}">→</button>`;w.innerHTML=h;}
async function exportCsv(){const r=await clientService.listar({...lastFilters,page:1,limit:1000});const rows=[['Nome','E-mail','Telefone','CPF','Cadastro','Pedidos','Total gasto','Perfil']];(r.data||[]).forEach(c=>rows.push([[c.nome,c.sobrenome].filter(Boolean).join(' '),c.email||'',c.telefone||'',c.cpf||'',dataUtil(c.created_at,'format','d/m/Y'),c.pedidos||0,Number(c.valor_gasto||0).toFixed(2),c.perfil||'']));const csv=rows.map(r=>r.map(v=>`"${String(v??'').replace(/"/g,'""')}"`).join(';')).join('\n');const b=new Blob(['\ufeff'+csv],{type:'text/csv;charset=utf-8'});const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='clientes.csv';a.click();URL.revokeObjectURL(a.href);}
const reload=debounce(()=>{currentPage=1;carregarClientes();},350);
document.getElementById('filter-descricao')?.addEventListener('input',reload);document.getElementById('filter-perfil')?.addEventListener('change',reload);document.getElementById('filter-order')?.addEventListener('change',reload);document.getElementById('btnExportClients')?.addEventListener('click',exportCsv);
document.addEventListener('click',e=>{const p=e.target.closest('.page-btn')?.dataset.page;if(p){currentPage=Number(p);carregarClientes();}});
window.addEventListener('client-updated',carregarClientes);carregarClientes();
