const base = window.APP_BASE_PATH || '';
const token = localStorage.getItem('token');
if (!token) location.href = `${base}/admin-login`;

const $ = (id) => document.getElementById(id);
const bool = (v) => String(v) === '1' || v === true;
const notify = (message, ok = true) => {
  let el = document.querySelector('.integration-toast');
  if (!el) { el = document.createElement('div'); el.className = 'integration-toast'; document.body.appendChild(el); }
  el.textContent = message; el.style.cssText = `position:fixed;right:20px;bottom:20px;z-index:2000;padding:14px 18px;background:${ok?'#1c1a17':'#9a342d'};color:#fff;font:12px Jost;max-width:380px`; setTimeout(()=>el.remove(),4200);
};
async function request(path, options={}) {
  const res = await fetch(`${base}/api${path}`, { ...options, headers: { 'Content-Type':'application/json', Authorization:`Bearer ${token}`, ...(options.headers||{}) } });
  const text = await res.text(); let data; try { data = JSON.parse(text); } catch { data = { message:text }; }
  if (!res.ok || data.success === false) throw new Error(data.message || data.error || `Erro HTTP ${res.status}`);
  return data;
}

const fields = [
  'smtp_host','smtp_port','smtp_encryption','smtp_username','smtp_password','smtp_from_email','smtp_from_name','smtp_admin_recipients',
  'evolution_base_url','evolution_api_key','evolution_instance_name','evolution_webhook_url','evolution_admin_numbers'
];
const checkFields = ['smtp_enabled','evolution_enabled','evolution_notify_new_order','evolution_notify_payment_paid','evolution_notify_payment_review','evolution_notify_stock_low'];
let availableEvents = [];

function fill(data = {}) {
  // Atualiza apenas os campos que pertencem ao grupo recebido.
  // Antes, ao carregar o grupo Evolution depois do SMTP, os checkboxes
  // ausentes eram interpretados como false e o smtp_enabled era desligado
  // apenas na interface, embora permanecesse salvo no banco.
  fields.forEach((id) => {
    if ($(id) && Object.prototype.hasOwnProperty.call(data, id) && data[id] != null) {
      $(id).value = data[id];
    }
  });

  checkFields.forEach((id) => {
    if ($(id) && Object.prototype.hasOwnProperty.call(data, id)) {
      $(id).checked = bool(data[id]);
    }
  });
}
function renderEvents(selected=[]) {
  $('eventsGrid').innerHTML = availableEvents.map(event => `<label><input type="checkbox" name="evo_event" value="${event}" ${selected.includes(event)?'checked':''}> ${event}</label>`).join('');
}
async function load() {
  const data = await request('/integrations/settings');
  availableEvents = data.available_events || [];
  fill(data.smtp || {}); fill(data.evolution || {});
  renderEvents(data.evolution?.evolution_events || []);
}

function serialize(ids, checks=[]) { const out={}; ids.forEach(id=>out[id]=$(id)?.value?.trim()??''); checks.forEach(id=>out[id]=$(id)?.checked?'1':'0'); return out; }

