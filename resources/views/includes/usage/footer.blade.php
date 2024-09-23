<footer id="footer">
    <div class="footer-top pt-0 pb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 footer-contact">
                    <img src="{{ asset('img/logo_cdf.png') }}" alt="La Cité des Formations"
                         title="La Cité des Formations" class="img-fluid">
                    <p>
                        8 allée Roger Lecotté<br>
                        37100 Tours<br><br>
                        <strong>Téléphone:</strong> 02 47 88 51 00<br>
                        <strong>Email:</strong> contact@citeformations.com<br>
                    </p>
                </div>
                @yield('footer-links')
                <div class="col-lg-3 col-md-6 footer-links mt-4">
                    <h4>Nos réseaux sociaux</h4>
                    <p>Retrouvez notre CFA sur tous ces médias</p>
                    <div class="social-links mt-3">
                      @foreach(AP::getMedias() as $media)
                        <a target="_blank" href="{{ $media->url }}" title="{{ $media->title }}"><i class="{{ $media->iconClass }}"></i></a>
                      @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container footer-bottom clearfix">
        <div class="copyright">&copy; Copyright <strong><span>Arsha</span></strong>. All Rights Reserved</div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/ -->
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
    </div>
</footer>
<div id="preloader"></div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Scripts -->
<!-- Vendor JS Files -->
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="{{ asset('vendor/aos/aos.js') }}" defer></script>
<script src="{{ asset('vendor/glightbox/js/glightbox.min.js') }}" defer></script>
<script src="{{ asset('vendor/isotope-layout/isotope.pkgd.min.js') }}" defer></script>
<script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}" defer></script>
<script src="{{ asset('vendor/waypoints/noframework.waypoints.js') }}" defer></script>
<script src="{{ asset('vendor/php-email-form/validate.js') }}" defer></script>

<!-- New Tab Redirection JS File -->
<script src="{{ asset('js/new-tab-redirection.js') }}" defer></script>

<!--auth-->
@if(isset(auth()->user()->id) )
<script>window.userId = {{auth()->user()->id}}</script>
@endif

<!-- Template Main JS File -->
<script src="{{ asset('js/main.js') }}" defer></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

@livewireScripts(['nonce' => csp_nonce()])

<!-- Modal JS File -->
<script src="{{ asset('js/confirmManager.js') }}"></script>
