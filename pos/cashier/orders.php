<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php
  require_once('partials/_sidebar.php');
  ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
    require_once('partials/_topnav.php');
    ?>
        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Select On Any Product To Make An Order
                            <button class="btn btn-outline-info float-right" data-toggle="modal"
                                data-target="#qrScanModal">
                                <i class="fas fa-qrcode"></i> Scan QR
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Image</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                  $ret = "SELECT * FROM  rpos_products  ORDER BY `rpos_products`.`created_at` DESC ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($prod = $res->fetch_object()) {
                    ?>
                                    <tr>
                                        <td>
                                            <?php
                        if ($prod->prod_img) {
                          echo "<img src='../admin/assets/img/products/$prod->prod_img' height='60' width='60 class='img-thumbnail'>";
                        } else {
                          echo "<img src='../admin/assets/img/products/default.jpg' height='60' width='60 class='img-thumbnail'>";
                        }

                        ?>
                                        </td>
                                        <td><?php echo $prod->prod_code; ?></td>
                                        <td><?php echo $prod->prod_name; ?></td>
                                        <td>RWF <?php echo $prod->prod_price; ?></td>
                                        <td>
                                            <a
                                                href="make_order.php?prod_id=<?php echo $prod->prod_id; ?>&prod_name=<?php echo $prod->prod_name; ?>&prod_price=<?php echo $prod->prod_price; ?>">
                                                <button class="btn btn-sm btn-warning">
                                                    <i class="fas fa-cart-plus"></i>
                                                    Place Order
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php
      require_once('partials/_footer.php');
      ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
  require_once('partials/_scripts.php');
  ?>
    <!-- QR Scan Modal -->
    <div class="modal fade" id="qrScanModal" tabindex="-1" role="dialog" aria-labelledby="qrScanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrScanModalLabel">Scan Product QR Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <video id="qr-video" width="320" height="240" autoplay muted playsinline
                        style="border:1px solid #ccc;"></video>
                    <canvas id="qr-canvas" width="320" height="240" style="display:none;"></canvas>
                    <div id="qr-result" class="mt-2"></div>
                    <div id="qr-error" class="text-danger mt-2"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script>
    let video = null;
    let canvas = null;
    let ctx = null;
    let scanning = false;
    let scanInterval = null;

    function startQRScanner() {
        video = document.getElementById('qr-video');
        canvas = document.getElementById('qr-canvas');
        ctx = canvas.getContext('2d', {
            willReadFrequently: true
        });
        document.getElementById('qr-result').innerHTML = '';
        document.getElementById('qr-error').innerHTML = '';
        scanning = true;
        let videoStarted = false;
        // Check for getUserMedia support with detailed debug
        if (!navigator.mediaDevices) {
            document.getElementById('qr-error').innerText =
                'navigator.mediaDevices is not available. This browser or context does not support camera access.\n' +
                'Try updating your browser, using a different browser, or checking if you are in an in-app browser.';
            return;
        }
        if (!navigator.mediaDevices.getUserMedia) {
            document.getElementById('qr-error').innerText =
                'navigator.mediaDevices.getUserMedia is not available. This browser does not support camera access.\n' +
                'Try updating your browser, using a different browser, or checking if you are in an in-app browser.';
            return;
        }
        // Timeout if video does not start
        let videoTimeout = setTimeout(function() {
            if (!videoStarted) {
                document.getElementById('qr-error').innerText =
                    'Camera did not start. Please check permissions, try a different browser, or reload the page.';
            }
        }, 5000);
        // Try environment camera first, fallback to default if it fails
        function tryDefaultCamera() {
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    video.srcObject = stream;
                    video.setAttribute('playsinline', true);
                    video.play();
                    scanInterval = setInterval(scanFrame, 300);
                    video.onplaying = function() {
                        videoStarted = true;
                        clearTimeout(videoTimeout);
                    };
                })
                .catch(function(err) {
                    document.getElementById('qr-error').innerText = 'Camera error (default): ' + err.message +
                        '\nMake sure you are using HTTPS and have granted camera permissions.';
                });
        }
        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment'
                }
            })
            .then(function(stream) {
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.play();
                scanInterval = setInterval(scanFrame, 300);
                video.onplaying = function() {
                    videoStarted = true;
                    clearTimeout(videoTimeout);
                };
            })
            .catch(function(err) {
                document.getElementById('qr-error').innerText = 'Camera error (environment): ' + err.message +
                    '\nTrying default camera...';
                tryDefaultCamera();
            });
    }

    function stopQRScanner() {
        scanning = false;
        clearInterval(scanInterval);
        if (video && video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
    }

    function scanFrame() {
        if (!scanning) return;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        let code = jsQR(imageData.data, imageData.width, imageData.height);
        if (code) {
            stopQRScanner();
            let prodCode = code.data;
            window.location.href = 'make_order.php?prod_code=' + encodeURIComponent(prodCode);
            return;
        }
    }
    $('#qrScanModal').on('shown.bs.modal', function() {
        startQRScanner();
    });
    $('#qrScanModal').on('hidden.bs.modal', function() {
        stopQRScanner();
        document.getElementById('qr-result').innerHTML = '';
        document.getElementById('qr-error').innerHTML = '';
    });
    </script>
</body>

</html>