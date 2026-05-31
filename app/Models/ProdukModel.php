<?php
class ProdukModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /** Ambil semua produk yang masih ada stoknya */
    public function getAllProduk(): array
    {
        $stmt = $this->db->query("SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
        return $stmt->fetchAll();
    }

    /** Ambil satu produk berdasarkan ID */
    public function getProdukById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM produk WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Kurangi stok produk setelah transaksi */
    public function kurangiStok(int $produkId, int $jumlah): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE produk SET stok = stok - :jumlah
             WHERE id = :id AND stok >= :jumlah"
        );
        $stmt->execute([':jumlah' => $jumlah, ':id' => $produkId]);
        // Jika 0 baris terpengaruh → stok tidak cukup
        return $stmt->rowCount() > 0;
    }
}