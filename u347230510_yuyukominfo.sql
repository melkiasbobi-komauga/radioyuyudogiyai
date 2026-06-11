-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 05 Jun 2026 pada 01.47
-- Versi server: 11.8.6-MariaDB-log
-- Versi PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u347230510_yuyukominfo`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`) VALUES
(8, 'admin', '$2y$10$o9NWZnPHS9isumrCX.zcyexqE6vo.WZNd.nJ7u2SUR0.xGTFI31BW', 'Administrator Utama');

-- --------------------------------------------------------

--
-- Struktur dari tabel `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `agenda`
--

INSERT INTO `agenda` (`id`, `nama_kegiatan`, `deskripsi`, `tanggal_mulai`, `waktu_mulai`, `tanggal_selesai`, `waktu_selesai`, `lokasi`) VALUES
(1, 'Siaran Langsung Bersama Bupati Dogiyai', 'Pembahasan program kerja pemerintah daerah untuk tahun berjalan.', '2025-08-15', '09:00:00', NULL, NULL, 'Studio RAKOM Dogiyai'),
(2, 'Pelatihan Jurnalistik untuk Pemuda', 'Workshop dasar-dasar jurnalistik radio bagi pemuda-pemudi Dogiyai.', '2025-09-01', '10:00:00', NULL, NULL, 'Aula Kantor Kominfo');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL COMMENT 'Nama file gambar',
  `penulis` varchar(100) DEFAULT NULL,
  `tanggal_publikasi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berita`
--

INSERT INTO `berita` (`id`, `judul`, `konten`, `gambar`, `penulis`, `tanggal_publikasi`) VALUES
(3, 'Dinas Kominfo Dogiyai Borong Dagangan Mama-Mama Papua di Pasar Tokapo', '<p>Dinas Komunikasi dan Informatika (Kominfo) Kabupaten Dogiyai kembali melaksanakan program belanja hasil jualan mama-mama Papua pada Jumat (01/08). Kegiatan berlangsung di Pasar Tokapo Kampung Mauwa, Distrik Kamuu, sebagai bentuk nyata dukungan terhadap ekonomi rakyat kecil. Dipimpin langsung oleh Kepala Dinas Kominfo, Yulianus Waine, rombongan menyambangi tempat jualan milik mama-mama. Mereka membeli berbagai hasil kebun dan makanan lokal seperti kacang tanah, ubi, hingga buah-buahan. Turut hadir dalam kegiatan tersebut Sekretaris Dinas Kominfo, Marius Yobee, dan seluruh staf dinas yang tergabung dalam rombongan. Kehadiran mereka tidak sekadar membeli, tetapi juga menyapa dan memberi semangat kepada para pedagang. Seluruh hasil belanja kemudian dibawa ke kantor Dinas Kominfo dan dinikmati dalam acara makan bersama. Tradisi ini sudah menjadi rutinitas setiap Jumat, sebagai upaya membangun kebersamaan internal. Kepala Dinas Kominfo, Yulianus Waine, menegaskan bahwa kegiatan ini adalah perintah langsung dari Bupati Dogiyai. Menurutnya, semua Organisasi Perangkat Daerah (OPD) wajib membeli hasil dagangan mama-mama setiap hari Jumat sebagai bentuk keberpihakan terhadap ekonomi Orang Asli Papua (OAP). &ldquo;Ini bukan sekadar kegiatan seremonial, tapi langkah konkret pemerintah hadir di tengah rakyat,&rdquo; ujar Waine. Ia juga menyebut mama-mama Papua sebagai pelaku utama dalam rantai ekonomi lokal yang harus dihargai. Sekretaris Kominfo, Marius Yobee, menambahkan bahwa pihaknya konsisten mendukung program Bupati. Ia menilai keberadaan pemerintah harus memberi dampak langsung, terutama kepada masyarakat kecil. &ldquo;Kami ingin memberikan pengakuan kepada mama-mama pasar sebagai tulang punggung ekonomi lokal,&rdquo; kata Yobee. Menurutnya, kehadiran langsung di pasar adalah bentuk penghargaan nyata. Program belanja rutin ini diharapkan menjadi contoh bagi OPD lain di lingkungan Pemkab Dogiyai. Selain mendukung ekonomi kerakyatan, kegiatan ini juga memperkuat solidaritas dan sinergi antarlembaga.</p>\r\n', '6891fb27cba9b.jpg', 'Admin', '2025-08-05 12:37:59'),
(4, 'Kepala Dinas Kominfo Dogiyai,Yulianus Waine menyerahkan DPA pada Kepala Bidang Komunikasi & Telekomunikasi Niko Tebai', '<p><strong>DOGIYAI&ndash;BUPATI YUDAS&amp;YULITEN,<em>News.</em></strong>&nbsp;Pemerintahan&nbsp;<em>Yudas Tebai,S.Pd.,M.Si&nbsp;</em>dan&nbsp;<em>Yuliten Anouw,SE</em>&nbsp;di kabupaten Dogiyai, Papua Tengah sedang menunjukan tindakan penting untuk meningkatkan kualitas pemerintahan melalui keterbukaan (transparansi) dalam penggunaan anggaran.</p>\r\n\r\n<p>Sebagai upaya tindak lanjut gerakan reformasi birokrasi tersebut, Kepala Dinas Kominfo Kabupaten Dogiyai,<em>Yulianus Waine,S.IP,</em>&nbsp;membagikan Dokumen Pelaksanaan Anggaran (DPA) tahun anggaran 2025 kepada Kepala Bidang Komunikasi &amp; Telekomunikasi Niko Tebai melalui rapat internal yang dilaksanakan pada, Senin (14/7/25) di Kantor Kominfo Dogiyai.</p>\r\n\r\n<p>&ldquo; Pembagian DPA terbuka ini,Sesuai instruksi bupati pada beberapa waktu lalu.kemarin kami telah membagikan DPA kepada Kepala Bidang Komunikasi &amp; Telekomunikasi Niko Tebai agar melaksanakan dan bertanggung jawab sesuai tupoksinya masing-masing,&rdquo;ucap Yulianus Waine Ketika dihubungi melalui Telepon pribadinya pada Selasa,15 Juli Pagi di Moanemani, Dogiyai.</p>\r\n\r\n<p><em>Yulianus Waine</em>&nbsp;mengatakan ketika Hubungi, keterbukaan itu penting untuk dapat mengetahui bagaimana anggaran digunakan dan untuk apa dan adanya rasa saling memiliki antara atasan dan bawahan.</p>\r\n\r\n<p>&ldquo;Terbuka seperti ini penting supaya sama-sama mengetahui angaran digunakan untuk apa dan adanya rasa saling memiliki,&rdquo;katanya.</p>\r\n\r\n<p>Lebih dari kata dia, untuk mendukung peningkatan kinerja, mempercepat pelayanan, dan yang terpenting, memperhatikan kesejahteraan para pegawai.</p>\r\n\r\n<p>&ldquo;saya minta kepada kepala bidang yang saya serahkan DPA,agar terbuka kepada staf dan mengutamakan kesejahteraan mereka,&rdquo; imbuhnya.</p>\r\n\r\n<p>Kepala Dinas&nbsp;<em>Yulianus Waine. S. IP.</em>&nbsp;menyerahkan DPA Ke Kepala Bidang Komunikasi dan Telekomunikasi di lingkungan kantor kominfo yang Di Saksikan Oleh Staf Dinas Kominfo Kabupaten Dogiyai.</p>\r\n\r\n<p>Usai menyerahkan DPA,Selaku Kepala Bidang Komunikasi dan Telekomunikasi,<em>Niko Tebai</em>&nbsp;mengucapkan banyak Terimakasih Kepada Kepala Dinas Kominfo Kabupaten Dogiyai,Bapak&nbsp;<em>Yulianus Waine,S.IP</em>&nbsp;Atas Kepercayaannya.sehingga sesuai Tugas dan fungsi saya tetap akan lakukan&rdquo;, Ujar Niko Tebai.</p>\r\n', 'berita-692c32deb6ffd9.10723109.jpg', 'Admin', '2025-11-30 12:04:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chart_lagu`
--

