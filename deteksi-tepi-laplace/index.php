<?php
// Membaca gambar
$gambar = imagecreatefromjpeg('input.jpg');

// Mendapatkan ukuran gambar
$lebar = imagesx($gambar);
$tinggi = imagesy($gambar);

// Membuat gambar baru untuk hasil deteksi tepi
$gambarTepi = imagecreatetruecolor($lebar, $tinggi);

// Loop melalui setiap piksel
for ($x = 1; $x < $lebar - 1; $x++) {
    for ($y = 1; $y < $tinggi - 1; $y++) {
        // Mendapatkan nilai intensitas piksel di sekitar piksel
        $pixelKiri = imagecolorat($gambar, $x - 1, $y);
        $pixelTengah = imagecolorat($gambar, $x, $y);
        $pixelKanan = imagecolorat($gambar, $x + 1, $y);

        // Mendapatkan komponen warna RGB masing-masing piksel
        $warnaKiri = imagecolorsforindex($gambar, $pixelKiri);
        $warnaTengah = imagecolorsforindex($gambar, $pixelTengah);
        $warnaKanan = imagecolorsforindex($gambar, $pixelKanan);

        // Menghitung gradien dengan operator Laplace menggunakan selisih pusat untuk setiap saluran warna
        $gradienMerah = $warnaKiri['red'] + $warnaKanan['red'] - 2 * $warnaTengah['red'];
        $gradienHijau = $warnaKiri['green'] + $warnaKanan['green'] - 2 * $warnaTengah['green'];
        $gradienBiru = $warnaKiri['blue'] + $warnaKanan['blue'] - 2 * $warnaTengah['blue'];

        // Menghitung gradien akhir
        $gradien = sqrt($gradienMerah**2 + $gradienHijau**2 + $gradienBiru**2);

        // Memastikan gradien berada dalam rentang yang valid
        $gradien = max(0, min(255, $gradien));

        // Menetapkan warna piksel ke gambar deteksi tepi
        $warnaTepi = imagecolorallocate($gambarTepi, (int) $gradien, (int) $gradien, (int) $gradien);
        imagesetpixel($gambarTepi, $x, $y, $warnaTepi);
    }
}

// Menyimpan gambar hasil deteksi tepi
imagejpeg($gambarTepi, 'output_tepi_laplace.jpg');

// Membebaskan memori
imagedestroy($gambar);
imagedestroy($gambarTepi);

echo "Deteksi tepi dengan operator Laplace berhasil, hasil disimpan sebagai 'output_tepi_laplace.jpg'.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deteksi Tepi dengan Operator Laplace</title>
</head>
<body>

<h2>Gambar Input</h2>
<img src="input.jpg" alt="Gambar Input" width="300" height="200">

<h2>Gambar Output (Deteksi Tepi dengan Operator Laplace)</h2>
<img src="output_tepi_laplace.jpg" alt="Gambar Output" width="300" height="200">

</body>
</html>
