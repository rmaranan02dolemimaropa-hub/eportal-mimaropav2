<?= $this->include('dashboard_layout/head') ?>


<!-- ==========================================
     DOLE MIMAROPA LOADING SCREEN
========================================== -->

<div id="doleLoadingScreen">

    <div id="doleLoadingContent">

        <div id="doleLogoBox">

            <img id="doleLoadingLogo" src="<?= base_url('assets/img/dole_blogo.png') ?>" width="125" height="125"
                fetchpriority="high" decoding="sync" alt="DOLE MIMAROPA Logo">

        </div>

        <div id="doleLoadingTitle">
            DOLE MIMAROPA
        </div>

        <div id="doleLoadingSubtitle">
            E-PORTAL
        </div>

    </div>

</div>

<script>
    (function() {

        const loadingScreen =
            document.getElementById('doleLoadingScreen');

        if (!loadingScreen) return;

        document.addEventListener('click', function(event) {

            const link = event.target.closest('.nav-link');

            if (!link) return;

            const href = link.href;

            if (!href) return;

            /*
             * Ignore special clicks
             */
            if (
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey ||
                event.button !== 0
            ) {
                return;
            }

            /*
             * Ignore same page
             */
            if (link.href === window.location.href) {
                return;
            }

            /*
             * Prevent normal navigation temporarily
             */
            event.preventDefault();

            /*
             * SHOW LOADING SCREEN FIRST
             */
            loadingScreen.classList.add('show');
            document.body.style.overflow = 'hidden';

            /*
             * Give browser one frame to render
             * the loading screen BEFORE navigation.
             */
            requestAnimationFrame(function() {

                requestAnimationFrame(function() {

                    window.location.href = href;

                });

            });

        }, true);


        /*
         * Hide when page is loaded/restored
         */
        window.addEventListener('pageshow', function() {

            loadingScreen.classList.remove('show');

            document.body.style.overflow = '';

        });

    })();
</script>