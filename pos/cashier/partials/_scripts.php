<!-- Core -->
<script src="assets/vendor/jquery/dist/jquery.min.js"></script>
<script src="assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/argon.js?v=1.0.0"></script>
<script src="assets/vendor/chart.js/dist/Chart.min.js"></script>
<script src="assets/vendor/chart.js/dist/Chart.extension.js"></script>

<!-- QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
<script>
  let qrScanner;
  $('#qrScanModal').on('shown.bs.modal', function () {
    if (!qrScanner) {
      qrScanner = new Html5Qrcode("qr-video");
      qrScanner.start(
        { facingMode: "environment" },
        {
          fps: 10,
          qrbox: 250
        },
        qrCodeMessage => {
          $('#qr-result').html('<b>QR Code:</b> ' + qrCodeMessage);
          // Optionally: AJAX to fetch product by code and show details or auto-fill order/cart
          // $.post('fetch_product.php', { qr: qrCodeMessage }, function(data) { ... });
          qrScanner.stop();
        },
        errorMessage => {
          // ignore errors
        }
      );
    }
  });
  $('#qrScanModal').on('hidden.bs.modal', function () {
    if (qrScanner) {
      qrScanner.stop();
      qrScanner = null;
      $('#qr-result').html('');
    }
  });
</script>