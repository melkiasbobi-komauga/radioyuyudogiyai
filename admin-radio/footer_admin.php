<?php
// File ini berisi tag penutup HTML dan skrip JavaScript.
?>
</main>
</div>
</div>

<script>
// Script untuk mengaktifkan/menonaktifkan sidebar di tampilan mobile.
document.addEventListener('DOMContentLoaded', function() {
    // PERBAIKAN: ID disesuaikan menjadi 'hamburger' agar konsisten dengan file lain.
    const hamburgerButton = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');

    if (hamburgerButton && sidebar) {
        hamburgerButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }
});
</script>
</body>

</html>