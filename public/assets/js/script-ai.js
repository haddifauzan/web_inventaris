document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const chatContainer = document.getElementById('chatContainer');
    const userMessageInput = document.getElementById('userMessage');
    const typingIndicator = document.getElementById('typingIndicator');
    const refreshButton = document.getElementById('refreshButton');
    
    // Ambil percakapan dari localStorage jika ada
    let conversation = JSON.parse(localStorage.getItem('chatConversation')) || [
        { role: 'assistant', content: 'Saya akan menjawabnya dengan senang hati🤩' }
    ];

    // Inisialisasi Bootstrap Offcanvas
    const chatOffcanvas = document.getElementById('chatOffcanvas');
    chatOffcanvas.addEventListener('shown.bs.offcanvas', function () {
        userMessageInput.focus();
    });

    // Tampilkan percakapan yang ada
    conversation.forEach(msg => addMessageToChat(msg.role, msg.content));

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const userMessage = userMessageInput.value.trim();
        if (!userMessage) return;

        // Tambahkan pesan user ke chat
        addMessageToChat('user', userMessage);
        userMessageInput.value = '';
        userMessageInput.disabled = true;

        // Tampilkan indikator mengetik
        showTypingIndicator();
        
        // Kirim pesan ke server
        sendMessage(userMessage);
    });

    // Fungsi untuk menampilkan indikator mengetik
    function showTypingIndicator() {
        typingIndicator.classList.add('active');
        setTimeout(() => {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }, 100);
    }

    // Fungsi untuk menyembunyikan indikator mengetik
    function hideTypingIndicator() {
        typingIndicator.classList.remove('active');
    }

    // Fungsi untuk menambahkan pesan ke chat
    function addMessageToChat(role, content) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('chat-message', 'animate__animated', 'animate__fadeIn');
        
        if (role === 'user') {
            messageDiv.classList.add('user-message');
        } else {
            messageDiv.classList.add('bot-message');
        }
        
        messageDiv.textContent = content;
        chatContainer.appendChild(messageDiv);
        
        // Scroll ke pesan terbaru dengan animasi smooth
        setTimeout(() => {
            chatContainer.scrollTo({
                top: chatContainer.scrollHeight,
                behavior: 'smooth'
            });
        }, 100);
    }

    // Fungsi untuk mengirim pesan ke server
    function sendMessage(message) {
        fetch('/chatbot/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message,
                conversation: conversation
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Sembunyikan indikator mengetik
                hideTypingIndicator();
                
                // Tambahkan respons AI ke chat dengan sedikit delay untuk efek natural
                setTimeout(() => {
                    addMessageToChat('assistant', data.message);
                    
                    // Update conversation history
                    conversation = data.conversation;
                    
                    // Simpan percakapan ke localStorage
                    localStorage.setItem('chatConversation', JSON.stringify(conversation));
                    
                    // Aktifkan kembali input
                    userMessageInput.disabled = false;
                    userMessageInput.focus();
                }, 500);
            } else {
                console.error('Error:', data.error);
                hideTypingIndicator();
                addMessageToChat('assistant', 'Maaf, terjadi kesalahan. Silakan coba lagi.');
                userMessageInput.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideTypingIndicator();
            addMessageToChat('assistant', 'Maaf, terjadi kesalahan jaringan. Silakan coba lagi.');
            userMessageInput.disabled = false;
        });
    }

    // Fungsi untuk menyegarkan percakapan
    function refreshConversation() {
        chatContainer.innerHTML = ''; // Hapus semua pesan
        conversation = []; // Reset percakapan
        const welcomeMessage = `<div class="welcome-message">
            <p>Halo! Saya adalah AI Assistant. Saya dapat membantu Anda untuk memberikan informasi dengan perintah berikut:</p>
            <ul>
                <li>barang inventaris / pengelolaan barang</li>
                <li>komputer / tablet / switch</li>
                <li>baru / backup / aktif/ pemusnahan</li>
                <li>kelayakan</li>
                <li>barang aktif / barang backup/ pemusnahan</li>
                <li>lokasi barang / lokasi</li>
                <li>departemen</li>
                <li>ip / ip address</li>
                <li>maintenance switch / perawatan</li>
                <li>riwayat / os / kepemilikan</li>
                <li>tahun perolehan</li>
                <li>total barang</li>
            </ul>
            <p>Silakan ajukan pertanyaan Anda!</p>
            <p>Anda juga dapat mengajukan pertanyaan lain diluar informasi tersebut!!</p>
        </div>`;

        // Modifikasi fungsi addMessageToChat untuk menangani HTML
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('chat-message', 'animate__animated', 'animate__fadeIn', 'bot-message');
        messageDiv.innerHTML = welcomeMessage;
        chatContainer.appendChild(messageDiv);
        
        localStorage.removeItem('chatConversation'); // Hapus percakapan dari localStorage
    }

    // Event listener untuk tombol refresh
    refreshButton.addEventListener('click', refreshConversation);
});