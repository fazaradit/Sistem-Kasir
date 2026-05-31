<?php
class KasirController
{
    private ProdukModel $produkModel;
    private TransaksiModel $transaksiModel;

    public function __construct()
    {
        $this->produkModel    = new ProdukModel();
        $this->transaksiModel = new TransaksiModel();
    }

    /** GET  → Tampilkan halaman kasir */
    public function index(): void
    {
        $produkList = $this->produkModel->getAllProduk();
        require_once ROOT_PATH . '/app/Views/kasir.php';
    }

    /** POST → Proses transaksi dari form */
    public function proses(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=kasir');
            exit;
        }

        // Ambil & validasi data dari form
        $itemsRaw  = $_POST['items']    ?? [];
        $uangBayar = (float)($_POST['uang_bayar'] ?? 0);

        if (empty($itemsRaw)) {
            $this->redirectWithError('Keranjang belanja kosong.');
        }

        // Bangun array items yang bersih
        $items      = [];
        $totalHarga = 0;

        foreach ($itemsRaw as $item) {
            $produkId = (int)($item['produk_id'] ?? 0);
            $jumlah   = (int)($item['jumlah']    ?? 0);

            if ($produkId <= 0 || $jumlah <= 0) continue;

            $produk = $this->produkModel->getProdukById($produkId);
            if (!$produk) continue;

            $subtotal    = $produk['harga'] * $jumlah;
            $totalHarga += $subtotal;
            $items[]     = [
                'produk_id' => $produkId,
                'jumlah'    => $jumlah,
                'subtotal'  => $subtotal,
            ];
        }

        if ($uangBayar < $totalHarga) {
            $this->redirectWithError('Uang bayar kurang dari total harga.');
        }

        try {
            $transaksiId = $this->transaksiModel->simpanTransaksi($totalHarga, $uangBayar, $items);
            $kembalian   = $uangBayar - $totalHarga;

            // Simpan hasil ke session untuk ditampilkan di struk
            session_start();
            $_SESSION['struk'] = [
                'id'         => $transaksiId,
                'items'      => $items,       // bisa diperkaya dengan nama produk
                'total'      => $totalHarga,
                'bayar'      => $uangBayar,
                'kembalian'  => $kembalian,
            ];

            header('Location: ?page=kasir&action=struk');
            exit;

        } catch (Exception $e) {
            $this->redirectWithError($e->getMessage());
        }
    }

    /** Tampilkan struk setelah transaksi berhasil */
    public function struk(): void
    {
        session_start();
        $struk = $_SESSION['struk'] ?? null;

        if (!$struk) {
            header('Location: ?page=kasir');
            exit;
        }

        unset($_SESSION['struk']); // Struk hanya bisa dilihat sekali
        require_once ROOT_PATH . '/app/Views/struk.php';
    }

    private function redirectWithError(string $pesan): void
    {
        session_start();
        $_SESSION['error'] = $pesan;
        header('Location: ?page=kasir');
        exit;
    }
}