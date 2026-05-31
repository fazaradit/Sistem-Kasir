<?php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS — Kasir</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap');

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --ink:    #0a0a0a;
    --paper:  #f5f4f0;
    --rule:   #d8d6d0;
    --muted:  #8a887f;
    --accent: #0a0a0a;
    --mono:   'DM Mono', monospace;
    --sans:   'DM Sans', sans-serif;
  }

  body {
    background: var(--paper);
    color: var(--ink);
    font-family: var(--sans);
    font-size: 14px;
    min-height: 100vh;
    display: grid;
    grid-template-rows: auto 1fr;
  }

  /* ── Header ── */
  header {
    border-bottom: 1px solid var(--rule);
    padding: 16px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .logo {
    font-family: var(--mono);
    font-size: 13px;
    letter-spacing: .12em;
    text-transform: uppercase;
  }
  .logo span { opacity: .35; }
  .timestamp {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--muted);
  }

  /* ── Layout ── */
  .layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    height: calc(100vh - 49px);
  }

  /* ── Produk Panel ── */
  .panel-produk {
    border-right: 1px solid var(--rule);
    overflow-y: auto;
    padding: 24px 32px;
  }
  .panel-label {
    font-family: var(--mono);
    font-size: 10px;
    letter-spacing: .15em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 16px;
  }
  .produk-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1px;
    background: var(--rule);
    border: 1px solid var(--rule);
  }
  .produk-card {
    background: var(--paper);
    padding: 20px 16px;
    cursor: pointer;
    transition: background .15s;
    user-select: none;
  }
  .produk-card:hover { background: var(--ink); color: var(--paper); }
  .produk-card:hover .produk-harga,
  .produk-card:hover .produk-stok { color: rgba(245,244,240,.5); }
  .produk-nama {
    font-size: 13px;
    font-weight: 500;
    line-height: 1.35;
    margin-bottom: 12px;
  }
  .produk-harga {
    font-family: var(--mono);
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 4px;
  }
  .produk-stok {
    font-family: var(--mono);
    font-size: 10px;
    color: var(--rule);
  }

  /* ── Kasir Panel ── */
  .panel-kasir {
    display: flex;
    flex-direction: column;
    padding: 24px;
  }

  /* Error */
  .alert {
    background: var(--ink);
    color: var(--paper);
    padding: 10px 14px;
    font-size: 12px;
    font-family: var(--mono);
    margin-bottom: 16px;
  }

  /* Keranjang */
  .keranjang { flex: 1; overflow-y: auto; }
  .keranjang-kosong {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--rule);
    font-family: var(--mono);
    font-size: 11px;
    letter-spacing: .1em;
    text-transform: uppercase;
    gap: 8px;
  }
  .keranjang-kosong svg { opacity: .2; }

  .cart-item {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    align-items: start;
    padding: 12px 0;
    border-bottom: 1px solid var(--rule);
  }
  .cart-item-nama { font-size: 13px; font-weight: 500; }
  .cart-item-sub  { font-family: var(--mono); font-size: 11px; color: var(--muted); margin-top: 3px; }
  .cart-item-ctrl {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .qty-btn {
    width: 24px; height: 24px;
    border: 1px solid var(--rule);
    background: none;
    cursor: pointer;
    font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: all .1s;
  }
  .qty-btn:hover { background: var(--ink); color: var(--paper); border-color: var(--ink); }
  .qty-val { font-family: var(--mono); font-size: 12px; min-width: 20px; text-align: center; }

  /* Total & Bayar */
  .divider { border: none; border-top: 1px solid var(--ink); margin: 16px 0; }
  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 6px;
  }
  .total-label { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: .12em; color: var(--muted); }
  .total-nilai  { font-family: var(--mono); font-size: 18px; font-weight: 500; }

  .input-wrap { margin-top: 12px; }
  .input-label { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: .12em; color: var(--muted); display: block; margin-bottom: 6px; }
  .input-bayar {
    width: 100%;
    border: 1px solid var(--ink);
    background: transparent;
    padding: 10px 12px;
    font-family: var(--mono);
    font-size: 16px;
    outline: none;
    color: var(--ink);
  }
  .input-bayar:focus { outline: 2px solid var(--ink); outline-offset: 2px; }

  .kembalian-row {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-family: var(--mono);
    font-size: 12px;
    color: var(--muted);
    min-height: 18px;
  }

  .btn-bayar {
    margin-top: 16px;
    width: 100%;
    padding: 14px;
    background: var(--ink);
    color: var(--paper);
    border: none;
    font-family: var(--mono);
    font-size: 12px;
    letter-spacing: .15em;
    text-transform: uppercase;
    cursor: pointer;
    transition: opacity .15s;
  }
  .btn-bayar:hover { opacity: .75; }
  .btn-bayar:disabled { opacity: .25; cursor: not-allowed; }
  .btn-reset {
    margin-top: 8px;
    width: 100%;
    padding: 10px;
    background: transparent;
    color: var(--muted);
    border: 1px solid var(--rule);
    font-family: var(--mono);
    font-size: 10px;
    letter-spacing: .12em;
    text-transform: uppercase;
    cursor: pointer;
  }
  .btn-reset:hover { border-color: var(--ink); color: var(--ink); }
