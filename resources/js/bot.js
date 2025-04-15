const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const mysql = require('mysql2');

// Koneksi ke database
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '', // isi sesuai
    database: 'lapor_poliwangi' // ganti dengan nama DB kamu
});

const client = new Client({
    authStrategy: new LocalAuth()
});

client.on('qr', (qr) => {
    console.log('Scan QR ini dengan WhatsApp Web:');
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => {
    console.log('✅ Bot siap digunakan!');
});

client.on('message', async msg => {
    // Abaikan pesan dari grup
    if (msg.from.includes('@g.us')) {
        console.log('❌ Pesan dari grup diabaikan.');
        return;
    }

    const messageText = msg.body;
    const nomorPengirim = msg.from.split('@')[0];

    if (messageText.toLowerCase().includes("nama :") &&
        messageText.toLowerCase().includes("kepada :") &&
        messageText.toLowerCase().includes("laporan :")) {

        const lines = messageText.split('\n');
        let nama = "", unitNama = "", complaint_text = "";

        lines.forEach(line => {
            if (line.toLowerCase().startsWith("nama")) {
                nama = line.split(':')[1]?.trim();
            } else if (line.toLowerCase().startsWith("kepada")) {
                unitNama = line.split(':')[1]?.trim();
            } else if (line.toLowerCase().startsWith("laporan")) {
                complaint_text = line.split(':')[1]?.trim();
            }
        });

        // Validasi data awal
        if (nama && unitNama && complaint_text) {
            // Ambil ID unit dari nama unit
            db.query(
                'SELECT id FROM units WHERE LOWER(nama) = ?',
                [unitNama.toLowerCase()],
                (err, results) => {
                    if (err || results.length === 0) {
                        console.error('❌ Gagal mendapatkan unit_id:', err);
                        msg.reply("⚠️ Unit tidak ditemukan di sistem. Mohon pastikan nama unit sesuai.");
                        return;
                    }

                    const unitId = results[0].id;

                    // Simpan laporan ke tabel complaint
                    db.query(
                        'INSERT INTO complaints (user_id, nama_pelapor, unit_id, complaint_text, nomor_pelapor, pending, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                        [1, nama, unitId, complaint_text, nomorPengirim, now, now, now],
                        (err, results) => {
                            if (err) {
                                console.error('❌ Gagal simpan:', err);
                                msg.reply("Terjadi kesalahan saat menyimpan laporan kamu.");
                                return;
                            }

                            console.log(`📩 Laporan dari ${nama} (${nomorPengirim}) disimpan.`);
                            msg.reply("✅ Laporan kamu sudah kami terima, terima kasih!");
                        }
                    );
                }
            );
        } else {
            msg.reply("⚠️ Format tidak lengkap. Mohon isi semua bagian: Nama, Kepada, dan Laporan.");
        }

    } else {
        msg.reply("📄 Format laporan salah. Gunakan format berikut:\n\nNama : Nama Lengkap\nKepada : Nama Unit\nLaporan : Isi laporan");
    }
});

client.initialize();
