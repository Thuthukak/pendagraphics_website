<template>
  <div class="set-root">
    <div class="set-topbar">
      <div>
        <h1 class="set-title">Settings</h1>
        <p class="set-subtitle">Company details, invoice &amp; quotation defaults, bank accounts</p>
      </div>
      <button class="penda-btn penda-btn-primary" :disabled="saving" @click="saveAll">
        {{ saving ? 'Saving…' : 'Save Changes' }}
      </button>
    </div>

    <div v-if="loading" class="set-loading">Loading settings…</div>

    <div v-else class="set-body">
      <!-- ── Company Profile ── -->
      <section class="set-card">
        <div class="set-card-hd">
          <h2>Company Profile</h2>
          <p>Shown on invoice and quotation PDFs</p>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Company Name</label>
            <input v-model="company.name" type="text" class="form-input" />
          </div>
          <div class="form-group">
            <label>Tax Number</label>
            <input v-model="company.tax_number" type="text" class="form-input" />
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input v-model="company.phone" type="tel" class="form-input" />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="company.email" type="email" class="form-input" />
          </div>
          <div class="form-group">
            <label>Website</label>
            <input v-model="company.website" type="text" class="form-input" placeholder="https://…" />
          </div>
          <div class="form-group full">
            <label>Address Line 1</label>
            <input v-model="company.address_line1" type="text" class="form-input" />
          </div>
          <div class="form-group full">
            <label>Address Line 2</label>
            <input v-model="company.address_line2" type="text" class="form-input" />
          </div>
          <div class="form-group">
            <label>City</label>
            <input v-model="company.city" type="text" class="form-input" />
          </div>
          <div class="form-group">
            <label>Province</label>
            <input v-model="company.state" type="text" class="form-input" />
          </div>
          <div class="form-group">
            <label>Postal Code</label>
            <input v-model="company.postal_code" type="text" class="form-input" />
          </div>
          <div class="form-group">
            <label>Country</label>
            <input v-model="company.country" type="text" class="form-input" />
          </div>
        </div>
      </section>

      <!-- ── Invoice Preferences ── -->
      <section class="set-card">
        <div class="set-card-hd">
          <h2>Invoice Preferences</h2>
          <p>Defaults applied when creating a new invoice</p>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Prefix</label>
            <input v-model="invoice.prefix" type="text" class="form-input" placeholder="INV-" />
          </div>
          <div class="form-group">
            <label>Next Number</label>
            <input v-model.number="invoice.next_number" type="number" min="1" class="form-input" />
          </div>
          <div class="form-group">
            <label>Default Tax Rate (%)</label>
            <input v-model.number="invoice.default_tax_rate" type="number" min="0" max="100" step="0.01" class="form-input" />
          </div>
          <div class="form-group">
            <label>Default Discount (%)</label>
            <input v-model.number="invoice.default_discount_rate" type="number" min="0" max="100" step="0.01" class="form-input" />
          </div>
          <div class="form-group">
            <label>Default Net Terms (days)</label>
            <input v-model.number="invoice.default_net_terms_days" type="number" min="0" class="form-input" />
          </div>
        </div>
        <div class="form-row" style="margin-top:14px">
          <div class="form-group full">
            <label>Default Terms <span class="opt">(optional)</span></label>
            <textarea v-model="invoice.default_terms" class="form-input" rows="2" placeholder="Late payment penalties, warranty terms…" />
          </div>
        </div>
      </section>

      <!-- ── Quotation Preferences ── -->
      <section class="set-card">
        <div class="set-card-hd">
          <h2>Quotation Preferences</h2>
          <p>Defaults applied when creating a new quotation</p>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Prefix</label>
            <input v-model="quotation.prefix" type="text" class="form-input" placeholder="QUO-" />
          </div>
          <div class="form-group">
            <label>Next Number</label>
            <input v-model.number="quotation.next_number" type="number" min="1" class="form-input" />
          </div>
          <div class="form-group">
            <label>Default Tax Rate (%)</label>
            <input v-model.number="quotation.default_tax_rate" type="number" min="0" max="100" step="0.01" class="form-input" />
          </div>
          <div class="form-group">
            <label>Valid For (days)</label>
            <input v-model.number="quotation.valid_days" type="number" min="0" class="form-input" />
          </div>
        </div>
        <div class="form-row" style="margin-top:14px">
          <div class="form-group full">
            <label>Default Terms <span class="opt">(optional)</span></label>
            <textarea v-model="quotation.default_terms" class="form-input" rows="2" placeholder="Quotation validity, conditions…" />
          </div>
        </div>
      </section>

      <!-- ── Bank Accounts ── -->
      <section class="set-card">
        <div class="set-card-hd">
          <div>
            <h2>Bank Accounts</h2>
            <p>Insertable into invoice &amp; quotation notes at a click</p>
          </div>
          <button type="button" class="btn-text" @click="addAccount">+ Add account</button>
        </div>

        <div v-if="!banking.length" class="set-empty">No bank accounts yet. Add one to enable "Insert bank details" on invoices.</div>

        <div v-for="(acc, idx) in banking" :key="acc._key" class="bank-account-row">
          <div class="form-row">
            <div class="form-group">
              <label>Account Name *</label>
              <input v-model="acc.name" type="text" class="form-input" placeholder="e.g. Segale Trading" />
            </div>
            <div class="form-group">
              <label>Bank *</label>
              <input v-model="acc.bank" type="text" class="form-input" placeholder="e.g. Standard Bank" />
            </div>
            <div class="form-group">
              <label>Account Number *</label>
              <input v-model="acc.account_number" type="text" class="form-input" />
            </div>
            <div class="form-group">
              <label>Branch <span class="opt">(optional)</span></label>
              <input v-model="acc.branch" type="text" class="form-input" placeholder="Branch name / code" />
            </div>
            <div class="form-group">
              <label>Phone <span class="opt">(optional)</span></label>
              <input v-model="acc.phone" type="tel" class="form-input" />
            </div>
          </div>
          <button type="button" class="del-account-btn" @click="removeAccount(idx)" title="Remove account">×</button>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useNotify } from '@/composables/useToast.js'

