<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecteur Vidéo HLS - Formation</title>
    
    <!-- Video.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.6.1/video-js.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #fff;
            min-height: 100vh;
        }

        .player-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .video-wrapper {
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
        }

        .video-js {
            width: 100%;
            height: auto;
            aspect-ratio: 16/9;
        }

        .video-js .vjs-big-play-button {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            line-height: 80px;
            border: none;
            background: rgba(102, 126, 234, 0.9);
            font-size: 2em;
        }

        .video-js .vjs-big-play-button:hover {
            background: rgba(102, 126, 234, 1);
        }

        .controls-panel {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .controls-panel h2 {
            margin-bottom: 20px;
            color: #667eea;
            font-size: 1.3em;
        }

        .quality-selector {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .quality-btn {
            padding: 10px 20px;
            border: 2px solid #667eea;
            background: transparent;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .quality-btn:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .quality-btn.active {
            background: #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
        }

        .quality-btn.auto {
            border-color: #28a745;
        }

        .quality-btn.auto.active {
            background: #28a745;
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
        }

        .info-section {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #999;
            font-weight: 500;
        }

        .info-value {
            color: #fff;
            font-weight: 600;
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-indicator.loading {
            background: #ffc107;
            animation: pulse 1.5s infinite;
        }

        .status-indicator.playing {
            background: #28a745;
        }

        .status-indicator.error {
            background: #dc3545;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .alert-info {
            background: rgba(33, 150, 243, 0.2);
            border-left: 4px solid #2196F3;
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-left: 4px solid #28a745;
        }

        .playlist {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 10px;
            padding: 25px;
        }

        .playlist h3 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .playlist-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .playlist-item:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateX(5px);
        }

        .playlist-item.active {
            background: rgba(102, 126, 234, 0.3);
            border-left: 4px solid #667eea;
        }

        .playlist-item-number {
            background: #667eea;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .playlist-item-info {
            flex: 1;
        }

        .playlist-item-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .playlist-item-duration {
            color: #999;
            font-size: 0.9em;
        }

        .debug-panel {
            background: #1a1a2e;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .debug-panel h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-family: 'Segoe UI', sans-serif;
        }

        .debug-log {
            background: #000;
            padding: 15px;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            color: #0f0;
        }

        .debug-log-entry {
            padding: 5px 0;
            border-bottom: 1px solid rgba(0, 255, 0, 0.1);
        }

        .debug-log-entry:last-child {
            border-bottom: none;
        }

        .debug-log-time {
            color: #999;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .player-container {
                padding: 10px;
            }

            .quality-selector {
                justify-content: center;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="player-container">
        <!-- Alert Section -->
        <div id="alert-container"></div>

        <!-- Video Player -->
        <div class="video-wrapper">
            <video id="hls-player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" crossorigin="use-credentials">
                <p class="vjs-no-js">
                    Pour regarder cette vidéo, veuillez activer JavaScript et considérer la mise à jour vers un navigateur web qui
                    <a href="https://videojs.com/html5-video-support/" target="_blank">supporte la vidéo HTML5</a>
                </p>
            </video>
        </div>

        <!-- Controls Panel -->
        <div class="controls-panel">
            <h2>🎯 Sélection de qualité</h2>
            <div class="quality-selector" id="quality-selector">
                <button class="quality-btn auto active" data-quality="auto">
                    ⚡ Auto
                </button>
                <!-- Les autres boutons de qualité seront ajoutés dynamiquement -->
            </div>

            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-indicator loading" id="status-indicator"></span>
                        <span id="status-text">Chargement...</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Qualité actuelle:</span>
                    <span class="info-value" id="current-quality">Auto</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Débit:</span>
                    <span class="info-value" id="current-bitrate">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Résolution:</span>
                    <span class="info-value" id="current-resolution">-</span>
                </div>
            </div>
        </div>

        <!-- Playlist -->
        <div class="playlist" style="display: none;">
            <h3>📚 Liste des vidéos</h3>
            <div id="playlist-container">
                <!-- Les items de playlist seront ajoutés ici -->
            </div>
        </div>

        <!-- Debug Panel -->
        <div class="debug-panel">
            <h3>🐛 Console de débogage</h3>
            <div class="debug-log" id="debug-log"></div>
        </div>
    </div>

    <!-- Video.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.6.1/video.min.js"></script>
    <!-- Video.js HLS Quality Selector -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-quality-levels/4.0.0/videojs-contrib-quality-levels.min.js"></script>
    
    <script>
        // Configuration depuis Laravel
        const CONFIG = {
            cloudFrontDomain: '{{ $cloudFrontDomain }}',
            // Chemin de la vidéo passé depuis le controller
            videoPath: '{{ $testFiles[0] ?? '' }}'
        };

        let player;
        let qualityLevels;

        // Fonction pour logger dans le debug panel
        function log(message, type = 'info') {
            const logContainer = document.getElementById('debug-log');
            const time = new Date().toLocaleTimeString();
            const entry = document.createElement('div');
            entry.className = 'debug-log-entry';
            entry.innerHTML = `<span class="debug-log-time">[${time}]</span>${message}`;
            logContainer.appendChild(entry);
            logContainer.scrollTop = logContainer.scrollHeight;
            console.log(message);
        }

        // Fonction pour afficher une alerte
        function showAlert(message, type = 'info') {
            const container = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            container.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        // Fonction pour mettre à jour le status
        function updateStatus(text, state = 'loading') {
            document.getElementById('status-text').textContent = text;
            const indicator = document.getElementById('status-indicator');
            indicator.className = `status-indicator ${state}`;
        }

        // Initialiser le lecteur vidéo
        function initPlayer() {
            log('Initialisation du lecteur vidéo...');

            player = videojs('hls-player', {
                html5: {
                    vhs: {
                        overrideNative: true,
                        enableLowInitialPlaylist: true,
                        withCredentials: true
                    },
                    nativeAudioTracks: false,
                    nativeVideoTracks: false
                },
                controls: true,
                fluid: true,
                preload: 'auto'
            });

            // Initialiser les niveaux de qualité
            qualityLevels = player.qualityLevels();

            // Écouter les changements de qualité
            qualityLevels.on('addqualitylevel', function(event) {
                log(`Niveau de qualité ajouté: ${event.qualityLevel.height}p`);
                updateQualitySelector();
            });

            qualityLevels.on('change', function() {
                updateCurrentQualityDisplay();
            });

            // Événements du lecteur
            player.on('loadstart', function() {
                log('Début du chargement de la vidéo');
                updateStatus('Chargement...', 'loading');
            });

            player.on('canplay', function() {
                log('Vidéo prête à être lue');
                updateStatus('Prêt', 'playing');
                showAlert('Vidéo chargée avec succès', 'success');
            });

            player.on('play', function() {
                log('Lecture démarrée');
                updateStatus('Lecture en cours', 'playing');
            });

            player.on('pause', function() {
                log('Lecture en pause');
                updateStatus('En pause', 'loading');
            });

            player.on('error', function(error) {
                const err = player.error();
                log(`Erreur: ${err.message}`, 'error');
                updateStatus('Erreur', 'error');
                showAlert(`Erreur de lecture: ${err.message}`, 'error');
            });

            player.on('progress', function() {
                updateBitrateDisplay();
            });

            // Charger la vidéo
            loadVideo();
        }

        // Charger la vidéo
        function loadVideo() {
            const videoUrl = `https://${CONFIG.cloudFrontDomain}/${CONFIG.videoPath}`;

            log(`Chargement de la vidéo`);
            log(`URL: ${videoUrl}`);

            player.src({
                src: videoUrl,
                type: 'application/x-mpegURL'
            });
        }

        // Mettre à jour le sélecteur de qualité
        function updateQualitySelector() {
            const container = document.getElementById('quality-selector');
            
            // Garder seulement le bouton Auto
            container.innerHTML = '<button class="quality-btn auto active" data-quality="auto">⚡ Auto</button>';

            // Ajouter les boutons pour chaque niveau de qualité
            for (let i = 0; i < qualityLevels.length; i++) {
                const quality = qualityLevels[i];
                const btn = document.createElement('button');
                btn.className = 'quality-btn';
                btn.dataset.quality = i;
                btn.textContent = `${quality.height}p`;
                
                btn.addEventListener('click', function() {
                    setQuality(i);
                });

                container.appendChild(btn);
            }
        }

        // Définir la qualité
        function setQuality(index) {
            if (index === 'auto') {
                // Mode auto
                for (let i = 0; i < qualityLevels.length; i++) {
                    qualityLevels[i].enabled = true;
                }
                log('Mode Auto activé');
            } else {
                // Qualité spécifique
                for (let i = 0; i < qualityLevels.length; i++) {
                    qualityLevels[i].enabled = (i === index);
                }
                log(`Qualité fixée à ${qualityLevels[index].height}p`);
            }

            // Mettre à jour l'UI
            document.querySelectorAll('.quality-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-quality="${index}"]`).classList.add('active');

            updateCurrentQualityDisplay();
        }

        // Mettre à jour l'affichage de la qualité actuelle
        function updateCurrentQualityDisplay() {
            for (let i = 0; i < qualityLevels.length; i++) {
                if (qualityLevels[i].enabled) {
                    const isAuto = qualityLevels.length === qualityLevels.selectedIndex || 
                                   Array.from(qualityLevels).every(q => q.enabled);
                    
                    if (isAuto) {
                        const current = qualityLevels[qualityLevels.selectedIndex];
                        document.getElementById('current-quality').textContent = `Auto (${current.height}p)`;
                        document.getElementById('current-resolution').textContent = `${current.width}x${current.height}`;
                    } else {
                        const quality = qualityLevels[i];
                        document.getElementById('current-quality').textContent = `${quality.height}p`;
                        document.getElementById('current-resolution').textContent = `${quality.width}x${quality.height}`;
                    }
                    break;
                }
            }
        }

        // Mettre à jour l'affichage du débit
        function updateBitrateDisplay() {
            if (qualityLevels.selectedIndex >= 0) {
                const quality = qualityLevels[qualityLevels.selectedIndex];
                const bitrate = (quality.bitrate / 1000000).toFixed(2);
                document.getElementById('current-bitrate').textContent = `${bitrate} Mbps`;
            }
        }

        // Event listener pour le bouton Auto
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.quality-btn.auto').addEventListener('click', function() {
                setQuality('auto');
            });
        });

        // Initialiser quand la page est chargée
        window.addEventListener('load', function() {
            log('Application chargée');
            
            // Vérifier les cookies CloudFront
            const cookies = document.cookie.split(';');
            const cfCookies = cookies.filter(c => c.trim().startsWith('CloudFront-'));
            
            if (cfCookies.length === 3) {
                log('✅ Cookies CloudFront détectés');
                showAlert('Cookies CloudFront détectés - Authentification OK', 'success');
            } else {
                log('⚠️ Cookies CloudFront manquants');
                showAlert('Attention: Cookies CloudFront manquants', 'error');
            }

            // Initialiser le lecteur
            initPlayer();
        });
    </script>
</body>
</html>