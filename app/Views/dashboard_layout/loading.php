    <!-- ==========================================
     DOLE MIMAROPA LOADING SCREEN
========================================== -->
    <div id="doleLoadingScreen">

        <div id="doleLoadingContent">

            <!-- LOGO -->
            <div id="doleLogoBox">
                <img id="doleLoadingLogo" src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE MIMAROPA Logo">
            </div>

            <!-- TITLE -->
            <div id="doleLoadingTitle">
                DOLE MIMAROPA
            </div>

            <div id="doleLoadingSubtitle">
                E-PORTAL
            </div>

        </div>

    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const loadingScreen =
            document.getElementById('doleLoadingScreen');

        if (!loadingScreen) return;

        const sidebarLinks =
            document.querySelectorAll('.nav-link');


        sidebarLinks.forEach(function (link) {

            link.addEventListener('click', function (event) {

                const href = this.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href === 'javascript:void(0)'
                ) {
                    return;
                }


                /* Allow Ctrl / CMD / Shift / Middle Click */

                if (
                    event.ctrlKey ||
                    event.metaKey ||
                    event.shiftKey ||
                    event.button === 1
                ) {
                    return;
                }


                /* Stop normal navigation temporarily */

                event.preventDefault();


                /* Show loading screen */

                loadingScreen.classList.add('show');

                document.body.style.overflow = 'hidden';


                /*
                 * Navigate immediately.
                 * The loading screen will remain visible
                 * while the next page is loading.
                 */

                window.location.href = href;

            });

        });


        /* Back / Forward */

        window.addEventListener('pageshow', function () {

            loadingScreen.classList.remove('show');

            document.body.style.overflow = '';

        });

    });
</script>