const notify = useNotify()

const loading = ref(true)
const saving  = ref(false)

const company = reactive({
  name: '', tax_number: '', address_line1: '', address_line2: '',
  city: '', state: '', postal_code: '', country: '', phone: '', email: '', website: '',
})

const invoice = reactive({
  prefix: 'INV-', next_number: 1, default_tax_rate: 15,
  default_discount_rate: 0, default_net_terms_days: 30, default_terms: '',
})

const quotation = reactive({
  prefix: 'QUO-', next_number: 1, default_tax_rate: 15, valid_days: 30, default_terms: '',
})

const banking = ref([])

function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

async function api(method, path, data = null) {
  const opts = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': getCsrf(),
    },
  }
  if (data) opts.body = JSON.stringify(data)
  const res = await fetch('/api' + path, opts)
  if (!res.ok) throw await res.json()
  return res.json()
}

function addAccount() {
  banking.value.push({ _key: cryptoKey(), name: '', bank: '', account_number: '', branch: '', phone: '' })
}

function removeAccount(idx) {
  banking.value.splice(idx, 1)
}

function cryptoKey() {
  return (crypto?.randomUUID?.() ?? `key-${Date.now()}-${Math.random()}`)
}

async function loadSettings() {
  loading.value = true
  try {
    const res = await api('GET', '/settings')
    Object.assign(company, res.company)
    Object.assign(invoice, res.invoice)
    Object.assign(quotation, res.quotation)
    banking.value = (res.banking ?? []).map(a => ({ ...a, _key: a.id ?? cryptoKey() }))
  } catch (err) {
    notify.error(err?.message ?? 'Failed to load settings.')
  } finally {
    loading.value = false
  }
}

