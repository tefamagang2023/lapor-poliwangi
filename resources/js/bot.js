const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const mysql = require('mysql2');
const path = require('path');
const fs = require('fs');

// Koneksi ke database
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '', // isi sesuai
    database: 'lapor', // ganti dengan nama DB kamu
    timezone: '+07:00'
});

const client = new Client({
    authStrategy: new LocalAuth()
});

// Ketika QR code dihasilkan, tampilkan di terminal
client.on('qr', (qr) => {
    console.log('Scan QR ini dengan WhatsApp Web:');
    qrcode.generate(qr, { small: true });
});

// Ketika client siap digunakan
client.on('ready', () => {
    console.log('✅ Bot siap digunakan!');
});

// Ketika menerima pesan baru
client.on('message', async msg => {
    // Abaikan pesan yang berasal dari grup
    if (msg.from.includes('@g.us')) {
        console.log('❌ Pesan dari grup diabaikan.');
        return;
    }

    // Ambil isi pesan dan nomor pengirim
    const messageText = msg.body;
    const nomorPengirim = msg.from.split('@')[0];



    // =============================
    // 1. CEK JIKA PESAN ADALAH JAWABAN
    // =============================
    if (messageText.toLowerCase().startsWith("jawaban dari saya adalah")) {
        // Ambil isi jawaban setelah teks "Jawaban dari saya adalah"
        const balasan = messageText.split('Jawaban dari saya adalah')[1]?.trim().replace(/^"|"$/g, '');
    
        // Jika jawaban kosong atau format salah
        if (!balasan) {
            msg.reply("⚠️ Format jawaban tidak lengkap. Pastikan kamu menulis seperti ini:\n\nJawaban dari saya adalah\n\"Isi jawaban kamu\"");
            return;
        }
    
        // Cari complaint yang sesuai dari database
        db.query(
            'SELECT id FROM complaints WHERE nomor_pelapor = ? AND reply_text IS NOT NULL AND date_reply_pelapor IS NULL ORDER BY created_at DESC LIMIT 1',
            [nomorPengirim],
            (err, results) => {
                if (err || results.length === 0) {
                    console.error('❌ Tidak ditemukan complaint yang cocok atau terjadi error:', err);
                    msg.reply("⚠️ Tidak ditemukan laporan yang menunggu balasan dari kamu.");
                    return;
                }
    
                const complaintId = results[0].id;
                const now = new Date();
    
                // Update complaint dengan jawaban dari pelapor
                db.query(
                    'UPDATE complaints SET complaint_text = ?, date_reply_pelapor = ? WHERE id = ?',
                    [balasan, now, complaintId],
                    (err) => {
                        if (err) {
                            console.error('❌ Gagal menyimpan jawaban:', err);
                            msg.reply("⚠️ Terjadi kesalahan saat menyimpan jawaban kamu.");
                            return;
                        }
    
                        console.log(`✅ Jawaban dari ${nomorPengirim} disimpan untuk complaint ID ${complaintId}`);
                        msg.reply("✅ Terima kasih, jawaban kamu sudah berhasil kami simpan!");
                    }
                );
            }
        );
    
        return; // Hentikan proses setelah jawaban ditangani
    }
    

    // =============================
    // 2. CEK JIKA PESAN ADALAH LAPORAN BARU
    // =============================
    if (messageText.toLowerCase().includes("nama :") &&
    messageText.toLowerCase().includes("kepada :") &&
    messageText.toLowerCase().includes("laporan :")) {

    // Memecah pesan menjadi beberapa baris
    const lines = messageText.split('\n');
    let nama = "", unitNama = "", complaint_text = "";

    // Ekstrak informasi dari masing-masing baris
    lines.forEach(line => {
        if (line.toLowerCase().startsWith("nama")) {
            nama = line.split(':')[1]?.trim();
        } else if (line.toLowerCase().startsWith("kepada")) {
            unitNama = line.split(':')[1]?.trim();
        } else if (line.toLowerCase().startsWith("laporan")) {
            complaint_text = line.split(':')[1]?.trim();
        }
    });

    if (nama && unitNama && complaint_text) {
        const today = new Date();
        const tanggalHariIni = today.toISOString().split('T')[0];

        // Cek apakah pengirim sudah membuat laporan hari ini
        db.query(
            'SELECT COUNT(*) AS total FROM complaints WHERE nomor_pelapor = ? AND DATE(created_at) = ?',
            [nomorPengirim, tanggalHariIni],
            async (err, results) => {
                if (err) {
                    console.error('❌ Gagal cek laporan hari ini:', err);
                    msg.reply("⚠️ Terjadi kesalahan saat memproses laporan kamu.");
                    return;
                }

                if (results[0].total > 0) {
                    msg.reply("⚠️ Kamu sudah mengirim laporan hari ini. Silakan kirim kembali besok.");
                    return;
                }

                // Cari ID unit tujuan
                db.query(
                    'SELECT id FROM units WHERE LOWER(nama) = ?',
                    [unitNama.toLowerCase()],
                    async (err, results) => {
                        if (err || results.length === 0) {
                            console.error('❌ Gagal mendapatkan unit_id:', err);
                            msg.reply("⚠️ Unit tidak ditemukan di sistem. Mohon pastikan nama unit sesuai.");
                            return;
                        }

                        const unitId = results[0].id;
                        const now = new Date();
                        let fileNama = null;

                        // Cek apakah ada media (gambar) yang dikirim bersama laporan
                        if (msg.hasMedia) {
                            try {
                                const media = await msg.downloadMedia();
                                if (!media || !media.data) {
                                    console.error("❌ Media kosong atau tidak bisa diunduh");
                                    msg.reply("⚠️ Gagal mendownload media.");
                                    return;
                                }

                                // Validasi file gambar
                                const mimeType = media.mimetype.split('/')[1];
                                const validImageTypes = ['jpeg', 'png', 'jpg'];
                                if (!validImageTypes.includes(mimeType)) {
                                    msg.reply("⚠️ Hanya gambar (JPG, PNG, JPEG) yang diperbolehkan.");
                                    return;
                                }

                                const fileName = `${Date.now()}_${msg.id.id}.jpg`;
                                fileNama = fileName;
                                const filePath = path.join(__dirname, '../../public/media', fileName);
                                fs.writeFileSync(filePath, media.data, 'base64');
                                console.log(`📷 Gambar disimpan di ${filePath}`);
                            } catch (mediaErr) {
                                console.error("❌ Gagal simpan media:", mediaErr);
                                msg.reply("⚠️ Terjadi kesalahan saat menyimpan gambar.");
                                return;
                            }
                        }

                        // Simpan laporan ke database
                        db.query(
                            'INSERT INTO complaints (user_id, nama_pelapor, unit_id, complaint_text, nomor_pelapor, file_path, pending, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            [1, nama, unitId, complaint_text, nomorPengirim, fileNama, now, now, now],
                            (err) => {
                                if (err) {
                                    console.error('❌ Gagal simpan laporan:', err);
                                    msg.reply("⚠️ Terjadi kesalahan saat menyimpan laporan kamu.");
                                    return;
                                }

                                msg.reply("✅ Laporan kamu sudah kami terima, terima kasih!");
                                console.log(`📩 Laporan dari ${nama} (${nomorPengirim}) disimpan.`);
                            }
                        );
                    }
                );
            }
        );
    } else {
        msg.reply("⚠️ Format tidak lengkap. Mohon isi semua bagian: Nama, Kepada, dan Laporan.");
    }

    return;
}



    // =============================
    // 3. CEK JIKA PESAN ADALAH JAWABAN
    // =============================

    if (messageText.toLowerCase().includes("nomor pelapor :") &&
    messageText.toLowerCase().includes("nama pelapor :") &&
    messageText.toLowerCase().includes("laporan :") &&
    messageText.toLowerCase().includes("balasan :")) {

    // Pecah pesan menjadi baris-baris
    const lines = messageText.split('\n');
    let nomor = "", nama = "", laporan = "", balasan = "";

    // Ambil isi masing-masing bagian
    lines.forEach(line => {
        const lower = line.trim().toLowerCase();
        if (lower.startsWith("nomor pelapor")) {
            nomor = line.split(':')[1]?.trim();
        } else if (lower.startsWith("nama pelapor")) {
            nama = line.split(':')[1]?.trim();
        } else if (lower.startsWith("laporan")) {
            laporan = line.split(':')[1]?.trim();
        } else if (lower.startsWith("balasan")) {
            balasan = line.split(':')[1]?.trim();
        }
    });

    // Validasi: Pastikan semua data terisi
    if (!nomor || !nama || !laporan || !balasan) {
        msg.reply("⚠️ Format tidak lengkap. Pastikan semua bagian terisi: Nomor, Nama, Laporan, Balasan.");
        return;
    }

    // Cari laporan (complaint) yang cocok
    db.query(
        'SELECT id, unit_id, status FROM complaints WHERE nomor_pelapor = ? AND nama_pelapor = ? AND complaint_text = ? ORDER BY created_at DESC LIMIT 1',
        [nomor, nama, laporan],
        (err, results) => {
            if (err || results.length === 0) {
                console.error('❌ Tidak ditemukan complaint:', err);
                msg.reply("⚠️ Tidak ditemukan laporan yang sesuai.");
                return;
            }

            const complaint = results[0];

            if (complaint.status === 'completed') {
                msg.reply("⚠️ Laporan ini sudah dibalas sebelumnya.");
                return;
            }

            const now = new Date();

            // Simpan balasan ke tabel responses
            db.query(
                'INSERT INTO responses (complaint_id, unit_id, response_text, sent_at, status, reviewed_at) VALUES (?, ?, ?, ?, ?, ?)',
                [complaint.id, complaint.unit_id, balasan, now, 0, now],
                (err2) => {
                    if (err2) {
                        console.error('❌ Gagal menyimpan balasan:', err2);
                        msg.reply("⚠️ Terjadi kesalahan saat menyimpan balasan.");
                        return;
                    }

                    // Update status laporan menjadi 'completed'
                    db.query(
                        'UPDATE complaints SET status = ?, completed_at = ? WHERE id = ?',
                        ['completed', now, complaint.id],
                        (err3) => {
                            if (err3) {
                                console.error('❌ Gagal update complaint:', err3);
                                msg.reply("⚠️ Balasan tersimpan, tapi gagal update status complaint.");
                                return;
                            }

                            console.log(`✅ Balasan disimpan & complaint #${complaint.id} ditandai selesai.`);
                            msg.reply("✅ Terima kasih, balasan kamu sudah disimpan dan laporan ditandai selesai.");
                        }
                    );
                }
            );
        }
    );

    return;
}

    // =============================
    // 4. BUKAN JAWABAN DAN BUKAN LAPORAN
    // =============================
    msg.reply("📄 Format laporan salah. Gunakan format berikut:\n\nNama : Nama Lengkap\nKepada : Nama Unit\nLaporan : Isi laporan");
});
// jika format yang dituliskan pelapor bukan laporan dan jawaban


