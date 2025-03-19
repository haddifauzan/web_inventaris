@extends('admin.layouts.master')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container py-5">
        <h1>Halaman Utama</h1>
        <p>Ini adalah konten halaman utama Anda. Klik tombol chat di pojok kanan bawah untuk memulai percakapan dengan chatbot AI.</p>
    </div>

    <!-- Tombol untuk membuka chatbot -->
    <button class="btn btn-primary chat-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#chatOffcanvas">
        <i class="bi bi-chat-dots"></i>
    </button>

    <!-- Offcanvas Chatbot -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="chatOffcanvas" aria-labelledby="chatOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="chatOffcanvasLabel">
                <i class="bi bi-robot me-2"></i> AI Assistant
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <div class="chat-messages flex-grow-1 p-3" id="chatContainer">
                <div class="chat-message bot-message animate__animated animate__fadeIn">
                    Halo! Saya adalah AI Assistant. Apa yang bisa saya bantu hari ini?
                </div>
            </div>
            
            <div class="typing-indicator px-4 py-2" id="typingIndicator">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span class="ms-2">AI sedang mengetik</span>
            </div>
            
            <div class="chat-input-area p-3 border-top">
                <form id="chatForm">
                    <div class="input-group">
                        <input type="text" id="userMessage" class="form-control border-end-0" placeholder="Ketik pesan Anda..." required>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chatForm');
            const chatContainer = document.getElementById('chatContainer');
            const userMessageInput = document.getElementById('userMessage');
            const typingIndicator = document.getElementById('typingIndicator');
            let conversation = [
                { role: 'assistant', content: 'Halo! Saya adalah AI Assistant. Apa yang bisa saya bantu hari ini?' }
            ];

            // Inatialize Bootstrap Offcanvas
            const chatOffcanvas = document.getElementById('chatOffcanvas');
            chatOffcanvas.addEventListener('shown.bs.offcanvas', function () {
                userMessageInput.focus();
            });

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

            function showTypingIndicator() {
                typingIndicator.classList.add('active');
                setTimeout(() => {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }, 100);
            }

            function hideTypingIndicator() {
                typingIndicator.classList.remove('active');
            }

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

            function sendMessage(message) {
                fetch('{{ route('chatbot.send') }}', {
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
        });
    </script>

<style>
    /* Layout utama */
    .offcanvas-body {
        position: relative;
    }
    
    .chat-messages {
        height: calc(100% - 70px);
        overflow-y: auto;
        background-color: #f8f9fa;
    }
    
    .chat-input-area {
        background-color: #fff;
        position: sticky;
        bottom: 0;
        width: 100%;
    }
    
    /* Style pesan */
    .chat-message {
        margin-bottom: 15px;
        padding: 12px 16px;
        border-radius: 18px;
        max-width: 80%;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        position: relative;
        transition: all 0.3s ease;
    }
    
    .user-message {
        background-color: #f0f2f5;
        color: #333;
        margin-left: auto;
        border-bottom-right-radius: 5px;
    }
    
    .bot-message {
        background-color: #0d6efd;
        color: white;
        margin-right: auto;
        border-bottom-left-radius: 5px;
    }
    
    /* Tombol chat */
    .chat-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 999;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    
    .chat-btn:hover {
        transform: scale(1.05);
    }
    
    .chat-btn i {
        font-size: 1.5rem;
    }
    
    /* Indikator mengetik */
    .typing-indicator {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }
    
    .typing-indicator.active {
        opacity: 1;
        visibility: visible;
    }
    
    .typing-dots {
        display: inline-flex;
        align-items: center;
    }
    
    .typing-dots span {
        height: 8px;
        width: 8px;
        margin: 0 1px;
        background-color: #6c757d;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.6;
    }
    
    .typing-dots span:nth-child(1) {
        animation: pulse 1.5s infinite;
    }
    
    .typing-dots span:nth-child(2) {
        animation: pulse 1.5s infinite 0.3s;
    }
    
    .typing-dots span:nth-child(3) {
        animation: pulse 1.5s infinite 0.6s;
    }
    
    /* Animasi */
    @keyframes pulse {
        0% {
            opacity: 0.4;
            transform: scale(1);
        }
        50% {
            opacity: 1;
            transform: scale(1.2);
        }
        100% {
            opacity: 0.4;
            transform: scale(1);
        }
    }
    
    /* Animasi Fade */
    .animate__animated {
        animation-duration: 0.5s;
    }
    
    .animate__fadeIn {
        animation-name: fadeIn;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Style untuk input */
    #chatForm .form-control {
        border-radius: 20px 0 0 20px;
        padding: 10px 16px;
        height: auto;
    }
    
    #chatForm .btn {
        border-radius: 0 20px 20px 0;
        padding: 10px 16px;
    }
    
    /* Ubah warna scrollbar */
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }
    
    .chat-messages::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 10px;
    }
    
    .chat-messages::-webkit-scrollbar-track {
        background-color: rgba(0,0,0,0.05);
    }
</style>
@endsection