</style>
</head>
<body>

<header>
  <div class="logo">POS <span>/</span> Kasir</div>
  <div class="timestamp" id="clock"></div>
</header>

<div class="layout">

  <!-- Panel Kiri: Produk -->
  <div class="panel-produk">
    <div class="panel-label">Pilih Produk</div>
    <div class="produk-grid">
      <?php foreach ($produkList as $p): ?>
      <div class="produk-card"
           onclick="tambahItem(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nama_produk'], ENT_QUOTES) ?>', <?= $p['harga'] ?>, <?= $p['stok'] ?>)">
        <div class="produk-nama"><?= htmlspecialchars($p['nama_produk']) ?></div>
        <div class="produk-harga"><?= 'Rp ' . number_format($p['harga'], 0, ',', '.') ?></div>
        <div class="produk-stok">Stok: <?= $p['stok'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Panel Kanan: Kasir -->
  <div class="panel-kasir">
    <?php if ($error): ?>
    <div class="alert">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="panel-label">Keranjang</div>

    <div class="keranjang" id="keranjang">
      <div class="keranjang-kosong" id="empty-state">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/>
        </svg>
        <span>Keranjang kosong</span>
      </div>
    </div>

    <hr class="divider">

    <div class="total-row">
      <span class="total-label">Total</span>
      <span class="total-nilai" id="total-display">Rp 0</span>
    </div>

    <div class="input-wrap">
      <label class="input-label" for="uang_bayar">Uang Bayar</label>
      <input type="number" class="input-bayar" id="uang_bayar"
             placeholder="0" min="0" oninput="hitungKembalian()">
    </div>

    <div class="kembalian-row">
      <span>Kembalian</span>
      <span id="kembalian-display">—</span>
    </div>

    <!-- Form tersembunyi untuk submit ke server -->
    <form id="form-transaksi" method="POST" action="?page=kasir&action=proses">
      <div id="hidden-inputs"></div>
      <input type="hidden" name="uang_bayar" id="hidden-bayar">
      <button type="button" class="btn-bayar" id="btn-bayar" disabled onclick="submitTransaksi()">
        Proses Pembayaran
      </button>
    </form>

    <button class="btn-reset" onclick="resetKeranjang()">Batal / Reset</button>
  </div>
</div>

<script>
  // ── State keranjang
  let cart = []; // [{id, nama, harga, jumlah, stok}]

  // ── Jam realtime
  const clock = document.getElementById('clock');
  const tick = () => {
    clock.textContent = new Date().toLocaleTimeString('id-ID', {
      hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
  };
  tick(); setInterval(tick, 1000);

  // ── Tambah item ke keranjang 
  function tambahItem(id, nama, harga, stok) {
    const existing = cart.find(i => i.id === id);
    if (existing) {
      if (existing.jumlah >= stok) {
        alert('Stok tidak mencukupi!');
        return;
      }
      existing.jumlah++;
    } else {
      cart.push({ id, nama, harga, jumlah: 1, stok });
    }
    renderKeranjang();
  }

  // ── Update jumlah
  function ubahJumlah(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.jumlah += delta;
    if (item.jumlah <= 0) cart = cart.filter(i => i.id !== id);
    renderKeranjang();
  }

  // ── Render keranjang ke DOM 
  function renderKeranjang() {
    const container = document.getElementById('keranjang');
    const emptyState = document.getElementById('empty-state');

    // Hapus item lama
    container.querySelectorAll('.cart-item').forEach(el => el.remove());

    if (cart.length === 0) {
      emptyState.style.display = '';
      updateTotal();
      return;
    }
    emptyState.style.display = 'none';

    cart.forEach(item => {
      const div = document.createElement('div');
      div.className = 'cart-item';
      div.innerHTML = `
        <div>
          <div class="cart-item-nama">${item.nama}</div>
          <div class="cart-item-sub">Rp ${fmt(item.harga)} × ${item.jumlah} = Rp ${fmt(item.harga * item.jumlah)}</div>
        </div>
        <div class="cart-item-ctrl">
          <button class="qty-btn" onclick="ubahJumlah(${item.id}, -1)">−</button>
          <span class="qty-val">${item.jumlah}</span>
          <button class="qty-btn" onclick="ubahJumlah(${item.id}, 1)">+</button>
        </div>
      `;
      container.appendChild(div);
    });

    updateTotal();
  }

  // ── Hitung & tampilkan total 
  function updateTotal() {
    const total = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    document.getElementById('total-display').textContent = 'Rp ' + fmt(total);
    hitungKembalian();

    const btnBayar = document.getElementById('btn-bayar');
    btnBayar.disabled = cart.length === 0;
  }

  // ── Hitung kembalian
  function hitungKembalian() {
    const total  = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const bayar  = parseFloat(document.getElementById('uang_bayar').value) || 0;
    const el     = document.getElementById('kembalian-display');

    if (bayar <= 0) { el.textContent = '—'; return; }
    const kembalian = bayar - total;
    el.textContent = (kembalian >= 0 ? 'Rp ' + fmt(kembalian) : '— Kurang Rp ' + fmt(Math.abs(kembalian)));
    el.style.color = kembalian < 0 ? '#c0392b' : '';
  }

  // ── Submit form ke server 
  function submitTransaksi() {
    const total = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const bayar = parseFloat(document.getElementById('uang_bayar').value) || 0;

    if (bayar < total) { alert('Uang bayar kurang!'); return; }
    if (cart.length === 0) { alert('Keranjang kosong!'); return; }

    // Bangun hidden inputs untuk dikirim ke server
    const container = document.getElementById('hidden-inputs');
    container.innerHTML = '';
    cart.forEach((item, idx) => {
      container.innerHTML += `
        <input type="hidden" name="items[${idx}][produk_id]" value="${item.id}">
        <input type="hidden" name="items[${idx}][jumlah]"    value="${item.jumlah}">
        <input type="hidden" name="items[${idx}][subtotal]"  value="${item.harga * item.jumlah}">
      `;
    });
    document.getElementById('hidden-bayar').value = bayar;
    document.getElementById('form-transaksi').submit();
  }

  // ── Reset keranjang
  function resetKeranjang() {
    cart = [];
    document.getElementById('uang_bayar').value = '';
    renderKeranjang();
  }

  // ── Format angka ribuan 
  function fmt(n) {
    return Number(n).toLocaleString('id-ID');
  }
</script>
</body>
</html>