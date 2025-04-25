const { makeWASocket, useMultiFileAuthState, jidNormalizedUser } = require('@whiskeysockets/baileys');
const axios = require('axios');

const repliedSenders = new Map(); // Menyimpan senderNumber dan tanggal balasan
const invalidSenders = new Set(); // Menyimpan senderNumber yang sudah menerima pesan error
const sentWarning = new Set(); // Menyimpan senderNumber yang sudah menerima pesan "Coba lagi besok"

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('./auth_info');
    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: true,
    });

    sock.ev.on('creds.update', saveCreds);
    sock.ev.on('messages.upsert', async (m) => {
        const msg = m.messages[0];
        if (!msg.message || msg.key.fromMe) return;

        let sender = msg.key.remoteJid;
        if (sender.includes('@g.us')) return; // Abaikan pesan grup

        // Simpan nomor tanpa @s.whatsapp.net
        const senderNumber = sender.replace('@s.whatsapp.net', '');
        const messageText = msg.message.conversation || msg.message.extendedTextMessage?.text || '';

        // Ambil tanggal hari ini dalam format YYYY-MM-DD
        const today = new Date().toISOString().split('T')[0];

        // Cek apakah senderNumber sudah pernah mendapat balasan hari ini
        if (repliedSenders.has(senderNumber)) {
            const lastRepliedDate = repliedSenders.get(senderNumber);
            if (lastRepliedDate === today) {
                if (!sentWarning.has(senderNumber)) { // Kirim sekali saja per hari
                    sentWarning.add(senderNumber);
                    const validJid = jidNormalizedUser(sender);
                    await sock.sendMessage(validJid, { text: 'Anda sudah mengirimkan laporan hari ini. Coba lagi besok' });
                    console.log(`⚠️ Pengirim ${senderNumber} sudah mendapat balasan hari ini.`);
                }
                return;
            }
        }

        // **Parsing Format Pesan**
        const parsedMessage = parseComplaintMessage(messageText);
        if (!parsedMessage) {
            if (!invalidSenders.has(senderNumber)) { // Kirim error hanya jika belum pernah dikirim
                const validJid = jidNormalizedUser(sender);
                await sock.sendMessage(validJid, { text: 'Format pesan salah! Gunakan:\n\nNama : Nama Anda\nKepada : Unit Tujuan\nLaporan : Isi Laporan' });
                console.log(`❌ Format pesan salah dari ${senderNumber}, pesan kesalahan dikirim.`);
                invalidSenders.add(senderNumber); // Tandai bahwa pengirim sudah menerima pesan error
            } else {
                console.log(`⚠️ Pengirim ${senderNumber} mengirim format salah lagi, tetapi tidak dikirim ulang pesan error.`);
            }
            return;
        }

        // **Simpan ke Database**
        try {
            const response = await axios.post('http://127.0.0.1:8000/messages', {
                nama_pelapor: parsedMessage.nama, // Nama User
                nomor_pelapor: senderNumber, // Nomor WA
                unit_tujuan: parsedMessage.unit_tujuan, // 1 untuk UPT TIK, 2 untuk TEFA
                complaint_text: parsedMessage.laporan, // Laporan User
                status: 'pending', // Set status sebagai pending
                pending: new Date().toISOString(), // Waktu sekarang
            });

            console.log('✅ Laporan berhasil disimpan ke Laravel!', response.data);
        } catch (error) {
            if (error.response) {
                console.error('❌ Gagal menyimpan Laporan ke Laravel:', error.response.data);
            } else {
                console.error('❌ Error:', error.message);
            }
            return;
        }

        // **Kirim Balasan**
        const validJid = jidNormalizedUser(sender);
        await sock.sendMessage(validJid, { text: 'Laporan anda sudah masuk, segera kami proses' });

        // Tandai sender sudah mendapat balasan hari ini
        repliedSenders.set(senderNumber, today);
        console.log(`📩 Balasan dikirim ke ${senderNumber}`);

        // Hapus dari daftar invalidSenders jika sebelumnya mengirim format salah
        invalidSenders.delete(senderNumber);
        sentWarning.delete(senderNumber); // Reset peringatan untuk keesokan harinya
    });
}

connectToWhatsApp();

/**
 * **Parsing Pesan Format Laporan**
 * @param {string} text
 * @returns {Object|null}
 */
function parseComplaintMessage(text) {
    const lines = text.split('\n');
    let nama = '', unit = '', laporan = '';

    lines.forEach(line => {
        const [key, value] = line.split(':').map(s => s.trim());
        if (!key || !value) return;
        
        const keyLower = key.toLowerCase(); // Pastikan key diubah ke lowercase untuk konsistensi

        if (keyLower === 'nama') nama = value;
        if (keyLower === 'kepada') unit = value.toLowerCase();
        if (keyLower === 'laporan') laporan = value;
    });

    if (!nama || !unit || !laporan) return null;

    // Mapping Unit Tujuan
    let unit_tujuan = 0;
    const unitLower = unit.toLowerCase(); // Konversi input ke lowercase

    if (unitLower.includes('upt tik')) unit_tujuan = 1;
    if (unitLower.includes('tefa')) unit_tujuan = 2;

    if (unit_tujuan === 0) return null; // Jika unit tidak dikenali

    return { nama, unit_tujuan, laporan };
}
