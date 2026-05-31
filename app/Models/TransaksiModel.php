<?php
class TransaksiModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Simpan transaksi lengkap dalam satu database transaction.
     * Mengembalikan ID transaksi atau melempar Exception jika gagal.
     */
    public function simpanTransaksi(float $totalHarga, float $uangBayar, array $items): int
    {
        $kembalian = $uangBayar - $totalHarga;

        $this->db->beginTransaction();

        try {
            // 1. Insert ke tabel transaksi
            $stmt = $this->db->prepare(
                "INSERT INTO transaksi (total_harga, uang_bayar, kembalian)
                 VALUES (:total, :bayar, :kembalian)
                 RETURNING id"
            );
            $stmt->execute([
                ':total'     => $totalHarga,
                ':bayar'     => $uangBayar,
                ':kembalian' => $kembalian,
            ]);
            $transaksiId = $stmt->fetchColumn();

            // 2. Insert detail & kurangi stok untuk setiap item
            $produkModel = new ProdukModel();
            $stmtDetail  = $this->db->prepare(
                "INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah, subtotal)
                 VALUES (:tid, :pid, :jumlah, :subtotal)"
            );

            foreach ($items as $item) {
                $produkId = (int)$item['produk_id'];
                $jumlah   = (int)$item['jumlah'];

                // Validasi stok di dalam transaksi
                if (!$produkModel->kurangiStok($produkId, $jumlah)) {
                    throw new Exception("Stok produk ID $produkId tidak mencukupi.");
                }

                $stmtDetail->execute([
                    ':tid'     => $transaksiId,
                    ':pid'     => $produkId,
                    ':jumlah'  => $jumlah,
                    ':subtotal'=> $item['subtotal'],
                ]);
            }

            $this->db->commit();
            return (int)$transaksiId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e; // Lempar ulang agar Controller bisa tangkap
        }
    }
}