<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["input_image"])) {
    // Mengambil informasi tentang file gambar yang diunggah
    $file_name = $_FILES["input_image"]["name"];
    $file_tmp = $_FILES["input_image"]["tmp_name"];

    // Tentukan folder tempat Anda ingin menyimpan gambar
    $upload_folder = "images/";

    // Pindahkan file gambar dari folder tempat sementara ke folder tujuan
    $destination = $upload_folder . $file_name;
    if (move_uploaded_file($file_tmp, $destination)) {
        // Membaca gambar dari file yang diunggah
        $gambar = imagecreatefromjpeg($destination);

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
        $output_file = 'output_tepi_laplace.jpg';
        imagejpeg($gambarTepi, $output_file);

        // Membebaskan memori
        imagedestroy($gambar);
        imagedestroy($gambarTepi);

        echo "Deteksi tepi dengan operator Laplace berhasil, hasil disimpan sebagai '$output_file'.";
    } else {
        echo "Gagal mengunggah file atau memindahkan file ke folder tujuan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deteksi Tepi dengan Operator Laplace</title>
</head>
<body>

    <h2>Form Unggah Gambar</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="input_image" accept=".jpg, .jpeg" required>
        <input type="submit" value="Unggah dan Deteksi Tepi">
    </form>

    <?php
    if (isset($file_name)) {
        echo "<h2>Gambar Input</h2>";
        echo "<img src='$upload_folder$file_name' alt='Gambar Input' width='300' height='200'>";
    }
    ?>

    <?php
    if (isset($output_file)) {
        echo "<h2>Gambar Output (Deteksi Tepi dengan Operator Laplace)</h2>";
        echo "<img src='$output_file' alt='Gambar Output' width='300' height='200'>";
    }
    ?>

</body>
</html>
