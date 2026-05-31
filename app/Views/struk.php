<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk #<?= $struk['id'] ?></title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: #f5f4f0;
    min-height: 100vh;
    display: grid;
    place-items: center;
    font-family: 'DM Mono', monospace;
  }
  .struk {
    background: white;
    width: 320px;
    padding: 32px 24px;
    border: 1px solid #d8d6d0;
  }
  .struk-header { text-align: center; margin-bottom: 24px; }
  .struk-header h1 { font-size: 14px; letter-spacing: .2em; text-transform: uppercase; }
  .struk-header p  { font-size: 11px; color: #8a887f; margin-top: 4px; }
  .struk-id { font-size: 11px; color: #8a887f; text-align: center; margin-bottom: 20px; }
  hr { border: none; border-top: 1px dashed #d8d6d0; margin: 16px 0; }
  .row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px; }
  .row.total { font-size: 14px; font-weight: 500; border-top: 1px solid #0a0a0a; padding-top: 12px; margin-top: 4px; }
  .row.kembalian { color: #8a887f; }
  .btn { display: block; width: 100%; margin-top: 24px; padding: 12px; background: #0a0a0a; color: #f5f4f0; border: none; font-family: 'DM Mono', monospace; font-size: 11px; letter-spacing: .15em; text-transform: uppercase; cursor: pointer; text-align: center; text-decoration: none; }
</style>
</head>
<body>
<div class="struk">
  <div class="struk-header">
    <h1>Toko Kasir</h1>
    <p><?= date('d M Y, H:i') ?></p>
  </div>
  <div class="struk-id">TRX #<?= str_pad($struk['id'], 6, '0', STR_PAD_LEFT) ?></div>
  <hr>
  <?php foreach ($struk['items'] as $item): ?>
  <div class="row">
    <span>Item #<?= $item['produk_id'] ?> ×<?= $item['jumlah'] ?></span>
    <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
  </div>
  <?php endforeach; ?>
  <div class="row total">
    <span>Total</span>
    <span>Rp <?= number_format($struk['total'], 0, ',', '.') ?></span>
  </div>
  <div class="row"><span>Bayar</span><span>Rp <?= number_format($struk['bayar'], 0, ',', '.') ?></span></div>
  <div class="row kembalian"><span>Kembalian</span><span>Rp <?= number_format($struk['kembalian'], 0, ',', '.') ?></span></div>
  <a href="?page=kasir" class="btn">← Transaksi Baru</a>
</div>
</body>
</html>