// composables/statusMeta.js
// Central place for status -> color/label mapping so every badge and
// chart in the dashboard agrees on what "paid" or "overdue" looks like.

const PALETTE = {
  slate:   { bg: '#EEF1F6', fg: '#4A5568', dot: '#94A3B8' },
  amber:   { bg: '#FCF1DD', fg: '#92600D', dot: '#E8A33D' },
  green:   { bg: '#E6F4EC', fg: '#1E6B44', dot: '#2F855A' },
  red:     { bg: '#FBE9E9', fg: '#9B2C2C', dot: '#C53030' },
  blue:    { bg: '#E8EEFB', fg: '#2A4374', dot: '#3B5CA8' },
  navy:    { bg: '#E7EAF1', fg: '#1B2A4A', dot: '#1B2A4A' },
}

const STATUS_MAP = {
  // invoices
  draft:     { label: 'Draft',     ...PALETTE.slate },
  sent:      { label: 'Sent',      ...PALETTE.blue },
  paid:      { label: 'Paid',      ...PALETTE.green },
  overdue:   { label: 'Overdue',   ...PALETTE.red },
  // delivery notes
  pending:   { label: 'Pending',   ...PALETTE.amber },
  delivered: { label: 'Delivered', ...PALETTE.green },
  cancelled: { label: 'Cancelled', ...PALETTE.red },
  // estimates
  emailed:      { label: 'Emailed',      ...PALETTE.blue },
  email_failed: { label: 'Email failed', ...PALETTE.red },
  completed:    { label: 'Completed',    ...PALETTE.green },
  converted:    { label: 'Converted',    ...PALETTE.navy },
}

export function statusMeta(status) {
  return STATUS_MAP[status] || { label: status ?? '—', ...PALETTE.slate }
}

export function statusColor(status) {
  return statusMeta(status).dot
}

export function formatCurrency(value, currency = 'ZAR') {
  const n = Number(value ?? 0)
  return new Intl.NumberFormat('en-ZA', {
    style: 'currency',
    currency,
    maximumFractionDigits: 2,
  }).format(n)
}

export function formatDate(value) {
  if (!value) return '—'
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return value
  return d.toLocaleDateString('en-ZA', { day: '2-digit', month: 'short', year: 'numeric' })
}