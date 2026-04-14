<!-- JS Files -->
<script src="https://code.jquery.com/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
  $(function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 150,
      max: 1500,
      values: [ 520, 1100 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
      " - $" + $( "#slider-range" ).slider( "values", 1 ) );
  });
</script>

<script src="{{ asset('vertical/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('vertical/js/lightbox.min.js') }}"></script>
<script src="{{ asset('vertical/js/jquery.elevatezoom.js') }}"></script>
<script src="{{ asset('vertical/js/jquery.bxslider.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.slider8').bxSlider({
            mode: 'vertical',
            slideWidth: 300,
            minSlides: 3,
            slideMargin: 10
        });
        $('.slider9').bxSlider({
            mode: 'vertical',
            slideWidth: 300,
            minSlides: 3,
            slideMargin: 10
        });
        $('.slider10').bxSlider({
            mode: 'vertical',
            slideWidth: 300,
            minSlides: 3,
            slideMargin: 10
        });
    });
</script>

<script src="{{ asset('vertical/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('vertical/js/rs-plugin/js/jquery.themepunch.plugins.min.js') }}"></script>
<script src="{{ asset('vertical/js/rs-plugin/js/jquery.themepunch.revolution.min.js') }}"></script>
<script src="{{ asset('vertical/js/rs-plugin/rs.home.js') }}"></script>
<script src="{{ asset('vertical/js/bootstrap.min.js') }}"></script>
<!--[if lte IE 8]>
    <script type="text/javascript" src="{{ asset('vertical/js/ie-opacity-polyfill.js') }}"></script>
<![endif]-->
<script src="{{ asset('vertical/js/main.js') }}"></script>