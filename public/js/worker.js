    const toggleBtn = document.getElementById('toggleStatsBtn');
        const statRow = document.getElementById('statRow');

        toggleBtn.addEventListener('click', () => {
            if (statRow.classList.contains('hidden')) {
                statRow.classList.remove('hidden');
                toggleBtn.textContent = 'Tutup';
            } else {
                statRow.classList.add('hidden');
                toggleBtn.textContent = 'Buka';
            }
        });