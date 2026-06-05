/**
 * RADIO YUYU DOGIYAI - SCRIPT FINAL
 * Fitur: Live Radio, Podcast, WhatsApp Toggle, Gallery Modal Fix
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // --- ELEMENT SELECTORS ---
    const dateElement = document.getElementById('current-date');
    const timeElement = document.getElementById('current-time');
    
    // Radio & Podcast
    const radioAudio = document.getElementById('radio-stream');
    const playBtn = document.querySelector('.play-btn'); 
    const nowPlayingSpan = document.getElementById('now-playing');
    const listenersSpan = document.getElementById('listeners');
    const canvas = document.getElementById('audio-visualizer');
    
    const podcastContainer = document.getElementById('podcast-player-container');
    const podcastAudio = document.getElementById('podcast-audio-player');
    const podcastTitle = document.getElementById('podcast-title');
    
    // Gallery Modal Elements (Pastikan ada di Footer/Index)
    let imageModal = document.getElementById('image-modal');
    let modalImage = document.getElementById('modal-image');
    let closeModalBtn = document.querySelector('.close-modal-btn');

    // Jika modal belum ada di HTML, buat secara dinamis (Safety Check)
    if (!imageModal) {
        const modalHTML = `
            <div id="image-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:10000; justify-content:center; align-items:center;">
                <button class="close-modal-btn" style="position:absolute; top:20px; right:30px; background:none; border:none; color:white; font-size:40px; cursor:pointer;">&times;</button>
                <img id="modal-image" src="" style="max-width:90%; max-height:80vh; border-radius:8px; box-shadow:0 0 20px rgba(255,255,255,0.2);">
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        imageModal = document.getElementById('image-modal');
        modalImage = document.getElementById('modal-image');
        closeModalBtn = document.querySelector('.close-modal-btn');
    }

    // Vars
    const streamUrl = radioAudio ? radioAudio.src : '';
    let isStreaming = false;
    let statsInterval = null;
    let activePlayButton = null;


    // --- 1. WHATSAPP TOGGLE LOGIC (BARU) ---
    window.toggleWhatsApp = function() {
        const chatBox = document.getElementById('wa-chat-box');
        if (chatBox) {
            chatBox.classList.toggle('active');
        }
    };


    // --- 2. GALLERY MODAL LOGIC (FIX) ---
    // Mencari semua gambar di dalam kartu galeri atau berita yang memiliki class img-fluid/gallery-img
    // Kita gunakan event delegation agar aman jika elemen diload via ajax
    document.body.addEventListener('click', function(e) {
        // Cek apakah yang diklik adalah gambar di dalam galeri
        if (e.target.classList.contains('gallery-img') || e.target.closest('.gallery-img-wrapper')) {
            e.preventDefault(); // Mencegah link pindah halaman
            
            const imgElement = e.target.tagName === 'IMG' ? e.target : e.target.querySelector('img');
            
            if (imgElement) {
                const src = imgElement.src;
                modalImage.src = src;
                imageModal.style.display = 'flex'; // Tampilkan modal
                setTimeout(() => imageModal.classList.add('active'), 10); // Animasi
            }
        }
        
        // Cek jika tombol close modal diklik
        if (e.target.classList.contains('close-modal-btn') || e.target === imageModal) {
            imageModal.style.display = 'none';
            imageModal.classList.remove('active');
            modalImage.src = '';
        }
    });


    // --- 3. RADIO LIVE LOGIC ---
    function fetchStreamStats() {
        fetch('check_stream.php')
            .then(response => response.json())
            .then(data => {
                if (listenersSpan) listenersSpan.innerText = data.listeners;
                if (nowPlayingSpan && isStreaming) {
                    if (data.current_song && data.current_song !== 'Terhubung') {
                        nowPlayingSpan.innerText = data.current_song;
                    } else if (nowPlayingSpan.innerText === 'Menghubungkan...') {
                        nowPlayingSpan.innerText = 'Terhubung';
                    }
                }
                if (data.status === 'offline' && isStreaming) {
                    stopRadio();
                    if(nowPlayingSpan) nowPlayingSpan.innerText = "Radio Offline";
                }
            })
            .catch(err => console.error('Gagal stats:', err));
    }

    window.togglePlay = function() {
        if (!radioAudio) return;
        if (isStreaming) stopRadio();
        else startRadio();
    };

    function startRadio() {
        if (podcastAudio && !podcastAudio.paused) {
            podcastAudio.pause();
            closePodcastPlayer();
        }

        updateLiveUI('loading');
        radioAudio.src = streamUrl; 
        radioAudio.load(); 

        const playPromise = radioAudio.play();
        if (playPromise !== undefined) {
            playPromise.then(_ => {
                isStreaming = true;
                if(nowPlayingSpan) nowPlayingSpan.innerText = 'Terhubung';
                updateLiveUI('playing');
                fetchStreamStats();
                statsInterval = setInterval(fetchStreamStats, 10000);
            }).catch(error => {
                console.error("Stream error:", error);
                alert("Gagal memuat siaran live.");
                stopRadio();
            });
        }
    }

    function stopRadio() {
        radioAudio.pause();
        radioAudio.src = ""; 
        radioAudio.load();
        isStreaming = false;
        updateLiveUI('paused');
        if (statsInterval) { clearInterval(statsInterval); statsInterval = null; }
    }

    function updateLiveUI(state) {
        if (!playBtn) return;
        if (state === 'loading') {
            playBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>MENGHUBUNGKAN...</span>';
        } else if (state === 'playing') {
            playBtn.innerHTML = '<i class="fas fa-pause"></i> <span>JEDA SIARAN</span>';
            playBtn.classList.add('btn-danger'); playBtn.classList.remove('btn-primary');
        } else {
            playBtn.innerHTML = '<i class="fas fa-play"></i> <span>DENGARKAN LIVE</span>';
            playBtn.classList.remove('btn-danger'); playBtn.classList.add('btn-primary');
            if(nowPlayingSpan) nowPlayingSpan.innerText = 'Siaran Terhenti';
            if(listenersSpan) listenersSpan.innerText = '-';
        }
    }


    // --- 4. PODCAST & SONG LOGIC ---
    document.body.addEventListener('click', function(e) {
        const trigger = e.target.closest('.play-trigger');
        if (trigger) {
            e.preventDefault(); e.stopPropagation();
            const audioSrc = trigger.getAttribute('data-audio');
            const title = trigger.getAttribute('data-title');
            if (audioSrc && audioSrc.trim() !== '') playMedia(trigger, audioSrc, title);
            else alert('File audio belum tersedia.');
        }
    });

    function playMedia(buttonElement, src, title) {
        if (isStreaming) stopRadio(); // Stop radio live

        if (activePlayButton === buttonElement) {
            podcastAudio.paused ? (podcastAudio.play(), updateButtonState(buttonElement, 'playing')) : (podcastAudio.pause(), updateButtonState(buttonElement, 'paused'));
            return;
        }

        if (activePlayButton) updateButtonState(activePlayButton, 'default');

        let finalSrc = src;
        if (!src.startsWith('http') && !src.startsWith('admin-radio/')) finalSrc = 'admin-radio/uploads/' + src;
        
        if(podcastAudio) {
            podcastAudio.src = finalSrc;
            if(podcastTitle) podcastTitle.textContent = title;
            if(podcastContainer) podcastContainer.style.display = 'flex';
            
            podcastAudio.play().then(() => {
                updateButtonState(buttonElement, 'playing');
                activePlayButton = buttonElement;
            }).catch(err => { console.error("Podcast error:", err); alert("Gagal memutar audio."); });
        }
    }

    window.closePodcastPlayer = function() {
        if(podcastAudio) { podcastAudio.pause(); podcastAudio.currentTime = 0; }
        if(podcastContainer) podcastContainer.style.display = 'none';
        if (activePlayButton) { updateButtonState(activePlayButton, 'default'); activePlayButton = null; }
    };

    function updateButtonState(btn, state) {
        if (!btn) return;
        const icon = btn.querySelector('i') || btn; 
        if (icon.classList.contains('fa-pause')) icon.classList.replace('fa-pause', 'fa-play');
        if (icon.classList.contains('fa-pause-circle')) icon.classList.replace('fa-pause-circle', 'fa-play-circle');
        
        if (state === 'playing') {
            if(icon.classList.contains('fa-play')) icon.classList.replace('fa-play', 'fa-pause');
            if(icon.classList.contains('fa-play-circle')) icon.classList.replace('fa-play-circle', 'fa-pause-circle');
        } 
    }


    // --- 5. UTILITIES ---
    function drawVisualizer() {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const w = canvas.width, h = canvas.height;
        function animate() {
            ctx.clearRect(0, 0, w, h);
            if (!isStreaming) {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.2)'; ctx.fillRect(0, h/2 - 0.5, w, 1);
            } else {
                const bars = 30, barW = w / bars, gap = 2; 
                ctx.fillStyle = '#ffca28'; 
                for(let i=0; i<bars; i++) {
                    const height = Math.random() * h * 0.7; 
                    ctx.fillRect(i * barW, (h - height) / 2, barW - gap, height);
                }
            }
            requestAnimationFrame(animate);
        }
        animate();
    }
    if(canvas) drawVisualizer();

    function updateDateTime() {
        const now = new Date();
        if (dateElement) dateElement.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        if (timeElement) timeElement.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    const hamburgerBtn = document.getElementById('hamburger-btn');
    const navMenu = document.getElementById('nav-menu');
    if(hamburgerBtn && navMenu) {
        hamburgerBtn.onclick = (e) => { e.preventDefault(); navMenu.classList.toggle('active'); };
    }
});