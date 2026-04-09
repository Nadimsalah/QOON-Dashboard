        </div> <!-- End page-content -->
    </div> <!-- End main-wrapper -->

    <script src="js/bootstrap.min.js"></script>
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }

        // Initialize Skeleton hide after page load (Simulated)
        $(window).on('load', function() {
            setTimeout(function() {
                $('.skeleton').removeClass('skeleton').css('background', 'none').css('animation', 'none');
            }, 800);
        });
    </script>
</body>
</html>