async function saveSmtpSettings() {
  const payload = serialize(
    fields.filter((field) => field.startsWith('smtp_')),
    ['smtp_enabled']
  );
  payload.smtp_password = String(payload.smtp_password || '').replace(/\s+/g, '');
  const result = await request('/integrations/smtp', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
  if (result.settings) fill(result.settings);
  return result;
}

$('smtpForm').addEventListener('submit', async (event) => {
  event.preventDefault();
  try {
    await saveSmtpSettings();
    notify('Configuração SMTP salva.');
  } catch (error) {
    notify(error.message || 'Não foi possível salvar o SMTP.', false);
  }
});

$('smtp_enabled')?.addEventListener('change', async () => {
  try {
    await saveSmtpSettings();
    notify($('smtp_enabled').checked ? 'Notificações por e-mail ativadas.' : 'Notificações por e-mail desativadas.');
  } catch (error) {
    $('smtp_enabled').checked = !$('smtp_enabled').checked;
    notify(error.message || 'Não foi possível alterar a ativação.', false);
  }
});
$('evolutionForm').addEventListener('submit', async e => { e.preventDefault(); try { const payload=serialize(fields.filter(x=>x.startsWith('evolution_')),checkFields.filter(x=>x.startsWith('evolution_'))); payload.evolution_events=[...document.querySelectorAll('[name="evo_event"]:checked')].map(x=>x.value); await request('/integrations/evolution',{method:'POST',body:JSON.stringify(payload)}); notify('Configuração Evolution salva.'); } catch(e){notify(e.message,false);} });

document.querySelectorAll('.integration-tab').forEach(btn=>btn.addEventListener('click',()=>{document.querySelectorAll('.integration-tab,.integration-panel').forEach(x=>x.classList.remove('active'));btn.classList.add('active');$(`panel-${btn.dataset.tab}`).classList.add('active');}));
$('selectRecommended').addEventListener('click',()=>{const recommended=['QRCODE_UPDATED','CONNECTION_UPDATE','MESSAGES_UPSERT','MESSAGES_UPDATE','SEND_MESSAGE','ERRORS'];document.querySelectorAll('[name="evo_event"]').forEach(x=>x.checked=recommended.includes(x.value));});

const instancePayload=()=>JSON.stringify({instance_name:$('evolution_instance_name').value.trim()});
async function evolutionAction(path, method='POST', body=instancePayload()) { try { const r=await request(path,{method,body:method==='GET'?undefined:body}); return r; } catch(e){notify(e.message,false); throw e;} }
function showQr(data) { const qr=data?.base64 || data?.qrcode?.base64 || data?.qrcode || data?.instance?.qrcode?.base64; const code=data?.pairingCode || data?.code; $('qrArea').innerHTML=qr?`<img src="${qr.startsWith('data:')?qr:`data:image/png;base64,${qr}`}" alt="QR Code WhatsApp">`:'<div class="qr-placeholder">A API não retornou um QR Code. Atualize o estado ou tente novamente.</div>'; if(code){$('pairingCode').hidden=false;$('pairingCode').textContent=`Código de pareamento: ${code}`;} }
function updateStatus(data){const state=data?.instance?.state || data?.state || data?.instance?.connectionStatus || data?.connectionStatus || 'desconhecido';$('instanceStatus').textContent=state;$('instanceStatus').className=`status-pill ${String(state).toLowerCase().includes('open')?'open':'close'}`;}

$('createInstance').addEventListener('click',async()=>{try{const r=await evolutionAction('/integrations/evolution/create');notify('Instância criada.');showQr(r.data||{});}catch{}});
$('generateQr').addEventListener('click',async()=>{try{const r=await evolutionAction('/integrations/evolution/qr');showQr(r.data||{});}catch{}});
$('checkState').addEventListener('click',async()=>{try{const r=await evolutionAction('/integrations/evolution/state');updateStatus(r.data||{});notify('Estado atualizado.');}catch{}});
$('setWebhook').addEventListener('click',async()=>{try{const payload={instance_name:$('evolution_instance_name').value.trim(),webhook_url:$('evolution_webhook_url').value.trim(),events:[...document.querySelectorAll('[name="evo_event"]:checked')].map(x=>x.value)};await evolutionAction('/integrations/evolution/webhook','POST',JSON.stringify(payload));notify('Webhook e eventos configurados.');}catch{}});
$('testWhatsapp').addEventListener('click',async()=>{try{await evolutionAction('/integrations/evolution/test','POST',JSON.stringify({instance_name:$('evolution_instance_name').value.trim(),number:$('evolution_test_number').value.trim()}));notify('Mensagem enviada.');}catch{}});
$('logoutInstance').addEventListener('click',async()=>{if(!confirm('Desconectar esta instância do WhatsApp?'))return;try{await evolutionAction('/integrations/evolution/logout');notify('Instância desconectada.');updateStatus({state:'close'});}catch{}});
$('deleteInstance').addEventListener('click',async()=>{if(!confirm('Excluir definitivamente a instância? Esta ação exige novo QR Code depois.'))return;try{await evolutionAction('/integrations/evolution/instance','DELETE');notify('Instância excluída.');$('qrArea').innerHTML='<div class="qr-placeholder">Instância excluída.</div>';updateStatus({state:'deleted'});}catch{}});

$('smtpTestOpen').addEventListener('click',()=>{$('smtpTestModal').hidden=false;});document.querySelectorAll('[data-close-modal]').forEach(x=>x.addEventListener('click',()=>{$('smtpTestModal').hidden=true;}));
$('sendSmtpTest').addEventListener('click', async () => {
  const email = $('smtp_test_email').value.trim();

  if (!email) {
    notify('Informe o destinatário do teste.', false);
    return;
  }

  try {
    if (!$('smtp_enabled').checked) {
      $('smtp_enabled').checked = true;
    }
    await saveSmtpSettings();
    await request('/integrations/smtp/test', {
      method: 'POST',
      body: JSON.stringify({ email }),
    });

    notify('E-mail de teste enviado.');
    $('smtpTestModal').hidden = true;
  } catch (error) {
    notify(
      error.message || 'Falha ao enviar o teste.',
      false
    );
  }
});

load().catch(e=>notify(e.message,false));
