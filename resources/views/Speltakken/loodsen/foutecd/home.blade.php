@extends('layouts.loodsen')

@section('content')
    <div class="flunky-background">
        <div class="py-4 container col-md-11">
            <h1 class="retro-title">Foute CD Speler</h1>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('loodsen') }}">Loodsen</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Foute CD Speler</li>
                </ol>
            </nav>

            <div id="album-covers" class="album-covers"></div>

            <div class="player-controls">
                <div class="car-radio">
                    <div class="marquee-container">
                    <div class="radio-display marquee-text" id="radioDisplay">No dsic</div>
                    </div>
                    <div class="radio-buttons">
                        <button id="restartButton" class="radio-button"><span
                                class="material-symbols-rounded">replay</span></button>
                        <button id="prevButton" class="radio-button"><span class="material-symbols-rounded">skip_previous</span>
                        </button>
                        <button id="playPauseButton" class="radio-button"><span class="material-symbols-rounded">play_arrow</span>
                        </button>
                        <button id="nextButton" class="radio-button"><span
                                class="material-symbols-rounded">skip_next</span></button>
                        <button id="listButton" class="radio-button"><span class="material-symbols-rounded">menu</span>
                        </button>
                        <button id="repeatButton" class="radio-button repeat-button"><span
                                class="material-symbols-rounded">repeat</span></button>
                        <button id="shuffleButton" class="radio-button shuffle-button"><span
                                class="material-symbols-rounded">shuffle</span></button>
                    </div>
                </div>
                <audio id="audioPlayer"></audio>
                <audio id="loadingSound" src="{{ asset('foutecd/files/cd_loading.mp3') }}"></audio>

                <div id="tracklist" class="retro-player d-none mt-5">
                    <p>Selecteer een album hierboven</p>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('load', init);

            const audioPlayer = document.getElementById('audioPlayer');
            const loadingSound = document.getElementById('loadingSound');
            const retroPlayer = document.getElementById('tracklist');
            const radioDisplay = document.getElementById('radioDisplay');
            const playPauseButton = document.getElementById('playPauseButton');
            const prevButton = document.getElementById('prevButton');
            const nextButton = document.getElementById('nextButton');
            const listButton = document.getElementById('listButton');
            const restartButton = document.getElementById('restartButton');
            const repeatButton = document.getElementById('repeatButton');
            const shuffleButton = document.getElementById('shuffleButton');

            let allAlbums = []; // Store all albums data
            let allSongs = []; // Flat list of all songs with album info for 'All' shuffle
            let allAlbumElements = [];
            let currentAlbumIndex = -1; // Index of the currently selected album
            let currentSongs = []; // The currently active playlist (can be album or all songs, shuffled or not)
            let currentIndex = 0; // Current index in the currentSongs array
            let isPlayingLoadingSound = false;

            // Repeat Modes: 0 = Off, 1 = Album, 2 = Song
            let repeatMode = 1; // Default to Album repeat

            // Shuffle Modes: 0 = Off, 1 = Album, 2 = All
            let shuffleMode = 0; // Default to Shuffle Off

            let originalAlbumTracklist = []; // Songs of the current album in original order
            let shuffledAlbumTracklist = []; // Songs of the current album in shuffled order
            let shuffledAllTracklist = []; // All songs in shuffled order

            let playbackHistory = []; // To track playback order in shuffle modes
            let historyIndex = -1; // Current position in playbackHistory

            const RepeatIcons = ['repeat', 'repeat', 'repeat_one'];
            const RepeatTitles = ['Repeat uit', 'Repeat Album', 'Repeat Nummer'];
            const ShuffleIcons = ['shuffle', 'shuffle', 'shuffle_on'];
            const ShuffleTitles = ['Shuffle uit', 'Shuffle Album', 'Shuffle Alles'];

            let currentSongTitle = '';
            let scrollAnimation; // Store animation globally or in closure

            const coversContainer = document.getElementById('album-covers');
            const tracklistContainer = document.getElementById('tracklist');


            function init() {
                console.log('Player initializing...');
                fetch('{{ asset('foutecd/albums.json') }}')
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP error ' + res.status);
                        return res.json();
                    })
                    .then(albums => {
                        allAlbums = albums;
                        // Populate allSongs list
                        allAlbums.forEach(album => {
                            album.songs.forEach(song => {
                                allSongs.push({
                                    ...song,
                                    album: album.album,
                                    year: album.year
                                });
                            });
                        });
                        renderAlbums(allAlbums);
                        updateRepeatButton();
                        updateShuffleButton();
                        console.log('Albums loaded.');
                    })
                    .catch(err => {
                        retroPlayer.innerHTML = '<p style="color:red">Kon albums niet laden.</p>';
                        console.error('Error loading albums.json:', err);
                    });

                // Add event listeners for custom controls
                playPauseButton.addEventListener('click', togglePlayPause);
                prevButton.addEventListener('click', playPreviousSong);
                nextButton.addEventListener('click', playNextSong);
                listButton.addEventListener('click', showList);
                restartButton.addEventListener('click', replay);
                repeatButton.addEventListener('click', toggleRepeatMode);
                shuffleButton.addEventListener('click', toggleShuffleMode);


                audioPlayer.addEventListener('play', () => {
                    playPauseButton.innerHTML = '<span class="material-symbols-rounded">pause</span>';
                });

                audioPlayer.addEventListener('pause', () => {
                    playPauseButton.innerHTML = '<span class="material-symbols-rounded">play_arrow</span>';
                });

                audioPlayer.addEventListener('ended', () => {
                    playNextSong();
                });

                loadingSound.addEventListener('ended', () => {
                    isPlayingLoadingSound = false;
                    // The callback from playLoadingSound will handle starting the main audio
                });
            }

            function playLoadingSound(callback, type) {
                if (isPlayingLoadingSound) {
                    return;
                }
                isPlayingLoadingSound = true;

                if (type === 'album') {
                    loadingSound.src = "{{ asset('foutecd/files/cd_open.mp3') }}";
                }
                if (type === 'song') {
                    loadingSound.src = "{{ asset('foutecd/files/cd_loading.mp3') }}";
                }

                updateRadioDisplay('Reading disc', ''); // Clear display temporarily
                audioPlayer.pause();
                playPauseButton.innerHTML = '<span class="material-symbols-rounded">play_arrow</span>';

                loadingSound.currentTime = 0;
                loadingSound.play().then(() => {
                    loadingSound.onended = () => {
                        loadingSound.onended = null;
                        isPlayingLoadingSound = false;
                        if (callback && typeof callback === 'function') {
                            callback();
                        }
                    };
                }).catch(error => {
                    console.error("Error playing loading sound:", error);
                    isPlayingLoadingSound = false;
                    if (callback && typeof callback === 'function') {
                        callback();
                    }
                });
            }

            function showList() {
                retroPlayer.classList.toggle('d-none');
            }

            function replay() {
                // Only replay the current song if there is one loaded
                if (currentSongs.length > 0 && currentIndex !== -1) {
                    // In non-shuffle mode or at the start of history, just play the current song from the beginning
                    playSong(currentIndex, false); // false to not add to history again
                }
            }


            function togglePlayPause() {
                if (isPlayingLoadingSound) {
                    return;
                }

                if (audioPlayer.paused) {
                    if (currentSongs.length > 0 && currentIndex !== -1) { // Prevent playing if no album is selected or no song selected
                        audioPlayer.play().catch(error => {
                            console.error("Error playing main audio:", error);
                            // Handle play error
                        });
                    }
                } else {
                    audioPlayer.pause();
                }
            }

            function playPreviousSong() {
                if (isPlayingLoadingSound || currentSongs.length === 0 || currentIndex === -1) {
                    return;
                }

                loadingSound.pause();
                isPlayingLoadingSound = false;

                // Always navigate in the currentSongs array
                let prevIndex;

                if (currentIndex > 0) {
                    prevIndex = currentIndex - 1;
                    playSong(prevIndex, true);
                } else {
                    // At the beginning of the list
                    if (repeatMode === 1) { // Repeat Album/All
                        prevIndex = currentSongs.length - 1; // Loop to the last song
                        playLoadingSound(() => playSong(prevIndex, true), 'song');
                    } else { // Repeat Off
                        console.log('Beginning of current list. Repeat Off. Moving to previous album.');
                        // Move to the next album
                        const nextAlbumIndex = (currentAlbumIndex - 1) % allAlbums.length;
                        handleAlbumClick(nextAlbumIndex, allAlbumElements[nextAlbumIndex], 'last'); // handleAlbumClick will load and play the first song of the next album
                        return; // Stop further processing in this function

                    }
                }

                 // Play the previous song and add to history
            }

            function playNextSong() {
                if (isPlayingLoadingSound || currentSongs.length === 0 || currentIndex === -1) {
                    return;
                }

                loadingSound.pause();
                isPlayingLoadingSound = false;

                if (repeatMode === 2) { // Repeat Song
                    playSong(currentIndex, false); // Pass false to avoid adding to history again
                    return;
                }

                let nextIndex;

                // Always navigate in the currentSongs array based on the current index
                if (currentIndex < currentSongs.length - 1) {
                    nextIndex = currentIndex + 1;
                    playSong(nextIndex, true);
                } else {
                    // End of the current list (shuffled or not)
                    if (repeatMode === 1) { // Repeat Album/All
                        console.log('End of current list, repeating.');
                        nextIndex = 0; // Loop back to the first song in the current list
                        playLoadingSound(() => playSong(nextIndex, true), 'song');
                    } else { // Repeat Off (repeatMode 0)
                        if (shuffleMode === 2) { // If in Shuffle All mode and Repeat Off, stop at the end
                            console.log('End of all songs in shuffle all. Repeat Off. Stopping playback.');
                            audioPlayer.pause();
                            updateRadioDisplay('Stopped', '');
                            playPauseButton.innerHTML = '<span class="material-symbols-rounded">play_arrow</span>';
                            currentIndex = -1; // Indicate no song is currently selected
                            return; // Stop further processing
                        } else { // If in Shuffle Album or Shuffle Off and Repeat Off, move to next album
                            console.log('End of current list. Repeat Off. Moving to next album.');
                            // Move to the next album
                            const nextAlbumIndex = (currentAlbumIndex + 1) % allAlbums.length;
                            handleAlbumClick(nextAlbumIndex, allAlbumElements[nextAlbumIndex]); // handleAlbumClick will load and play the first song of the next album
                            return; // Stop further processing in this function
                        }
                    }
                }
            }


            function playSong(index, addToHistory = true) {
                const song = currentSongs[index];
                if (!song) {
                    audioPlayer.pause();
                    updateRadioDisplay('Error', '');
                    currentIndex = -1; // Indicate no song is currently selected
                    return;
                }

                currentIndex = index;

                // History is primarily for navigating backward in shuffle mode if needed,
                // but for linear progression (shuffle off, or next/prev clicks in shuffle),
                // we just update the currentIndex in the currentSongs array.
                // We can simplify history management or remove it if not strictly needed for the desired behavior.
                // For now, let's keep a simple history for potential future use or complex navigation requirements,
                // but the primary navigation on skip will use currentIndex.

                if (addToHistory && shuffleMode !== 0) {
                    // Add to history, clearing future history if navigating manually
                    playbackHistory = playbackHistory.slice(0, historyIndex + 1);
                    playbackHistory.push(currentIndex);
                    historyIndex = playbackHistory.length - 1;
                } else if (shuffleMode === 0) {
                    // In non-shuffle mode, clear history when a new song is played directly
                    playbackHistory = [];
                    historyIndex = -1;
                }


                audioPlayer.src = encodeURI(song.filename);
                audioPlayer.play().catch(error => {
                    console.error("Error playing main audio:", error);
                    // Handle play error
                });

                updateRadioDisplay(song.album, song.title);

                document.querySelectorAll('.song').forEach(el => el.classList.remove('active'));
                // Find the song element corresponding to the current song in the rendered tracklist
                const songElements = retroPlayer.querySelectorAll('li.song');
                // We need to find the li element whose text content matches the current song title and album (approximately)
                currentSongTitle = song.title;
                const currentAlbumTitle = song.album;

                for (let i = 0; i < songElements.length; i++) {
                    // A simple text match might be sufficient, or we could store song data on the li elements
                    // For simplicity, let's check if the list item text contains the song title.
                    // This might have false positives with very similar titles.
                    // A more robust solution would involve adding data attributes to the li elements during rendering.
                    if (songElements[i].textContent.includes(currentSongTitle)) {
                        // Further check if album matches if possible, or rely on the fact that tracklist is for the current album/all songs
                        // Let's rely on the tracklist context for now.
                        songElements[i].classList.add('active');
                        // Optional: Scroll the active song into view if the tracklist is scrollable
                        songElements[i].scrollIntoView({behavior: 'smooth', block: 'nearest'});
                        break;
                    }
                }

                // Remove active class from all albums
                allAlbumElements.forEach((albumEl) => {
                    albumEl.classList.remove('active-album');
                });

                // Find the index of the album that contains the song
                    let currentAlbumIndex = allAlbums.findIndex(album =>
                    album.songs.some(song => song.title === currentSongTitle)
                );

                if (currentAlbumIndex !== -1) {
                    const targetElement = allAlbumElements[currentAlbumIndex];
                    targetElement.classList.add('active-album');

                    // Manually scroll the albumScroller to center the target
                    const scrollerRect = coversContainer.getBoundingClientRect();
                    const targetRect = targetElement.getBoundingClientRect();
                    const scrollLeft = coversContainer.scrollLeft + (targetRect.left - scrollerRect.left) - (scrollerRect.width / 2) + (targetRect.width / 2);

                    coversContainer.scrollTo({
                        left: scrollLeft,
                        behavior: 'smooth'
                    });
                }

            }


            function renderAlbums(albums) {
                coversContainer.innerHTML = '';
                // Initial tracklist display
                renderTracklist(null);


                albums.forEach((album, index) => {
                    let albumElement;

                    if (album.image) {
                        const div = document.createElement('div')
                        const p = document.createElement('p')
                        p.innerHTML = album.collection;
                        p.classList.add('collection')
                        div.appendChild(p)

                        const img = document.createElement('img');
                        img.src = album.image;
                        img.alt = album.album;
                        img.className = 'album-cover';

                        div.appendChild(img)
                        albumElement = div;
                    } else {
                        const div = document.createElement('div')
                        const p = document.createElement('p')
                        p.innerHTML = album.collection;
                        p.classList.add('collection')
                        div.appendChild(p)

                        const divEmpty = document.createElement('div');
                        divEmpty.textContent = album.album;
                        divEmpty.className = 'album-cover album-placeholder';

                        div.appendChild(divEmpty)
                        albumElement = div;
                    }

                    albumElement.addEventListener('click', () => {
                        handleAlbumClick(index, albumElement);
                    });

                    coversContainer.appendChild(albumElement);

                    allAlbumElements.push(albumElement)
                });
            }

            function handleAlbumClick(albumIndex, albumElement, type) {
                if (albumIndex < 0 || albumIndex >= allAlbums.length) return;

                allAlbumElements.forEach((album) => {
                    album.classList.remove('active-album')
                })

                albumElement.classList.add('active-album')

                currentAlbumIndex = albumIndex;
                originalAlbumTracklist = allAlbums[currentAlbumIndex].songs;

                // Reset shuffle history and current index when a new album is selected
                playbackHistory = [];
                historyIndex = -1;


                rebuildCurrentSongs(); // Build the currentSongs list based on the new album and current shuffle mode
                renderTracklist(allAlbums[currentAlbumIndex]); // Render the tracklist for the selected album
                // Only play the first song if shuffle mode is off or album shuffle is active
                // If shuffle all is active, selecting an album doesn't change the playing list immediately.
                if (shuffleMode === 0 || shuffleMode === 1) {
                    currentIndex = 0; // Start at the beginning of the new list (of the selected album)
                    if (type === "last") {
                        playLoadingSound(() => playSong(currentSongs.length - 1), 'album'); // Play the first song of the new list
                    } else {
                        playLoadingSound(() => playSong(0), 'album'); // Play the first song of the new list
                    }
                } else {
                    // If in shuffle all mode, update the tracklist display but don't change the playing song
                    updateRadioDisplay(allAlbums[currentAlbumIndex].album, 'Disc inserted');
                }
            }

            function renderTracklist(album) {
                const tracklistContainer = document.getElementById('tracklist');
                tracklistContainer.innerHTML = '';

                if (!album && currentAlbumIndex === -1 && shuffleMode !== 2) { // Show default message if no album selected and not in shuffle all
                    return;
                }

                const albumData = album || (currentAlbumIndex !== -1 ? allAlbums[currentAlbumIndex] : null);

                if (shuffleMode === 2) { // If in Shuffle All mode, show a general title
                    const title = document.createElement('h2');
                    title.textContent = 'Alle nummers (Shuffle)';
                    tracklistContainer.appendChild(title);
                } else if (albumData) { // If an album is selected (and not in Shuffle All), show album title
                    const albumTitle = document.createElement('h2');
                    albumTitle.textContent = `${albumData.album} (${albumData.year})`;
                    tracklistContainer.appendChild(albumTitle);
                }


                const ul = document.createElement('ol');
                // Display the songs from the currentSongs array in their current order
                currentSongs.forEach((song, index) => {
                    const li = document.createElement('li');
                    li.classList.add('song');
                    li.textContent = `${song.title}`;
                    if (shuffleMode === 2 && song.album) { // Add album info in Shuffle All mode
                        li.textContent += ` - ${song.album}`;
                    }
                    li.addEventListener('click', () => {
                        // Play selected song directly, clear history after this point in shuffle mode
                        if (!isPlayingLoadingSound) {
                            playSong(index, true);
                        }
                    });
                    ul.appendChild(li);
                });

                tracklistContainer.appendChild(ul);

                // Highlight the currently playing song if one is selected
                if (currentSongs.length > 0 && currentIndex !== -1 && currentIndex < currentSongs.length) {
                    const songElements = tracklistContainer.querySelectorAll('li.song');
                    // Find the list item that corresponds to the current song
                    const currentSong = currentSongs[currentIndex];
                    for (let i = 0; i < songElements.length; i++) {
                        if (songElements[i].textContent.includes(currentSong.title)) {
                            // Add more robust matching if titles can be similar
                            if (shuffleMode === 2 && songElements[i].textContent.includes(currentSong.album)) {
                                songElements[i].scrollIntoView({behavior: 'smooth', block: 'nearest'});
                                break;
                            } else if (shuffleMode !== 2) {
                                songElements[i].scrollIntoView({behavior: 'smooth', block: 'nearest'});
                                break;
                            }
                        }

                    }

                    currentIndex = currentSongs.findIndex(song => song.title === currentSongTitle);

                    for (let i = 0; i < songElements.length; i++) {
                        if (songElements[i].textContent.includes(currentSongTitle)) {
                            songElements[i].classList.add('active');
                            break;
                        }
                    }
                }

            }


            function updateRadioDisplay(albumName, trackTitle) {
                let display = '';
                if (shuffleMode === 2) {
                    display = 'Shuffle All';
                    if (trackTitle) {
                        display += ` - ${trackTitle}`;
                    }
                    if (albumName && trackTitle) {
                        display += ` (${albumName})`;
                    }
                } else {
                    if (albumName) {
                        display = albumName;
                        if (trackTitle) {
                            display += ` - ${trackTitle}`;
                        }
                    } else if (trackTitle) {
                        display = trackTitle;
                    } else {
                        display = 'No disc...';
                    }
                }
                radioDisplay.textContent = display;

                scrollText()
            }


            function toggleRepeatMode() {
                repeatMode = (repeatMode + 1) % 3; // Cycle through 0, 1, 2
                updateRepeatButton();
                console.log('Repeat mode:', RepeatTitles[repeatMode]);
            }

            function updateRepeatButton() {
                repeatButton.innerHTML = `<span class="material-symbols-rounded">${RepeatIcons[repeatMode]}</span>`;
                repeatButton.title = RepeatTitles[repeatMode];
                // Optional: Add a visual indicator for the active repeat mode
                repeatButton.classList.remove('active');
                if (repeatMode !== 0) {
                    repeatButton.classList.add('active');
                }
            }

            function toggleShuffleMode() {
                const wasShuffleOff = shuffleMode === 0;
                shuffleMode = (shuffleMode + 1) % 3; // Cycle through 0, 1, 2
                updateShuffleButton();
                console.log('Shuffle mode:', ShuffleTitles[shuffleMode]);

                // If a song is playing or an album is selected, rebuild the currentSongs list based on the new shuffle mode
                if ((!audioPlayer.paused && audioPlayer.src) || currentAlbumIndex !== -1) {
                    rebuildCurrentSongs(true); // Rebuild the song list and reset history when shuffle mode changes

                    // If a song was playing and we are now in a shuffle mode (Album or All), and the song was successfully placed at the start
                    if (!audioPlayer.paused && audioPlayer.src && shuffleMode !== 0) {
                        // The rebuildCurrentSongs logic should have placed the current song at index 0 and updated currentIndex
                        // We don't need to call playSong again here, it should continue playing.
                        // Just ensure the tracklist is updated to show the new order with the current song at the top.
                        renderTracklist(currentAlbumIndex !== -1 ? allAlbums[currentAlbumIndex] : null);
                        // The highlight is handled within renderTracklist
                    } else if (!audioPlayer.paused && audioPlayer.src && shuffleMode === 0 && !wasShuffleOff) {
                        // Switched from shuffle back to off, song should ideally stay the same if in the album
                        // rebuildCurrentSongs already handles finding the song in the non-shuffled list
                        renderTracklist(currentAlbumIndex !== -1 ? allAlbums[currentAlbumIndex] : null);
                    } else {
                        // If player was paused or no song was playing, just update the tracklist display
                        renderTracklist(currentAlbumIndex !== -1 ? allAlbums[currentAlbumIndex] : null);
                    }
                } else {
                    // If no album is selected and no song is playing, just update the button and display
                    updateRadioDisplay('', ''); // Update display based on new shuffle mode
                }
            }


            function updateShuffleButton() {
                shuffleButton.innerHTML = `<span class="material-symbols-rounded">${ShuffleIcons[shuffleMode]}</span>`;
                shuffleButton.title = ShuffleTitles[shuffleMode];
                // Optional: Add a visual indicator for the active shuffle mode
                shuffleButton.classList.remove('active');
                if (shuffleMode !== 0) {
                    shuffleButton.classList.add('active');
                }
            }

            function rebuildCurrentSongs(resetHistory = false) {
                const currentlyPlayingSongSrc = !audioPlayer.paused && audioPlayer.src ? audioPlayer.src : null;
                let currentlyPlayingSong = null;

                if (currentlyPlayingSongSrc) {
                    // Find the currently playing song object in the *original* allSongs list
                    currentlyPlayingSong = allSongs.find(song => encodeURI(song.filename) === currentlyPlayingSongSrc);
                }

                let newCurrentSongs = [];

                if (shuffleMode === 0) { // Shuffle Off
                    newCurrentSongs = originalAlbumTracklist;
                } else if (shuffleMode === 1) { // Shuffle Album
                    if (currentAlbumIndex !== -1) {
                        newCurrentSongs = shuffleArray(originalAlbumTracklist.slice());
                        // If a song is currently playing AND it's in the current album,
                        // ensure it's placed at the beginning of the shuffled list.
                        if (currentlyPlayingSong) {
                            const isSongInCurrentAlbum = originalAlbumTracklist.some(song => song.filename === currentlyPlayingSong.filename);
                            if (isSongInCurrentAlbum) {
                                // Remove the currently playing song from the shuffled list if it's there
                                newCurrentSongs = newCurrentSongs.filter(song => song.filename !== currentlyPlayingSong.filename);
                                // Add it to the beginning
                                newCurrentSongs.unshift(currentlyPlayingSong);
                            }
                        }
                    } else {
                        // Should not happen in normal flow if shuffle album is selected without an album
                        newCurrentSongs = [];
                    }
                } else if (shuffleMode === 2) { // Shuffle All
                    newCurrentSongs = shuffleArray(allSongs.slice());
                    // If a song is currently playing, ensure it's placed at the beginning of the shuffled list.
                    if (currentlyPlayingSong) {
                        // Remove the currently playing song from the shuffled list if it's there
                        newCurrentSongs = newCurrentSongs.filter(song => song.filename !== currentlyPlayingSong.filename);
                        // Add it to the beginning
                        newCurrentSongs.unshift(currentlyPlayingSong);
                    }
                }

                currentSongs = newCurrentSongs; // Update the main playing list

                if (resetHistory) {
                    playbackHistory = [];
                    historyIndex = -1;
                }

                // Update currentIndex and history based on the new currentSongs list and currently playing song
                if (currentlyPlayingSong) {
                    const newIndex = currentSongs.findIndex(song => song.filename === currentlyPlayingSong.filename);
                    if (newIndex !== -1) {
                        currentIndex = newIndex;
                        if (resetHistory && shuffleMode !== 0) {
                            // If history was reset and we are in shuffle mode, add the current song as the first history entry
                            playbackHistory.push(currentIndex);
                            historyIndex = 0;
                        } else if (shuffleMode === 0) {
                            // If switched back to non-shuffle, history is cleared, and current index is set
                            // No need to add to history as history is for shuffle
                        }
                    } else {
                        // This case should ideally not happen if currentlyPlayingSong was found initially,
                        // but as a safeguard, stop playback if the song isn't in the new list.
                        audioPlayer.pause();
                        updateRadioDisplay('CD error', '');
                        playPauseButton.innerHTML = '<span class="material-symbols-rounded">play_arrow</span>';
                        currentIndex = -1;
                        playbackHistory = [];
                        historyIndex = -1;
                    }
                } else {
                    // If no song was playing, reset index and history
                    currentIndex = 0;
                    playbackHistory = [];
                    historyIndex = -1;
                }


                // Update the tracklist display to reflect the new order
                // Pass the current album data if available, even in shuffle all mode, for the title display
                renderTracklist(currentAlbumIndex !== -1 ? allAlbums[currentAlbumIndex] : null);
            }


            function shuffleArray(array) {
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]]; // Swap elements
                }
                return array;
            }


            const scrollText = () => {
                const container = document.querySelector('.marquee-container');
                const text = document.querySelector('.marquee-text');

                // Cancel previous animation if it exists
                if (scrollAnimation) {
                    scrollAnimation.cancel();
                    scrollAnimation = null;
                }

                // Reset position
                text.style.transform = 'translateX(0)';

                const containerWidth = container.offsetWidth;
                const textWidth = text.scrollWidth;

                if (textWidth > containerWidth) {
                    const overflowAmount = textWidth - containerWidth;

                    const keyframes = [
                        { transform: 'translateX(0)' },
                        { transform: 'translateX(0)', offset: 0.1 }, // small pause
                        { transform: `translateX(-${overflowAmount + 20}px)`, offset: 0.5 },
                        { transform: `translateX(-${overflowAmount + 20}px)`, offset: 0.6 }, // pause again
                        { transform: 'translateX(0)', offset: 1 }
                    ];

                    scrollAnimation = text.animate(keyframes, {
                        duration: textWidth * 30,
                        iterations: Infinity,
                        easing: 'linear'
                    });
                }
            };


        </script>


        <style>
            @font-face {
                font-family: LCDM2B;
                src: url("{{ asset('foutecd/files/DS-DIGI.TTF') }}") format("opentype");
            }

            .collection {
                font-size: smaller;
                padding: 2px;
                text-align: center;
                background-color: #ddd;
                margin: 0;
            }

            .album-covers div {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .breadcrumb {
                background-color: transparent;
            }

            .breadcrumb-item a {
                color: #007bff; /* Link color */
            }

            .breadcrumb-item.active {
                color: #6c757d; /* Active color */
            }

            .retro-player {
                padding: 20px;
                color: white;
                background: #3e3e3e; /* Darker gray background */
                border-radius: 15px;
                box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3); /* Add some shadow */
            }

            .retro-player h2 {
                font-weight: bolder;
                color: #0091dd; /* Blue accent color */
                margin-bottom: 15px;
            }

            .retro-player ol {
                list-style: none; /* Remove default list numbers */
                padding: 0;
            }

            .song.active {
                background-color: #0091dd !important;
                color: white;
                font-weight: bold; /* Make active song text bolder */
            }

            .song {
                cursor: pointer;
                padding: 8px 10px; /* Increased padding */
                transition: background 0.3s ease; /* Smooth transition */
                border-bottom: 1px solid #555; /* Separator line */
            }

            .song:last-child {
                border-bottom: none; /* No border for the last item */
            }

            .song:hover {
                background-color: #555; /* Slightly lighter gray on hover */
            }

            .player-controls {
                margin-top: 30px;
                background-color: #333;
                border: 5px solid #555;
                border-radius: 15px;
                padding: 20px; /* Increased padding */
                display: flex;
                flex-direction: column;
                align-items: center;
                box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5); /* Inner shadow for depth */
            }

            .car-radio {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 20px; /* Space below the radio part */
            }

            .marquee-container {
                background-color: #222;
                overflow: hidden;
                padding: 2px 7px; /* Increased padding */
                margin-bottom: 15px;
                width: 95%;
                text-align: center;
                border-radius: 5px;
                min-height: 35px; /* Slightly increased height */
                white-space: nowrap;
                text-overflow: ellipsis;
                font-size: 1.1em; /* Slightly larger font */
                border: 1px inset #444; /* Inset border */
            }

            .marquee-text {
                color: #0091dd;
                font-family: 'LCDM2B', Courier, monospace;
                font-size: xx-large;
                display: inline-block;
            }

            .radio-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 15px; /* Reduced gap slightly */
                justify-content: center;
            }

            .radio-button {
                background-color: #555;
                color: white;
                border: none;
                border-radius: 5px;
                padding: 10px 15px;
                cursor: pointer;
                font-size: 1.2em;
                transition: background 0.3s ease, transform 0.1s ease;
                display: flex; /* Use flexbox for centering icon */
                align-items: center;
                justify-content: center;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Button shadow */
            }

            .radio-button:hover {
                background-color: #777;
            }

            .radio-button:active {
                background-color: #999;
                transform: translateY(1px); /* Press effect */
                box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            }

            .radio-button.active {
                background-color: #0091dd; /* Highlight active repeat/shuffle */
                color: white;
            }


            audio {
                display: none;
            }

            .album-covers {
                display: flex;
                overflow-x: auto;
                padding: 15px; /* Increased padding */
                background-color: #ffffff; /* Light gray background for the scroll area */
                gap: 15px; /* Increased gap */
                border-radius: 15px;
                margin-bottom: 20px; /* Space below the covers */
                box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2); /* Inner shadow */
            }

            .album-cover {
                width: 120px;
                height: 120px; /* Ensure covers are square */
                object-fit: cover; /* Crop image to fit */
                cursor: pointer;
                transition: 0.3s ease;
                border: 2px solid transparent;
            }

            .album-cover:hover {
                transform: scale(1.05); /* Slightly less scale */
                box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.4);
            }

            .album-placeholder {
                min-width: 120px;
                min-height: 120px;
                background-color: #ddd; /* Slightly darker placeholder */
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                font-size: 0.9em;
                color: #555; /* Darker text */
                padding: 5px;
                box-sizing: border-box;
                overflow: hidden;
                text-overflow: ellipsis;
                word-break: auto-phrase !important;
            }

            .active-album div, .active-album img {
                transform: scale(1.1) !important;
                border-radius: 8px;
                border: 2px solid #0091dd;
            }

            /* Scrollbar styling for album covers */
            .album-covers::-webkit-scrollbar {
                height: 8px; /* Height of horizontal scrollbar */
            }

            .album-covers::-webkit-scrollbar-track {
                background: #ccc; /* Track color */
                border-radius: 10px;
            }

            .album-covers::-webkit-scrollbar-thumb {
                background: #888; /* Thumb color */
                border-radius: 10px;
            }

            .album-covers::-webkit-scrollbar-thumb:hover {
                background: #555; /* Thumb color on hover */
            }




        </style>
    </div>
@endsection
