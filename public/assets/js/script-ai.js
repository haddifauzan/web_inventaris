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
            <div class="mb-4">
                <h5 class="mb-3">Halo! Saya adalah AI Assistant RHGIS 👋</h5>
                <p class="mb-2">Saya dapat membantu Anda dengan informasi mengenai:</p>
                
                <div class="alert alert-info mb-3">
                <strong><i class="bi bi-info-circle me-2"></i>Catatan:</strong>
                Gunakan perintah <code>/cek</code> sebelum setiap pertanyaan
                </div>

                <div class="topics-list">
                <h6 class="mb-2">Topik yang dapat ditanyakan:</h6>
                <ul class="list-unstyled ms-3">
                    <li><i class="bi bi-check2-circle me-2"></i>Barang inventaris & pengelolaan barang</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Komputer, tablet, dan switch</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Status barang (baru/backup/aktif/pemusnahan)</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Kelayakan barang</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Lokasi barang</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Informasi departemen</li>
                    <li><i class="bi bi-check2-circle me-2"></i>IP Address</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Maintenance dan perawatan switch</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Riwayat, OS, dan kepemilikan</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Tahun perolehan</li>
                    <li><i class="bi bi-check2-circle me-2"></i>Total barang</li>
                </ul>
                </div>

                <p class="mt-3 mb-0">
                <i class="bi bi-chat-dots me-2"></i>Silakan ajukan pertanyaan Anda! Saya juga dapat membantu dengan pertanyaan di luar topik di atas.
                </p>
            </div>
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

    // Event listener untuk tombol scroll ke atas
    document.getElementById('scrollUpButton').addEventListener('click', function() {
        chatContainer.scrollTo({
            top: 0, // Scroll ke paling atas
            behavior: 'smooth'
        });
    });

    // Event listener untuk tombol scroll ke bawah
    document.getElementById('scrollDownButton').addEventListener('click', function() {
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight, // Scroll ke paling bawah
            behavior: 'smooth'
        });
    });
    
});