CREATE TABLE `chart_lagu` (
  `id` int(11) NOT NULL,
  `peringkat` int(11) NOT NULL,
  `judul_lagu` varchar(255) NOT NULL,
  `artis` varchar(255) NOT NULL,
  `cover_album` varchar(255) DEFAULT NULL,
  `file_audio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `chart_lagu`
--

INSERT INTO `chart_lagu` (`id`, `peringkat`, `judul_lagu`, `artis`, `cover_album`, `file_audio`) VALUES
(2, 2, 'Cintanya Pramuria', 'Leola Drakel', 'cover-6931453313a7f1.10003667.jpg', 'lagu-69314533140220.46223771.mp3'),
(3, 3, 'Rindu Dogiyai', 'Penyanyi Solo Dogiyai', '6895df913376d.jpg', NULL),
(4, 4, 'Senja di Pantai Nabire', 'Band Reggae Nabire', '6895df9b48cc8.jpg', NULL),
(5, 5, 'Suara Hutan', 'Musisi Etnik Jayapura', '6895dfa675b89.jpg', NULL),
(6, 1, 'Akhir Sebuah Kisah', 'Black Sweet', 'cover-6931421be143c1.65201991.jpg', 'lagu-6931421be16669.20283223.mp3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_balasan`
--

CREATE TABLE `forum_balasan` (
  `id` int(11) NOT NULL,
  `id_topik` int(11) NOT NULL,
  `nama_pembalas` varchar(100) NOT NULL,
  `isi_balasan` text NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `forum_balasan`
--

INSERT INTO `forum_balasan` (`id`, `id_topik`, `nama_pembalas`, `isi_balasan`, `tanggal_dibuat`) VALUES
(1, 1, 'Penyiar RAKOM', 'Terima kasih atas sarannya! Usulan Anda sudah kami catat dan akan kami diskusikan dengan tim program.', '2025-08-08 11:38:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_topik`
--

CREATE TABLE `forum_topik` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `nama_pembuat` varchar(100) NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `forum_topik`
--

INSERT INTO `forum_topik` (`id`, `judul`, `isi`, `nama_pembuat`, `tanggal_dibuat`) VALUES
(1, 'Saran Program Musik Daerah', 'Saya punya usul, bagaimana jika program Musik Noken juga memutar lagu-lagu dari suku Yali? Pasti akan sangat menarik.', 'Pendengar Setia', '2025-08-08 11:38:49'),
(2, 'Informasi Beasiswa Pendidikan', 'Apakah ada informasi terbaru mengenai beasiswa untuk anak-anak Dogiyai yang ingin melanjutkan kuliah?', 'Orang Tua Murid', '2025-08-08 11:38:49'),
(3, 'Layanan Perizinan', 'saya mau bertanya', 'Melkias bobi', '2025-12-03 11:23:53'),
(4, 'Sejumlah Elemen Serahkan Aspirasi Terkait Rencana DOB Mapia Raya ke DPRP Papua Tengah', 'Sejumlah Elemen Serahkan Aspirasi Terkait Rencana DOB Mapia Raya ke DPRP Papua Tengah', 'Melkias bobi', '2025-12-03 11:26:00'),
(5, 'Layanan Perizinan', 'llllll', 'Melkias bobi', '2025-12-03 11:28:21'),
(6, 'saya', 'twntna', 'melky', '2025-12-03 11:45:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_media` varchar(255) NOT NULL COMMENT 'Nama file gambar atau URL video',
  `tipe_media` enum('foto','video') NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `galeri`
--

INSERT INTO `galeri` (`id`, `judul`, `keterangan`, `file_media`, `tipe_media`, `tanggal_upload`) VALUES
(1, 'Peresmian Studio Baru', 'Peresmian studio baru RAKOM Dogiyai oleh Bupati.', '689227f85d84c.jpg', 'foto', '2025-08-05 03:34:21'),
(2, 'Targetkan 19 Persen PDB dari Ekonomi Digital, Kemkomdigi Gandeng Australia', 'Menteri Komunikasi dan Digital Meutya Hafid menegaskan arti penting mempercepat kolaborasi lintas sektor untuk memperkuat transformasi digital nasional.', 'https://youtu.be/m_KJnArQZDE?si=M_FXXDaJX7QjK-qp', 'video', '2025-08-05 03:34:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `hari` varchar(50) NOT NULL DEFAULT 'Setiap Hari',
  `waktu` varchar(50) NOT NULL,
  `program` varchar(255) NOT NULL,
  `penyiar` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id`, `hari`, `waktu`, `program`, `penyiar`) VALUES
(1, 'Setiap Hari', '06:00 - 08:00', 'Morning Show', 'Yohanis D'),
(2, 'Setiap Hari', '08:00 - 10:00', 'Info Pemerintah', 'Maria W'),
(3, 'Setiap Hari', '10:00 - 12:00', 'Musik Daerah', 'Petrus K'),
(4, 'Setiap Hari', '12:00 - 14:00', 'Berita Siang', 'Yuliana M'),
(5, 'Setiap Hari', '14:00 - 16:00', 'Program Pendidikan', 'Simon T'),
(6, 'Setiap Hari', '16:00 - 18:00', 'Anak Muda', 'Ruben & Siska'),
(7, 'Setiap Hari', '18:00 - 20:00', 'Berita Malam', 'Tim Berita'),
(8, 'Setiap Hari', '20:00 - 22:00', 'Relaksasi Musik', 'Otomatis');

-- --------------------------------------------------------

--
-- Struktur dari tabel `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `tanggal_daftar`) VALUES
(1, 'komaugastudio@gmail.com', '2025-11-26 23:51:46'),
(2, 'gnxgdizw@checkyourform.xyz', '2026-01-22 03:16:19'),
(3, 'hxpuyspz@checkyourform.xyz', '2026-01-22 03:16:20'),
(4, 'xvfzmyff@checkyourform.xyz', '2026-01-24 15:46:40'),
(5, 'hdutujhg@checkyourform.xyz', '2026-01-26 11:21:48'),
(6, 'onzizfqs@checkyourform.xyz', '2026-01-26 11:22:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi_pengumuman` text NOT NULL,
  `file_lampiran` varchar(255) DEFAULT NULL COMMENT 'Nama file lampiran',
  `tanggal_berakhir` date DEFAULT NULL COMMENT 'Kapan pengumuman tidak lagi relevan',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('aktif','tidak aktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi_pengumuman`, `file_lampiran`, `tanggal_berakhir`, `tanggal_dibuat`, `status`) VALUES
(1, 'Pemeliharaan Jaringan Listrik', 'Akan ada pemadaman siaran sementara pada tanggal 15 Agustus 2025 dari pukul 10:00 hingga 12:00 WIT sehubungan dengan pemeliharaan jaringan listrik oleh PLN.', '', '2025-08-16', '2025-08-06 15:11:45', 'aktif'),
(2, 'Pendaftaran Lomba Baca Puisi', 'Pendaftaran untuk lomba baca puisi dalam rangka HUT RI telah dibuka! Hubungi kontak kami untuk informasi lebih lanjut.', NULL, '2025-08-10', '2025-08-06 15:11:45', 'aktif'),
(3, 'Dinas Kominfo Dogiyai Borong Dagangan Mama-Mama Papua di Pasar Tokapo', 'File ini akan menampilkan semua pengumuman yang berstatus \"aktif\" dari database Anda, lengkap dengan tombol untuk mengunduh lampiran jika ada.', '689379f066b3f.docx', '2025-08-06', '2025-08-06 15:51:12', 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan_kontak`
--

CREATE TABLE `pesan_kontak` (
  `id` int(11) NOT NULL,
  `nama_pengirim` varchar(100) NOT NULL,
  `email_pengirim` varchar(100) NOT NULL,
  `subjek` varchar(255) DEFAULT NULL,
  `isi_pesan` text NOT NULL,
  `tanggal_kirim` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('belum dibaca','sudah dibaca') NOT NULL DEFAULT 'belum dibaca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesan_kontak`
--

INSERT INTO `pesan_kontak` (`id`, `nama_pengirim`, `email_pengirim`, `subjek`, `isi_pesan`, `tanggal_kirim`, `status`) VALUES
(1, 'saya', 'melkiasbobi@gmail.com', 'pertanyaan', 'selamat pagi', '2025-08-05 12:35:03', 'sudah dibaca'),
(2, 'saya', 'melkiasbobi@gmail.com', 'pertanyaan', 'selamat pagi', '2025-08-05 12:35:03', 'sudah dibaca'),
(3, 'melky', 'melkiasbobi@gmail.com', 'pertanyaan', 'saya', '2025-08-06 01:08:30', 'sudah dibaca'),
(5, 'Chas Shoebridge', 'info@domainsreg.pro', 'radioyuyudogiyai.com', 'Hi,\r\n\r\nAdd your radioyuyudogiyai.com website in Google Search Index to be displayed in WebSearch Results.\r\n\r\nList radioyuyudogiyai.com at https://searchregister.info', '2025-12-11 12:46:31', 'belum dibaca'),
(6, 'Dear http://radioyuyudogiyai.com/fekal0911 Administrator', 'pirduhina96@gmail.com', 'Hi http://radioyuyudogiyai.com/fekal0911 Owner', 'To the http://radioyuyudogiyai.com/fekal0911 Administrator', '2026-01-19 07:35:16', 'belum dibaca'),
(7, 'xluhlwpqjf', 'tsynwmgx@checkyourform.xyz', 'qnovdsdkuz', 'unpnkummdyhqeloxtdyiqpmxkwhwnj', '2026-01-22 03:16:36', 'belum dibaca'),
(8, 'wmnxgkvxfg', 'ohokeofm@checkyourform.xyz', 'ijvwxltjov', 'lykqxdhuvsyhzqegsmqmkzktshvtff', '2026-01-24 15:46:40', 'belum dibaca'),
(9, 'dnfkzdmkgl', 'srxewtlr@checkyourform.xyz', 'tgjvhqrvdl', 'mufdphjjsrgkpojnnfkykjjnvgfqyj', '2026-01-26 11:21:48', 'belum dibaca');

-- --------------------------------------------------------

--
-- Struktur dari tabel `podcast`
--

CREATE TABLE `podcast` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_audio` varchar(255) NOT NULL,
  `file_gambar` varchar(255) DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `podcast`
--

INSERT INTO `podcast` (`id`, `judul`, `deskripsi`, `file_audio`, `file_gambar`, `tanggal_upload`) VALUES
(1, 'Dialog Interaktif: Masa Depan Pendidikan di Dogiyai', 'Diskusi mendalam bersama Kepala Dinas Pendidikan tentang tantangan dan inovasi dalam sistem pendidikan lokal.', '68959bb7d46f9.mp3', '68959bb7d3ec0.jpg', '2025-08-08 05:53:10'),
(2, 'Cerita Rakyat Suku Mee: Asal Usul Danau Tigi', 'Dengarkan kembali kisah legendaris tentang asal-usul Danau Tigi yang diceritakan oleh tetua adat.', '689594931d4f8.mp3', '689594931cfc1.jpeg', '2025-08-08 05:53:10'),
(3, 'Musik Noken Edisi Spesial: Wawancara Musisi Lokal', 'Edisi spesial program Musik Noken yang menghadirkan wawancara dan penampilan akustik dari musisi muda berbakat Dogiyai.', '689594b745529.mp3', '689594b745039.jpg', '2025-08-08 05:53:10'),
(4, 'Peran Pemuda Dogiyai Berantas Penyakit Sosial', 'Pemuda di Kabupaten Dogiyai, Papua, memegang peran strategis dalam memberantas penyakit sosial melalui berbagai pendekatan. ', '689b1a2d68e7d.mp3', '689b1a2d621c3.png', '2025-08-12 10:40:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil_website`
--

CREATE TABLE `profil_website` (
  `id` int(11) NOT NULL,
  `nama_pengaturan` varchar(50) NOT NULL COMMENT 'e.g., sejarah, visi, misi, alamat',
  `isi_pengaturan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil_website`
--

INSERT INTO `profil_website` (`id`, `nama_pengaturan`, `isi_pengaturan`) VALUES
(1, 'profil_singkat', 'Radio YUYU Kominfo Dogiyai (RAYUKOM Dogiyai) adalah lembaga penyiaran publik lokal yang berkomitmen untuk menyajikan informasi yang akurat, edukatif, dan menghibur bagi seluruh masyarakat Dogiyai.'),
(2, 'sejarah', 'Didirikan pada tahun 2025, RAYUKOM Dogiyai berawal dari inisiatif pemerintah daerah untuk menciptakan media pemersatu dan sumber informasi terpercaya di tengah tantangan geografis. Sejak mengudara pertama kali, kami terus berinovasi untuk melayani pendengar setia.'),
(3, 'visi', 'Menjadi media informasi dan komunikasi terdepan yang mendorong kemajuan dan partisipasi masyarakat Dogiyai.'),
(4, 'misi', '1. Menyajikan program siaran yang informatif, edukatif, dan inspiratif.\r\n2. Menjadi wadah aspirasi dan kreativitas masyarakat.\r\n3. Melestarikan budaya dan kearifan lokal melalui program siaran.\r\n4. Mendukung program pembangunan Pemerintah Kabupaten Dogiyai.'),
(5, 'alamat_kantor', 'Jl. Trans Papua, Kigamani, Kabupaten Dogiyai, Papua Tengah'),
(6, 'email_kontak', 'kontak@radioyuyudogiyai.go.id'),
(7, 'telepon_kontak', '(0984) 123-456'),
(8, 'logo_file', 'logo-692ecf83b949a.png'),
(9, 'station_name', 'RADIO YUYU KOMINFO'),
(10, 'organization_name', '107.7 FM - SUARA DOGIYAI'),
(11, 'copyright_text', 'Dinas Komunikasi dan Informatika'),
(12, 'iframe_peta', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `program_unggulan`
--

CREATE TABLE `program_unggulan` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data untuk tabel `program_unggulan`
--

INSERT INTO `program_unggulan` (`id`, `judul`, `deskripsi`, `gambar`, `tanggal_dibuat`) VALUES
(1, 'Suara Pemerintah', 'Informasi resmi & kebijakan Pemkab Dogiyai.', 'prog-692ebd27482a53.07968759.jpg', '2025-12-02 07:38:05'),
(2, 'Musik Noken', 'Melestarikan musik khas daerah Papua & Dogiyai.', 'prog-692ebd1ab45e14.77705061.jpg', '2025-12-02 07:38:05'),
(3, 'Pendidikan Kita', 'Diskusi inspiratif seputar dunia pendidikan.', 'prog-692ebd0d302713.59403213.jpg', '2025-12-02 07:38:05'),
(4, 'Suara Masyarakat', 'Wadah aspirasi warga untuk pembangunan.', 'prog-692ebcfcef3e14.59664350.jpg', '2025-12-02 07:38:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimoni`
--

CREATE TABLE `testimoni` (
  `id` int(11) NOT NULL,
  `nama_pengirim` varchar(100) NOT NULL,
  `peran` varchar(100) DEFAULT 'Pendengar Setia',
  `pesan` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tanggal_kirim` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('tampil','sembunyi') NOT NULL DEFAULT 'sembunyi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `testimoni`
--

INSERT INTO `testimoni` (`id`, `nama_pengirim`, `peran`, `pesan`, `foto`, `tanggal_kirim`, `status`) VALUES
(1, 'Agus T.', 'Warga Kamu', 'Sangat membantu untuk mendapatkan informasi terbaru seputar pembangunan di Dogiyai. Maju terus RAKOM!', NULL, '2025-11-26 14:08:29', 'tampil'),
(2, 'Maria Y.', 'Mahasiswa', 'Program musik daerahnya sangat menghibur, terutama saat sedang belajar. Sukses selalu buat penyiar-penyiarnya.', NULL, '2025-11-26 14:08:29', 'tampil'),
(3, 'Pak Yulianus', 'Tokoh Masyarakat', 'Radio ini menjadi jembatan informasi yang efektif antara pemerintah dan masyarakat. Pertahankan kualitasnya.', NULL, '2025-11-26 14:08:29', 'tampil'),
(4, 'YULI T', 'Masyarakat', 'Terimaksih Radio Yuyu dogiyai sudah hadir untuk kami masyarakat dogiyai', NULL, '2025-11-30 07:45:09', 'tampil'),
(6, 'AndrewdemCX', 'Pendengar Setia', 'Howdy-ho! radioyuyudogiyai.com \r\n \r\nDid you know that it is possible to send appeal in a fully legitimate manner? \r\nWhen such proposals are sent, no personal data is used and messages are directed to specially designed forms in order to receive messages and appeals. It is improbable to have Feedback Forms messages marked as spam, since they are considered important. \r\nTry our service out – it’s free of charge! \r\nWe can dispatch up to 50,000 messages in your behalf. \r\n \r\nThe cost of sending one million messages is $59. \r\n \r\nThis offer is automatically generated. \r\n \r\nContact us. \r\nTelegram - https://t.me/FeedbackFormEU \r\nWhatsApp - +375259112693 \r\nWhatsApp  https://wa.me/+375259112693 \r\nWe only use chat for communication.', NULL, '2025-12-27 01:37:12', 'sembunyi'),
(7, 'Mike Ralf Girard\r\nCV', 'Pendengar Setia', 'Hi, \r\nI understand that most website owners find it challenging recognizing that organic ranking growth is a long-term game and a carefully organized ongoing investment. \r\n \r\nUnfortunately, very few businesses have the patience to observe the progressive yet significant benefits that can completely transform their online presence. \r\n \r\nWith regular search engine updates, a consistent, long-term strategy including Answer Engine Optimization (AEO) is critical for getting a strong return on investment. \r\n \r\nIf you recognize this as the best approach, partner with us! \r\n \r\nDiscover Our Monthly SEO Services https://www.digital-x-press.com/unbeatable-seo/ \r\n \r\nTalk to Us on Instant Messaging https://www.digital-x-press.com/whatsapp-us/ \r\n \r\nWe provide unbeatable outcomes for your budget, and you will value choosing us as your digital marketing ally. \r\n \r\nWarm regards, \r\nDigital X SEO Experts \r\nPhone/WhatsApp: +1 (844) 754-1148', NULL, '2026-01-04 13:41:32', 'sembunyi'),
(8, 'Mike Karl-Erik Schulz\r\nKY', 'Pendengar Setia', 'Hi, \r\n \r\nSearch is changing faster than most businesses realize. \r\n \r\nMore buyers are now discovering products and services through AI-driven platforms — not only traditional search results. This is why we created the AI Rankings SEO Plan at Monkey Digital. \r\n \r\nIt’s designed to help websites become clear, trusted, and discoverable by AI systems that increasingly influence how people find and choose businesses. \r\n \r\nYou can view the plan here: \r\nhttps://www.monkeydigital.co/ai-rankings/ \r\n \r\nIf you’d like to see whether this approach makes sense for your site, feel free to reach out directly — even a quick question is fine. Whatsapp: https://wa.link/b87jor \r\n \r\n \r\n \r\nBest regards, \r\nMike Karl-Erik Schulz\r\n \r\nMonkey Digital \r\nmike@monkeydigital.co \r\nPhone/Whatsapp: +1 (775) 314-7914', NULL, '2026-01-04 21:14:24', 'sembunyi'),
(9, 'Mike Stephane Johnson\r\nRD', 'Pendengar Setia', 'Greetings, \r\n \r\nHaving some collection of links redirecting to radioyuyudogiyai.com may result in 0 value or harmful results for your website. \r\n \r\nIt really makes no difference how many external links you have, what is key is the amount of keywords those platforms appear in search for. \r\n \r\nThat is the most important thing. \r\nNot the fake Moz DA or Domain Rating. \r\nThat anyone can do these days. \r\nBUT the volume of high-traffic search terms the domains that link to you contain. \r\nThat’s it. \r\n \r\nGet these quality links redirect to your site and your rankings will skyrocket! \r\n \r\nWe are providing this exclusive offer here: \r\nhttps://www.strictlydigital.net/product/semrush-backlinks/ \r\n \r\nNeed more details, or want to know more, chat with us here: \r\nhttps://www.strictlydigital.net/whatsapp-us/ \r\n \r\nSincerely, \r\nMike Stephane Johnson\r\n \r\nstrictlydigital.net \r\nPhone/WhatsApp: +1 (877) 566-3738', NULL, '2026-01-09 15:09:38', 'sembunyi'),
(10, 'Olivier Gabriel Balzac', 'Pendengar Setia', 'Good day, \r\n \r\nMy name is Olivier Gabriel Balzac, a practicing lawyer from France. I previously contacted you regarding a transaction involving 13.5 million Euros, which was left by my late client before his unexpected demise. \r\n \r\nI am reaching out to you once more because, after examining your profile, I am thoroughly convinced that you are capable of managing this transaction effectively alongside me. \r\nIf you are interested, I would like to emphasize that after the transaction, 5% of the funds will be allocated to charitable organizations, while the remaining 95% will be divided equally between us, resulting in 47.5% for each party. \r\nThis transaction is entirely risk-free. Please respond to me at your earliest convenience to receive further details regarding the transaction. \r\nMy email: info@balzacavocate.com Sincerely, I look forward to your prompt response. \r\nBest regards. \r\nOlivier Gabriel Balzac, \r\nAttorney. \r\nPhone: +33 756 850 084 \r\nEmail: info@balzacavocate.com', NULL, '2026-01-23 06:13:24', 'sembunyi'),
(11, 'Mike Thomas Simonson\r\nCV', 'Pendengar Setia', 'Hi, \r\nI understand that most website owners find it challenging recognizing that organic ranking growth is a gradual process and a well-planned regular commitment. \r\n \r\nThe reality is, very few businesses have the dedication to recognize the gradual yet significant results that can completely transform their online presence. \r\n \r\nWith regular search engine updates, a stable, continuous SEO strategy including Answer Engine Optimization (AEO) is vital for getting a strong return on investment. \r\n \r\nIf you recognize this as the right method, collaborate with us! \r\n \r\nCheck out Our Monthly SEO Services https://www.digital-x-press.com/unbeatable-seo/ \r\n \r\nTalk to Us on Instant Messaging https://www.digital-x-press.com/whatsapp-us/ \r\n \r\nWe deliver exceptional performance for your investment, and you will enjoy choosing us as your SEO partner. \r\n \r\nKind regards, \r\nDigital X SEO Experts \r\nPhone/WhatsApp: +1 (844) 754-1148', NULL, '2026-01-27 13:23:19', 'sembunyi'),
(12, 'Mike Felix Bernard\r\nKY', 'Pendengar Setia', 'Hi, \r\n \r\nSearch is changing faster than most businesses realize. \r\n \r\nMore buyers are now discovering products and services through AI-driven platforms — not only traditional search results. This is why we created the AI Rankings SEO Plan at Monkey Digital. \r\n \r\nIt’s designed to help websites become clear, trusted, and discoverable by AI systems that increasingly influence how people find and choose businesses. \r\n \r\nYou can view the plan here: \r\nhttps://www.monkeydigital.co/ai-rankings/ \r\n \r\nIf you’d like to see whether this approach makes sense for your site, feel free to reach out directly — even a quick question is fine. Whatsapp: https://wa.link/b87jor \r\n \r\n \r\n \r\nBest regards, \r\nMike Felix Bernard\r\n \r\nMonkey Digital \r\nmike@monkeydigital.co \r\nPhone/Whatsapp: +1 (775) 314-7914', NULL, '2026-01-28 08:55:30', 'sembunyi'),
(13, 'Michalak Aleksandra', 'Pendengar Setia', 'Good day. \r\nMy name is Michalak Aleksandra, a Poland based business consultant. \r\nRunning a business means juggling a million things, and getting the funding you need shouldn\'t be another hurdle. We\'ve helped businesses to secure debt financing for growth, inventory, or operations, without the typical bank delays. \r\nTogether with our partners (investors), we offer a straightforward, transparent process with clear terms, designed to get you funded quickly so you can focus on your business. \r\nReady to explore our services? Please feel free to contact me directly by email: michalakaleksandrama@gmail.com Let\'s make your business goals a reality, together. \r\nRegards, \r\nMichalak Aleksandra. \r\nEmail:michalakaleksandrama@gmail.com', NULL, '2026-02-05 09:03:25', 'sembunyi'),
(14, 'Mike Jorg Mercier\r\nRD', 'Pendengar Setia', 'Greetings, \r\n \r\nGetting some set of links linking to radioyuyudogiyai.com may result in no value or worse for your business. \r\n \r\nIt really makes no difference the total backlinks you have, what is crucial is the number of keywords those domains rank for. \r\n \r\nThat is the critical factor. \r\nNot the meaningless third-party metrics or ahrefs DR score. \r\nAnyone can manipulate those. \r\nBUT the number of Google-ranked terms the domains that point to your site contain. \r\nThat’s what really matters. \r\n \r\nGet these quality links link to your domain and your rankings will skyrocket! \r\n \r\nWe are introducing this special offer here: \r\nhttps://www.strictlydigital.net/product/semrush-backlinks/ \r\n \r\nIn doubt, or want to know more, chat with us here: \r\nhttps://www.strictlydigital.net/whatsapp-us/ \r\n \r\nSincerely, \r\nMike Jorg Mercier\r\n \r\nstrictlydigital.net \r\nPhone/WhatsApp: +1 (877) 566-3738', NULL, '2026-02-07 07:26:39', 'sembunyi'),
(15, 'Sharma', 'Pendengar Setia', 'Hi,\r\n\r\nI came across your website (radioyuyudogiyai.com) and wanted to check in  if you\'re looking to build a new website or improve your current one, we’d love to help at a very affordable cost.\r\n\r\nAt V Group, we create custom websites tailored to your business  not just pre-built templates. Whether you need small updates or a full redesign, we can support you with:\r\n\r\n• Clean, modern design that’s easy for visitors to use\r\n• Mobile-friendly and loads fast\r\n• Easy to manage (we can build with or without WordPress)\r\n• Secure and optimized for performance\r\n• Accessibility features to ensure your website is usable by all visitors, including those with disabilities\r\n• Ongoing support whenever you need it\r\n\r\nAccessibility is becoming a legal requirement in many countries, and it also improves your site\'s SEO and user experience. We help align your site with WCAG (Web Content Accessibility Guidelines) to make sure it meets today\'s standards.\r\n\r\nWe’ve helped many small businesses and startups get high-quality websites without spending a lot.\r\n\r\nLet me know if you’d like to talk  happy to share examples and a quick plan that fits your budget.\r\n\r\nBest regards,\r\nManshi', NULL, '2026-05-05 00:37:50', 'sembunyi'),
(16, 'WilliamMomEZ', 'Pendengar Setia', 'YyErjcwdkdjwjjwjjdwjddjwsjf ndsaKAqwdweihduncbbwebidaa iudwnishqwuvdwqihbfvweuiojsqjqioqdefiw dwqsqwijbfiewdncbhvdifqhioqsjnqw radioyuyudogiyai.com', NULL, '2026-05-20 05:10:30', 'sembunyi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tim`
--

CREATE TABLE `tim` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL COMMENT 'Nama file foto',
  `bio_singkat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tim`
--

INSERT INTO `tim` (`id`, `nama_lengkap`, `jabatan`, `foto`, `bio_singkat`) VALUES
(1, 'Yohanes Dogomo', 'Kepala Stasiun', 'tim-68a5f048cde4e1.29638933.jpg', 'Bertanggung jawab atas keseluruhan operasional dan program siaran RAYUKOM Dogiyai.'),
(2, 'Maria Tebai', 'Penyiar & Produser Program Pagi', '689227c974979.jpg', 'Menemani pagi pendengar dengan informasi aktual dan musik-musik pilihan.');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `chart_lagu`
--
ALTER TABLE `chart_lagu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `peringkat` (`peringkat`);

--
-- Indeks untuk tabel `forum_balasan`
--
ALTER TABLE `forum_balasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_topik` (`id_topik`);

--
-- Indeks untuk tabel `forum_topik`
--
ALTER TABLE `forum_topik`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesan_kontak`
--
ALTER TABLE `pesan_kontak`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `podcast`
--
ALTER TABLE `podcast`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `profil_website`
--
ALTER TABLE `profil_website`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_pengaturan` (`nama_pengaturan`);

--
-- Indeks untuk tabel `program_unggulan`
--
ALTER TABLE `program_unggulan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tim`
--
ALTER TABLE `tim`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `chart_lagu`
--
ALTER TABLE `chart_lagu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `forum_balasan`
--
ALTER TABLE `forum_balasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `forum_topik`
--
ALTER TABLE `forum_topik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pesan_kontak`
--
ALTER TABLE `pesan_kontak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `podcast`
--
ALTER TABLE `podcast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `profil_website`
--
ALTER TABLE `profil_website`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `program_unggulan`
--
ALTER TABLE `program_unggulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tim`
--
ALTER TABLE `tim`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