async function saveAll() {
  // Basic validation for bank accounts before hitting the API
  const incomplete = banking.value.some(a => !a.name || !a.bank || !a.account_number)
  if (incomplete) {
    notify.error('Each bank account needs a name, bank and account number.')
    return
  }

  saving.value = true
  try {
    await api('POST', '/settings', {
      company,
      invoice,
      quotation,
      banking: banking.value.map(({ _key, ...rest }) => rest),
    })
    notify.success('Settings saved successfully.')
    await loadSettings()
  } catch (err) {
    notify.error(err?.message ?? 'Failed to save settings.')
  } finally {
    saving.value = false
  }
}

onMounted(loadSettings)
</script>

<style scoped>
.set-root { min-height: 100vh; background: #f8f7f4; }

.set-topbar {
  display: flex; justify-content: space-between; align-items: flex-start;
  padding: 36px 40px 24px;
  max-width: 1000px; margin: 0 auto;
}
.set-title { font-size: 28px; font-weight: 700; color: #1a1a1a; letter-spacing: -0.5px; margin: 0 0 4px; }
.set-subtitle { font-size: 13px; color: #888; margin: 0; }

.penda-btn-primary {
  padding: 10px 22px; background: #1a1a1a; color: #f0efe7;
  border: none; border-radius: 8px; font-size: 14px; font-weight: 500;
  cursor: pointer; transition: background 0.15s;
}
.penda-btn-primary:hover:not(:disabled) { background: #333; }
.penda-btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.set-loading { text-align: center; padding: 80px; color: #aaa; font-style: italic; }

.set-body {
  max-width: 1000px; margin: 0 auto; padding: 0 40px 60px;
  display: flex; flex-direction: column; gap: 20px;
}

.set-card {
  background: white; border: 1px solid #e8e8e0; border-radius: 12px; padding: 24px 28px;
}
.set-card-hd {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 18px;
}
.set-card-hd h2 {
  font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0 0 2px;
  font-family: 'Playfair Display', Georgia, serif;
}
.set-card-hd p { font-size: 12px; color: #999; margin: 0; }

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px;
}
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 12px; font-weight: 500; color: #666; }
.opt { font-weight: 400; color: #bbb; }

.form-input {
  padding: 8px 11px;
  border: 1px solid #e0e0d8;
  border-radius: 8px;
  font-size: 14px; color: #1a1a1a;
  outline: none; background: white;
  width: 100%; box-sizing: border-box;
  transition: border-color 0.15s;
}
.form-input:focus { border-color: #d4a853; box-shadow: 0 0 0 3px rgba(212,168,83,0.1); }
textarea.form-input { resize: vertical; }

.btn-text { background: none; border: none; font-size: 13px; color: #d4a853; font-weight: 600; cursor: pointer; padding: 0; white-space: nowrap; }
.btn-text:hover { color: #b8902e; }

.set-empty {
  font-size: 13px; color: #aaa; font-style: italic;
  padding: 16px; background: #fafaf7; border-radius: 8px; margin-bottom: 4px;
}

.bank-account-row {
  position: relative;
  padding: 16px;
  background: #fafaf7;
  border: 1px solid #eeeee6;
  border-radius: 10px;
  margin-bottom: 12px;
}
.bank-account-row:last-child { margin-bottom: 0; }

.del-account-btn {
  position: absolute; top: 10px; right: 10px;
  width: 24px; height: 24px;
  background: none; border: none; border-radius: 5px;
  color: #ccc; font-size: 16px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.del-account-btn:hover { background: #fee8e8; color: #c0392b; }

@media (max-width: 640px) {
  .set-topbar { flex-direction: column; gap: 14px; padding: 24px 20px; }
  .set-body { padding: 0 20px 60px; }
  .form-row { grid-template-columns: 1fr; }
}
</style>