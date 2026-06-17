<x-public-layout :title="'Syarat & Ketentuan — Penerjemah Tolaki'">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('terjemah') }}" wire:navigate
            class="inline-flex items-center gap-1 mb-6 text-sm font-medium text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">
            ← Kembali ke Terjemah
        </a>

        <article class="syarat-konten rounded-2xl bg-white dark:bg-slate-900 p-6 sm:p-10 shadow-sm ring-1 ring-brand-100/50 dark:ring-slate-800">
            <h1>Syarat &amp; Ketentuan</h1>
            <p class="meta">Berlaku sejak 1 Januari 2025 · Versi Beta</p>

            <p>Dengan mengakses dan menggunakan layanan Penerjemah Tolaki ("Layanan"), Anda menyetujui syarat dan ketentuan berikut. Harap baca dengan saksama.</p>

            <h2>1. Tentang Layanan</h2>
            <p>Penerjemah Tolaki adalah platform digital berbasis AI (RAG — <em>Retrieval-Augmented Generation</em>) yang bertujuan melestarikan dan mendigitalisasi bahasa daerah Tolaki dari Sulawesi Tenggara. Layanan ini bersifat nirlaba dan saat ini tersedia dalam status <strong>Versi Beta</strong>.</p>

            <h2>2. Penggunaan yang Diizinkan</h2>
            <ul>
                <li>Menggunakan fitur terjemah untuk keperluan pribadi, akademik, atau budaya.</li>
                <li>Mengakses Kamus Tolaki sebagai referensi belajar bahasa.</li>
                <li>Mengirimkan koreksi terjemahan untuk membantu meningkatkan akurasi sistem.</li>
            </ul>

            <h2>3. Penggunaan yang Dilarang</h2>
            <ul>
                <li>Menggunakan layanan untuk konten yang bersifat SARA, menyinggung, atau melanggar hukum yang berlaku di Indonesia.</li>
                <li>Melakukan <em>scraping</em>, pengujian beban berlebihan, atau upaya merusak ketersediaan layanan.</li>
                <li>Menyalahgunakan fitur koreksi untuk memasukkan data yang keliru atau menyesatkan.</li>
                <li>Mengklaim kepemilikan atas konten budaya Tolaki yang bersifat kolektif.</li>
            </ul>

            <h2>4. Koreksi &amp; Kontribusi Pengguna</h2>
            <p>Setiap koreksi terjemahan yang Anda kirimkan akan ditinjau oleh moderator sebelum dimasukkan ke dalam sistem. Dengan mengirimkan koreksi, Anda memberikan hak kepada tim kami untuk menggunakan kontribusi tersebut guna meningkatkan kualitas layanan tanpa kewajiban kompensasi.</p>

            <h2>5. Akurasi Terjemahan</h2>
            <p>Layanan ini menggunakan teknologi AI yang terus berkembang. Hasil terjemahan <strong>tidak dijamin 100% akurat</strong> dan tidak boleh digunakan sebagai satu-satunya referensi untuk keperluan hukum, medis, atau akademik formal. Kami terus memperbaiki sistem dengan bantuan komunitas.</p>

            <h2>6. Kekayaan Intelektual</h2>
            <p>Bahasa dan budaya Tolaki adalah warisan bersama masyarakat Tolaki. Konten bahasa dalam database kami bersumber dari khazanah budaya tersebut dan tidak diklaim sebagai milik eksklusif platform ini. Kode sumber, desain antarmuka, dan infrastruktur teknis merupakan karya tim pengembang.</p>

            <h2>7. Batasan Tanggung Jawab</h2>
            <p>Layanan disediakan "sebagaimana adanya" (<em>as-is</em>) tanpa garansi tersurat maupun tersirat. Tim pengembang tidak bertanggung jawab atas kerugian yang timbul akibat penggunaan atau ketidakmampuan menggunakan layanan ini.</p>

            <h2>8. Perubahan Syarat</h2>
            <p>Kami dapat memperbarui syarat ini sewaktu-waktu. Perubahan signifikan akan diumumkan melalui halaman ini. Penggunaan layanan secara berkelanjutan setelah perubahan diterbitkan berarti Anda menyetujui syarat yang diperbarui.</p>

            <h2>9. Hukum yang Berlaku</h2>
            <p>Syarat ini tunduk pada hukum Republik Indonesia. Setiap sengketa diselesaikan secara musyawarah; apabila tidak tercapai kesepakatan, penyelesaian dilakukan melalui jalur hukum yang berlaku.</p>

            <hr>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pertanyaan? Hubungi kami melalui halaman <a href="{{ route('tentang') }}" wire:navigate class="text-brand-600 dark:text-brand-400 hover:underline">Tentang</a>.</p>
        </article>
    </div>

    <style>
        .syarat-konten { color:#475569; font-size:.95rem; line-height:1.75; }
        .dark .syarat-konten { color:#cbd5e1; }
        .syarat-konten h1 { font-size:1.7rem; font-weight:700; color:#0f172a; margin:0 0 .25rem; font-family:'Playfair Display',serif; }
        .dark .syarat-konten h1 { color:#f8fafc; }
        .syarat-konten .meta { font-size:.8rem; color:#94a3b8; margin-bottom:1.5rem; }
        .syarat-konten h2 { font-size:1.05rem; font-weight:600; color:#0f172a; margin:1.75rem 0 .5rem; }
        .dark .syarat-konten h2 { color:#f1f5f9; }
        .syarat-konten p { margin:.6rem 0; }
        .syarat-konten ul { list-style:disc; padding-left:1.4rem; margin:.5rem 0; }
        .syarat-konten li { margin:.3rem 0; }
        .syarat-konten strong { font-weight:600; color:#0f172a; }
        .dark .syarat-konten strong { color:#f1f5f9; }
        .syarat-konten em { font-style:italic; }
        .syarat-konten hr { margin:1.5rem 0; border:0; border-top:1px solid #e2e8f0; }
        .dark .syarat-konten hr { border-top-color:#334155; }
    </style>
</x-public-layout>
