<?php
require_once('../koneksi.php');

$bulan_pilihan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m'); 
$nama_bulan = date('F Y', strtotime($bulan_pilihan));

// 1. Query Ambil Data Transaksi
$query = "SELECT t.*, a.nama_lengkap, b.judul 
          FROM transaksi t
          JOIN anggota a ON t.anggota_id = a.id
          JOIN buku b ON t.buku_id = b.id
          WHERE t.tgl_pinjam LIKE '$bulan_pilihan%'
          ORDER BY t.tgl_pinjam ASC";
$result = $koneksi->query($query);

// 2. Query Ambil Data Identitas (Kepsek & Pustakawan)
$q_identitas = "SELECT * FROM identitas LIMIT 1";
$res_id = $koneksi->query($q_identitas);
$data_id = $res_id->fetch_assoc();

// Jika data kosong, beri nilai default garis bawah
$nama_kepsek = $data_id['nama_kepsek'] ?? "________________________";
$nama_pustakawan = $data_id['nama_pustakawan'] ?? "________________________";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan_PerpusKita</title>
    <style>
        @page { size: auto; margin: 10mm; }
        body { font-family: "Times New Roman", Times, serif; background: #fff; margin: 0; padding: 0; }
        .wrapper { width: 190mm; margin: auto; padding: 10mm; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h2 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 10pt; }
        .judul-laporan { text-align: center; margin-bottom: 30px; }
        .judul-laporan h3 { text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 10px 8px; font-size: 10pt; word-wrap: break-word; }
        th { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; text-transform: uppercase; }
        .ttd-table { width: 100%; margin-top: 50px; border: none; }
        .ttd-table td { border: none !text-align: center; width: 50%; vertical-align: top; text-align: center; }
        .spacer { height: 80px; }
        .no-print { background: #fff3cd; padding: 15px; border: 1px solid #ffeeba; margin: 20px; text-align: center; border-radius: 8px; font-family: sans-serif; }
        .btn-kembali { text-decoration: none; background: #4f46e5; color: white; padding: 8px 15px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        @media print { .no-print { display: none; } body { padding: 0; } .wrapper { padding: 0; width: 100%; } }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <p style="margin-top:0"><b>PENTING:</b> Agar tulisan URL hilang, klik <b>"More Settings"</b> dan <b>Hapus Centang</b> pada <b>"Headers and footers"</b>.</p>
        <a href="transaksi.php" class="btn-kembali">⬅️ KEMBALI KE TRANSAKSI</a>
    </div>

    <div class="wrapper">
        <div class="kop">
            <h2>PERPUSTAKAAN</h2>
            <p>Kajoran, Magelang Regency, Central Java 56163 | Telp: (0251) 1234567</p>
            <p>Email: info@smpkesatuan.sch.id</p>
        </div>

        <div class="judul-laporan">
            <h3>LAPORAN PENYEBARAN BAHAN PUSTAKA</h3>
            <p>Periode: <b><?= $nama_bulan ?></b></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="30px">No</th>
                    <th width="120px">Nama Siswa</th>
                    <th>Judul Buku</th>
                    <th width="80px">Tgl Pinjam</th>
                    <th width="70px">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="text-align:center;"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?= htmlspecialchars($row['judul']); ?></td>
                    <td style="text-align:center;"><?= date('d/m/Y', strtotime($row['tgl_pinjam'])); ?></td>
                    <td style="text-align:center;"><b><?= strtoupper($row['status']); ?></b></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Tanda Tangan Otomatis -->
        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>Kepala Sekolah<br>
                    <div class="spacer"></div>
                    <b>( <?= $nama_kepsek ?> )</b>
                </td>
                <td>
                    Magelang, <?= date('d F Y') ?><br>Kepala Perpustakaan<br>
                    <div class="spacer"></div>
                    <b>( <?= $nama_pustakawan ?> )</b>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
