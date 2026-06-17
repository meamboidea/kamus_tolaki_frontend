<x-public-layout :title="'Kebijakan Privasi — Penerjemah Tolaki'">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('terjemah') }}" wire:navigate
            class="inline-flex items-center gap-1 mb-6 text-sm font-medium text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">
            ← Kembali ke Terjemah
        </a>

        <article class="privasi-konten rounded-2xl bg-white dark:bg-slate-900 p-6 sm:p-10 shadow-sm ring-1 ring-brand-100/50 dark:ring-slate-800">
            <h1>Kebijakan Privasi</h1>
            <p class="meta">Berlaku sejak 1 Januari 2025 · Versi Beta</p>

            <p>Kami menghargai privasi Anda. Kebijakan ini menjelaskan data apa yang kami kumpulkan, bagaimana kami menggunakannya, dan hak Anda atas data tersebut saat menggunakan Penerjemah Tolaki.</p>

            <h2>1. Data yang Kami Kumpulkan</h2>

            <h3>a. Pengguna Publik (tanpa akun)</h3>
            <ul>
                <li><strong>Teks yang diterjemahkan</strong> — dikirim ke mesin terjemah untuk diproses. Tidak disimpan secara permanen dan tidak dikaitkan dengan identitas Anda.</li>
                <li><strong>Data teknis</strong> — alamat IP, jenis browser, dan waktu akses dicatat secara anonim dalam log server untuk keperluan keamanan dan pemantauan performa.</li>
            </ul>

            <h3>b. Pengguna Terdaftar (moderator / admin)</h3>
            <ul>
                <li><strong>Informasi akun</strong> — nama, alamat email, dan kata sandi (dienkripsi dengan bcrypt).</li>
                <li><strong>Foto profil</strong> — opsional, disimpan di server kami jika Anda mengunggahnya.</li>
                <li><strong>Aktivitas moderasi</strong> — riwayat koreksi yang Anda tinjau (setujui/tolak/cabut) disimpan sebagai jejak audit untuk transparansi.</li>
            </ul>

            <h2>2. Cara Kami Menggunakan Data</h2>
            <ul>
                <li>Menjalankan dan meningkatkan layanan terjemahan AI.</li>
                <li>Menyimpan jejak audit moderasi untuk akuntabilitas dan transparansi proses kurasi bahasa.</li>
                <li>Mengirim email terkait akun (reset kata sandi, verifikasi) — tidak ada email pemasaran.</li>
                <li>Memantau keamanan dan mencegah penyalahgunaan layanan.</li>
            </ul>

            <h2>3. Penyimpanan &amp; Keamanan</h2>
            <p>Data disimpan di server yang berlokasi di Indonesia. Kami menerapkan enkripsi HTTPS untuk semua komunikasi, enkripsi bcrypt untuk kata sandi, dan pembatasan akses berbasis peran untuk data sensitif. Meski demikian, tidak ada sistem yang sepenuhnya kebal risiko — kami menyarankan Anda menggunakan kata sandi yang kuat.</p>

            <h2>4. Berbagi Data dengan Pihak Ketiga</h2>
            <p>Kami <strong>tidak menjual</strong> data Anda kepada siapa pun. Data hanya dibagikan kepada:</p>
            <ul>
                <li><strong>Penyedia layanan AI (API publik)</strong> — teks yang Anda masukkan dikirim ke API terjemahan pihak ketiga untuk diproses. Harap tidak memasukkan informasi pribadi atau rahasia dalam kolom terjemah.</li>
                <li><strong>Penyedia infrastruktur server</strong> — untuk keperluan hosting dan penyimpanan data.</li>
                <li><strong>Penegak hukum</strong> — hanya jika diwajibkan oleh hukum yang berlaku di Indonesia.</li>
            </ul>

            <h2>5. Cookie &amp; Penyimpanan Lokal</h2>
            <p>Kami menggunakan:</p>
            <ul>
                <li><strong>Session cookie</strong> — untuk menjaga status login Anda (kadaluarsa saat browser ditutup atau setelah batas waktu sesi).</li>
                <li><strong>localStorage</strong> — untuk menyimpan preferensi tema terang/gelap Anda secara lokal di perangkat. Tidak dikirim ke server.</li>
            </ul>
            <p>Kami tidak menggunakan cookie pelacak atau iklan.</p>

            <h2>6. Hak Anda</h2>
            <p>Sebagai pengguna terdaftar, Anda berhak untuk:</p>
            <ul>
                <li>Mengakses dan memperbarui data profil Anda melalui halaman Profil.</li>
                <li>Meminta penghapusan akun dan data terkait — hubungi kami melalui halaman <a href="{{ route('tentang') }}" wire:navigate class="text-brand-600 dark:text-brand-400 hover:underline">Tentang</a>.</li>
                <li>Meminta salinan data aktivitas moderasi Anda.</li>
            </ul>

            <h2>7. Retensi Data</h2>
            <p>Data akun disimpan selama akun aktif. Jejak audit moderasi disimpan lebih lama untuk keperluan transparansi dan integritas data bahasa. Log server anonim dihapus setelah 90 hari.</p>

            <h2>8. Layanan untuk Anak-Anak</h2>
            <p>Layanan ini tidak ditujukan untuk anak di bawah 13 tahun. Kami tidak secara sengaja mengumpulkan data dari anak-anak.</p>

            <h2>9. Perubahan Kebijakan</h2>
            <p>Kami dapat memperbarui kebijakan ini sewaktu-waktu. Versi terbaru selalu tersedia di halaman ini. Penggunaan layanan secara berkelanjutan setelah pembaruan berarti Anda menyetujui kebijakan yang diperbarui.</p>

            <hr>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pertanyaan terkait privasi? Hubungi kami melalui halaman <a href="{{ route('tentang') }}" wire:navigate class="text-brand-600 dark:text-brand-400 hover:underline">Tentang</a>.</p>
        </article>
    </div>

    <style>
        .privasi-konten { color:#475569; font-size:.95rem; line-height:1.75; }
        .dark .privasi-konten { color:#cbd5e1; }
        .privasi-konten h1 { font-size:1.7rem; font-weight:700; color:#0f172a; margin:0 0 .25rem; font-family:'Playfair Display',serif; }
        .dark .privasi-konten h1 { color:#f8fafc; }
        .privasi-konten .meta { font-size:.8rem; color:#94a3b8; margin-bottom:1.5rem; }
        .privasi-konten h2 { font-size:1.05rem; font-weight:600; color:#0f172a; margin:1.75rem 0 .5rem; }
        .dark .privasi-konten h2 { color:#f1f5f9; }
        .privasi-konten h3 { font-size:.95rem; font-weight:600; color:#334155; margin:1rem 0 .4rem; }
        .dark .privasi-konten h3 { color:#cbd5e1; }
        .privasi-konten p { margin:.6rem 0; }
        .privasi-konten ul { list-style:disc; padding-left:1.4rem; margin:.5rem 0; }
        .privasi-konten li { margin:.3rem 0; }
        .privasi-konten strong { font-weight:600; color:#0f172a; }
        .dark .privasi-konten strong { color:#f1f5f9; }
        .privasi-konten em { font-style:italic; }
        .privasi-konten hr { margin:1.5rem 0; border:0; border-top:1px solid #e2e8f0; }
        .dark .privasi-konten hr { border-top-color:#334155; }
    </style>
</x-public-layout>
