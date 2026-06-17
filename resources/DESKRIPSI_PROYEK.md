# PROFIL PROJECT: Sistem Penerjemah Cerdas Bahasa Tolaki (RAG-Based AI Translator)

Dokumen ini berisi gambaran umum, metodologi, dan arsitektur teknologi dari sistem **Penerjemah Bahasa Tolaki** sebagai bahan informasi untuk klien, mitra, atau kolaborator.

---

## 1. Ringkasan Eksekutif (Executive Summary)

Aplikasi ini adalah platform penerjemah digital dua arah (Indonesia ⇄ Tolaki) berbasis kecerdasan buatan (AI) terintegrasi yang dirancang khusus untuk melestarikan dan mendigitalisasi bahasa daerah Tolaki (Sulawesi Tenggara). Dengan menggabungkan teknologi web modern , mesin API berkecepatan tinggi (**Python & FastAPI**), dan teknologi **Large Language Models (LLM)** kelas dunia (seperti Claude dan Gemini), aplikasi ini menjadi pionir solusi pelestarian bahasa daerah yang akurat, dinamis, dan ramah pengguna.

---

## 2. Latar Belakang & Masalah (Problem Statement)

Bahasa Tolaki dikategorikan sebagai **_low-resource language_** di dunia digital—yaitu bahasa yang memiliki korpus data digital paralel berukuran sangat minim untuk melatih AI secara mandiri. Metode penerjemahan mesin konvensional (seperti _fine-tuning_ model bahasa dari nol) tidak layak secara finansial maupun ketersediaan data.

Platform ini hadir memecahkan kebuntuan tersebut dengan mendigitalisasi database Kamus Master Tolaki fisik (hasil ekstraksi dan validasi terstruktur sebanyak **3.299 entri** dan **5.000+ indeks kata**) menjadi sistem penalaran AI yang cerdas dan siap pakai.

---

## 3. Solusi & Metode Utama (Key Methodologies)

Aplikasi ini tidak sekadar menerjemahkan kata per kata (secara harfiah), melainkan memahami konteks kalimat secara alami melalui beberapa metode ilmiah:

- **RAG (Retrieval-Augmented Generation):**
  Alih-alih melatih ulang model AI (_fine-tuning_) yang memakan biaya besar, sistem menggunakan pendekatan **RAG**. Saat pengguna memasukkan kalimat, mesin pencari pintar (_smart retrieval_) akan langsung menyisir kamus master untuk mencari kosakata Tolaki yang relevan beserta contoh-contoh kalimatnya, lalu menyuapkan informasi tersebut ke dalam prompt LLM. AI kemudian menalar tata bahasa Tolaki yang tepat secara _real-time_.
- **Pivot Translation (Terjemahan Perantara):**
  Sistem menggunakan Bahasa Indonesia sebagai bahasa perantara (_pivot_). Penerjemahan dari bahasa asing (seperti Inggris) akan terlebih dahulu diterjemahkan ke Bahasa Indonesia sebelum diproses ke Bahasa Tolaki untuk menjaga presisi makna kamus.
- **Stemming Morfologi Pintar (Sastrawi):**
  Menggunakan algoritma stemming bahasa Indonesia untuk mencari bentuk kata dasar dari kalimat masukan. Contoh: jika pengguna mengetik _"perumahan"_, sistem otomatis mencari kata dasar _"rumah"_ di indeks kamus Tolaki untuk menjaring padanan kata yang tepat.
- **Mekanisme Pinjam Kata (Loanword Detection):**
  Jika suatu kata modern (seperti _"kulkas"_ atau _"komputer"_) tidak memiliki padanan dalam kamus Tolaki kuno, AI akan meminjam kata tersebut dan memberikan penanda khusus (_serapan_), memberikan transparansi penuh kepada pengguna.
- **Continuous Learning Loop (Umpan Balik Penutur Asli):**
  Sistem dilengkapi dengan fitur **Koreksi Terjemahan** yang dapat diisi oleh pengguna atau penutur asli bahasa Tolaki. Koreksi ini akan dimoderasi oleh admin, dan setelah disetujui, akan menjadi **prioritas utama** dalam prompt RAG berikutnya. Ini membuat sistem terjemahan terus bertambah cerdas secara organik seiring berjalannya waktu.

---

## 4. Arsitektur & Teknologi Sistem (Technology Stack)

Sistem dibangun dengan arsitektur **decoupled/hybrid** yang memisahkan bagian antarmuka pengguna dengan mesin kecerdasan buatan:

1.  **Frontend & Core Web App :**
    Menyediakan portal publik yang interaktif, halaman kamus cepat, modul donasi melestarikan budaya, serta panel moderasi admin yang aman untuk menyetujui usulan koreksi kata dari masyarakat.
2.  **AI Translation Engine API (Python + FastAPI):**
    Layanan API mikro (_microservice_) independen yang bertugas melakukan _retrieval_ database JSON, menyusun prompt dinamis, dan melakukan pemanggilan kecerdasan buatan.
3.  **Neutral LLM Provider Layer (Anthropic Claude & Google Gemini):**
    Integrasi API AI dengan mekanisme **Auto-Failover**. Jika salah satu penyedia AI mengalami gangguan kuota/jaringan, sistem secara otomatis mengalihkan tugas ke penyedia AI cadangan secara instan tanpa mengganggu kenyamanan pengguna.

---

## 5. Keunggulan untuk Mitra Kerja Sama

- **Akurasi Berbasis Bukti (No Hallucination):** AI dibatasi hanya boleh merangkai kalimat berdasarkan kosakata asli yang terdaftar di Kamus Master. AI tidak akan pernah mengarang-ngarang kata Tolaki baru di luar korpus kamus.
- **Dapat Direplikasi (Scalable):** Kerangka kerja (framework) arsitektur ini dapat dengan mudah diadaptasikan untuk digitalisasi dan penerjemahan **bahasa daerah lain di Indonesia** yang juga kekurangan sumber data digital.
- **Keterlibatan Komunitas:** Memadukan kekuatan kecerdasan buatan dengan kearifan lokal penutur asli melalui sistem moderasi komunitas.

---

## 6. Sumber & Referensi

Kamus Tolaki yang menjadi fondasi utama database sistem ini bersumber dari publikasi resmi berikut:

**Kamus Tolaki–Indonesia**
Penerbit: Kementerian Pendidikan Dasar dan Menengah Republik Indonesia
[Unduh PDF (repositori.kemendikdasmen.go.id)](https://repositori.kemendikdasmen.go.id/23855/1/KAMUS%20TOLAKI%20-%20INDONESIA.pdf)

Kamus fisik ini diekstraksi, distrukturisasi, dan divalidasi secara manual menjadi **3.299 entri** dan **5.000+ indeks kata** yang digunakan sebagai korpus RAG.