client.initialize();

setInterval(() => {
    // Ambil data dari tabel responses yang status = 0
    db.query(
        `SELECT responses.id, responses.response_text, complaints.nama_pelapor, complaints.nomor_pelapor, complaints.complaint_text, units.nama AS unit_nama
         FROM responses
         JOIN complaints ON responses.complaint_id = complaints.id
         JOIN units ON responses.unit_id = units.id
         WHERE responses.status = 0
         ORDER BY responses.created_at ASC
         LIMIT 5`, // Batas 5 data per eksekusi (biar ringan)
        async (err, results) => {
            if (err) {
                console.error('❌ Gagal ambil data dari responses:', err);
                return;
            }

            if (results.length === 0) {
                console.log('⏳ Tidak ada balasan baru yang perlu dikirim.');
                return;
            }

            for (const response of results) {
                const nomor = response.nomor_pelapor + '@c.us';
                const pesan =
                    `Halo ${response.nama_pelapor},\n` +
                    `Saya admin LAPOR Poliwangi mengirim konfirmasi balasan dari laporan Anda:\n` +
                    `"${response.complaint_text}"\n\n` +
                    `Laporan tersebut sudah dibalas oleh unit ${response.unit_nama}.\n` +
                    `Berikut balasannya:\n` +
                    `"${response.response_text}"\n\n` +
                    `Terima kasih telah melapor 🙏`;

                    // format laporan balasan
                try {
                    await client.sendMessage(nomor, pesan);
                    console.log(`✅ Balasan otomatis dikirim ke ${nomor}`);

                    // Update status menjadi 1 (sudah dikirim)
                    db.query('UPDATE responses SET status = "1" WHERE id = ?', [response.id], (updateErr) => {
                        if (updateErr) {
                            console.error(`❌ Gagal update status response ID ${response.id}:`, updateErr);
                        }
                    });
                } catch (sendErr) {
                    console.error(`❌ Gagal kirim pesan ke ${nomor}:`, sendErr);
                }
            }
        }
    );
}, 10000); // Cek setiap 10 detik


    const express = require('express');
    const bodyParser = require('body-parser');
    const cors = require('cors');

    const app = express();

    // Middleware setup
    app.use(cors());
    app.use(bodyParser.json());

    // Endpoint API untuk kirim pesan WhatsApp
    app.post('/send-message', async (req, res) => {
        // Ambil nomor dan pesan dari body request
        const { nomor, pesan } = req.body;

        // Validasi input: Pastikan nomor dan pesan terisi
        if (!nomor || !pesan) {
            return res.status(400).send({ success: false, error: 'Nomor dan pesan wajib diisi.' });
        }

        const chatId = nomor + '@c.us'; // Format chat ID

        try {
            await client.sendMessage(chatId, pesan);
            console.log(`📤 Pesan terkirim ke ${nomor}`);
            return res.send({ success: true, pesan: 'Pesan berhasil dikirim.' });
        } catch (error) {
            console.error('❌ Gagal kirim pesan:', error);
            return res.status(500).send({ success: false, error: error.pesan });
        }
    });

    // Endpoint API untuk kirim pesan WhatsApp (admin ke pengguna)
    app.post('/send-whatsapp', async (req, res) => {
        const { nomor, pesan } = req.body;

        // Validasi input
        if (!nomor || !pesan) {
            return res.status(400).send({ success: false, error: 'Nomor dan pesan wajib diisi.' });
        }

        const chatId = nomor + '@c.us';  // Format chat ID

        try {
            await client.sendMessage(chatId, pesan);
            console.log(`📤 Pesan terkirim ke ${nomor}`);
            return res.send({ success: true, pesan: 'Pesan berhasil dikirim.' });
        } catch (error) {
            console.error('❌ Gagal kirim pesan:', error);
            return res.status(500).send({ success: false, error: error.pesan });
        }
    });

    // Endpoint API untuk kirim gambar berdasarkan ID complaint
    app.post('/send-image/:id', (req, res) => {
        const complaintId = req.params.id; // Ambil ID complaint dari parameter URL
        const { nomor, pesan } = req.body;

        // Validasi input
        if (!nomor || !pesan) {
            return res.status(400).send({ success: false, message: 'Nomor dan pesan wajib diisi.' });
        }

        // Cek apakah data complaint tersedia di database
        db.query('SELECT * FROM complaints WHERE id = ?', [complaintId], async (err, results) => {
            if (err) return res.status(500).send({ success: false, message: 'Database error', error: err });
            if (results.length === 0) return res.status(404).send({ success: false, message: 'Data tidak ditemukan' });

            const complaint = results[0];
            const chatId = nomor + '@c.us'; // Format chat ID

            // Cek apakah ada gambar yang terkait dengan complaint
            if (!complaint.file_path) {
                return res.status(404).send({ success: false, message: 'Tidak ada gambar untuk dikirim' });
            }

            try {
                // Ambil path gambar dari file yang sudah disimpan
                const filePath = path.join(__dirname, '../../public/media', complaint.file_path);
                const media = MessageMedia.fromFilePath(filePath);

                // Kirim gambar ke nomor tujuan
                await client.sendMessage(chatId, media, { caption: pesan });
                res.send({ success: true, message: 'Gambar berhasil dikirim!' });
            } catch (error) {
                console.error('❌ Gagal kirim gambar:', error);
                res.status(500).send({ success: false, message: 'Gagal mengirim gambar', error: error.message });
            }
        });
    });



// Jalankan server
const PORT = 3000;  // Mendefinisikan port yang digunakan oleh server
app.listen(PORT, () => {
    // Menjalankan server Express pada port yang telah ditentukan
    console.log(`🌐 API WhatsApp aktif di http://localhost:${PORT}`);
});


