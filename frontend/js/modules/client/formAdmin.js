import { clientService } from '../../services/clientService.js';
import { notify } from '../../utils/notify.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';
import { getInitials } from '../../utils/inicials.js';

let currentClientId=null; let editing=false; const modal=document.getElementById('clientModal');
const esc=(v='')=>String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
const set=(id,v='—')=>{const e=document.getElementById(id);if(e)e.textContent=v||'—';};
function setEditing(on){editing=on;document.querySelectorAll('#formClient [data-editable]').forEach(e=>e.disabled=!on);const b=document.getElementById('btnEditClient');if(b)b.textContent=on?'Salvar alterações':'Editar cliente';}
function close(){modal?.classList.remove('open');currentClientId=null;setEditing(false);} window.closeClient=close;window.closeClientOutside=e=>{if(e.target===modal)close();};
function render(c){currentClientId=Number(c.id);const nome=[c.nome,c.sobrenome].filter(Boolean).join(' ');set('clientAvatarLg',getInitials(nome));set('clientNameLg',nome);set('clientEmailLg',c.email);set('clientTagLg',c.perfil||'Regular');set('clientCadastro',dataUtil(c.created_at,'format','d/m/Y')||'—');set('clientPedidos',String(c.pedidos||0));set('clientTotal',`R$ ${valorUtil(c.valor_gasto)}`);const ticket=Number(c.pedidos||0)>0?Number(c.valor_gasto||0)/Number(c.pedidos):0;set('clientTicket',`R$ ${valorUtil(ticket)}`);
  for(const f of ['nome','sobrenome','email','telefone','cpf']){const e=document.getElementById(`client-${f}`);if(e)e.value=c[f]||'';}
  const a=c.address||{};set('clientAddress',[a.endereco,a.numero,a.complemento].filter(Boolean).join(', ')||'—');set('clientCity',[a.cidade,a.estado].filter(Boolean).join(' / ')||'—');set('clientCep',a.cep||'—');
  const box=document.getElementById('clientOrdersList');box.innerHTML=(c.orders||[]).length?(c.orders||[]).map(o=>`<div class="order-mini-row"><span class="order-num">#${o.id}</span><span style="flex:1;font-size:12px;color:var(--ink-3)">${dataUtil(o.created_at,'format','d/m/Y')||'—'}</span><span class="status-pill">${esc(o.status||'—')}</span><span style="font-weight:500">R$ ${valorUtil(o.total_amount)}</span></div>`).join(''):'<p style="font-size:12px;color:var(--ink-3)">Nenhum pedido registrado.</p>';
}
async function open(id){try{const r=await clientService.buscar(id);render(r.data||r);setEditing(false);modal?.classList.add('open');}catch(e){notify.error(e.message||'Erro ao carregar cliente');}}
document.addEventListener('click',e=>{const b=e.target.closest('.js-client-detail');if(b)open(b.dataset.id);});
document.getElementById('btnEditClient')?.addEventListener('click',async e=>{e.preventDefault();if(!currentClientId)return;if(!editing){setEditing(true);return;}const data={};for(const f of ['nome','sobrenome','email','telefone','cpf'])data[f]=document.getElementById(`client-${f}`)?.value.trim()||'';try{e.currentTarget.disabled=true;const r=await clientService.atualizar(currentClientId,data);render(r.data);setEditing(false);notify.success('Cliente atualizado com sucesso');window.dispatchEvent(new Event('client-updated'));}catch(err){notify.error(err.message||'Erro ao atualizar cliente');}finally{e.currentTarget.disabled=false;}});
