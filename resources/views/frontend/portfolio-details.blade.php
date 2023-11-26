@extends('frontend.main_master')

 <!-- ======= Portfolio Section ======= -->
    <div class="container">

        <div class="section-title">
            <h2>Portfolio</h2>
            <p>My Works</p>
        </div>

        <div class="row">
            <div class="col-lg-12 d-flex justify-content-center">
                <ul id="portfolio-flters">
                    <li data-filter="*" class="filter-active">All</li>
                </ul>
            </div>
        </div>

        <div class="row portfolio-container">
        @foreach($portfolios as $portfolio)
            <div class="col-lg-4 col-md-6 portfolio-item filter-app">
                <div class="portfolio-wrap">
                    <img src="{{ asset($portfolio->project_image) }}" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>{{ $portfolio->title }}</h4>
                        <p>{{ $portfolio->category }}</p>
                        <div class="portfolio-links">
                            <a href="assets/img/portfolio/portfolio-1.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="App 1"><i class="bx bx-plus"></i></a>
                            <a href="{{ url('project/details/'.$portfolio->id ) }}" data-gallery="portfolioDetailsGallery" data-glightbox="type: external" class="portfolio-details-lightbox" title="Portfolio Details"><i class="bx bx-link"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        </div>

    </div>
</main><!-- End #main -->

    <!-- Vendor JS Files -->
    <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/purecounter/purecounter.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/waypoints/noframework.waypoints.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>

