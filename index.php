<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WargaIndo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Tambahkan CSS sesuai kebutuhan */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2a2a72, #009ffd);
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .container {
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }
        .players-list {
            max-height: 200px;
            overflow-y: auto;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SAMP Server</h1>
        <p id="samp-name">Loading...</p>
        <p>Players: <span id="samp-players">0</span>/<span id="samp-max-players">0</span></p>
        <p>Map: <span id="samp-map">N/A</span></p>
    </div>

    <div class="container">
        <h1>FiveM Server</h1>
        <p id="fivem-name">Loading...</p>
        <p>Players: <span id="fivem-players">0</span>/<span id="fivem-max-players">0</span></p>
        <p>Map: <span id="fivem-map">N/A</span></p>
        <h3>Pemain Online:</h3>
        <ul id="fivem-online-players" class="players-list"></ul>
    </div>

    <script>
        async function fetchServerData() {
            try {
                const response = await fetch('api.php');
                const data = await response.json();

                // Update SAMP info
                document.getElementById('samp-name').textContent = data.samp.name;
                document.getElementById('samp-players').textContent = data.samp.players;
                document.getElementById('samp-max-players').textContent = data.samp.max_players;
                document.getElementById('samp-map').textContent = data.samp.map;

                // Update FiveM info
                document.getElementById('fivem-name').textContent = data.fivem.name;
                document.getElementById('fivem-players').textContent = data.fivem.players;
                document.getElementById('fivem-max-players').textContent = data.fivem.max_players;
                document.getElementById('fivem-map').textContent = data.fivem.map;

                // Update FiveM players list
                const playersList = document.getElementById('fivem-online-players');
                playersList.innerHTML = ''; // Clear existing list
                if (data.fivem.online_players.length > 0) {
                    data.fivem.online_players.forEach(player => {
                        const li = document.createElement('li');
                        li.textContent = `${player.name} - ID: ${player.id ?? 'N/A'}`;
                        playersList.appendChild(li);
                    });
                } else {
                    playersList.innerHTML = '<li>Tidak ada pemain online saat ini.</li>';
                }
            } catch (error) {
                console.error('Error fetching server data:', error);
            }
        }

        // Fetch data every 5 seconds
        setInterval(fetchServerData, 5000);
        fetchServerData(); // Fetch immediately on page load
    </script>
</body>
</html>
