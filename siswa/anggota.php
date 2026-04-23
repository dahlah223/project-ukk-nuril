<?php
include '../koneksi.php';

$data = mysqli_query($koneksi, "SELECT * FROM anggota");
?>

<h2>Data Anggota</h2>

<table border="1">
<?php while($row = mysqli_fetch_assoc($data)) : ?>
<tr>
    <td><?= $row['nama_lengkap'] ?></td>
    <td><?= $row['nis'] ?></td>
    <td><?= $row['alamat'] ?></td>
</tr>
<?php endwhile; ?>
</table>