<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleButton = document.getElementById("sidebarToggleTop"); // Correct ID
        const sidebar = document.getElementById("accordionSidebar");

        toggleButton.addEventListener("click", function () {
            document.body.classList.toggle("sidebar-toggled");
            sidebar.classList.toggle("toggled");
        });
    });
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

