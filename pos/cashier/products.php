<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $adn = "DELETE FROM  rpos_products  WHERE  prod_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Deleted" && header("refresh:1; url=products.php");
    } else {
        $err = "Try Again Later";
    }
}
// Add PHP logic at the top to handle status toggle
if (isset($_POST['toggleStatus'])) {
    $toggle_id = $_POST['toggle_id'];
    $new_status = $_POST['new_status'];
    $stmt = $mysqli->prepare("UPDATE rpos_products SET status=? WHERE prod_id=?");
    $stmt->bind_param('ss', $new_status, $toggle_id);
    $stmt->execute();
    $stmt->close();
    header("Location: products.php");
    exit();
}
// Handle filters and pagination
$where = [];
$params = [];
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $where[] = 'status = ?';
    $params[] = $_GET['status'];
}
if (isset($_GET['category']) && $_GET['category'] !== '') {
    $where[] = 'category = ?';
    $params[] = $_GET['category'];
}
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $where[] = '(prod_name LIKE ? OR prod_code LIKE ?)';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
// Remove all pagination logic
// Remove: $page, $per_page, $offset, $total_pages, and pagination nav UI
// Only keep the SQL and display logic for all products
// Remove the pagination nav section at the bottom of the page
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
                            Stock Items
                            <button class="btn btn-outline-info float-right" data-toggle="modal"
                                data-target="#qrScanModal">
                                <i class="fas fa-qrcode"></i> Scan QR
                            </button>
                            <!-- <a href="add_product.php" class="btn btn-outline-success">
                                <i class="fas fa-utensils"></i>
                                Add New Product
                            </a> -->
                        </div>
                        <div class="table-responsive">
                            <form class="form-inline mb-3" method="get">
                                <input type="text" name="search" class="form-control mr-2"
                                    placeholder="Search name/code"
                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <select name="status" class="form-control mr-2">
                                    <option value="">All Status</option>
                                    <option value="active" <?php if (isset($_GET['status']) && $_GET['status'] === 'active')
                                        echo 'selected'; ?>>Active</option>
                                    <option value="inactive" <?php if (isset($_GET['status']) && $_GET['status'] === 'inactive')
                                        echo 'selected'; ?>>Inactive</option>
                                </select>
                                <select name="category" class="form-control mr-2">
                                    <option value="">All Categories</option>
                                    <option value="Fruits" <?php if (isset($_GET['category']) && $_GET['category'] == "Fruits")
                                        echo 'selected'; ?>>Fruits</option>
                                    <option value="Vegetables" <?php if (isset($_GET['category']) && $_GET['category'] == "Vegetables")
                                        echo 'selected'; ?>>Vegetables</option>
                                    <option value="Dairy" <?php if (isset($_GET['category']) && $_GET['category'] == "Dairy")
                                        echo 'selected'; ?>>Dairy</option>
                                    <option value="Meat" <?php if (isset($_GET['category']) && $_GET['category'] == "Meat")
                                        echo 'selected'; ?>>Meat</option>
                                    <option value="Bakery" <?php if (isset($_GET['category']) && $_GET['category'] == "Bakery")
                                        echo 'selected'; ?>>Bakery</option>
                                    <option value="Beverages" <?php if (isset($_GET['category']) && $_GET['category'] == "Beverages")
                                        echo 'selected'; ?>>Beverages</option>
                                    <option value="Snacks" <?php if (isset($_GET['category']) && $_GET['category'] == "Snacks")
                                        echo 'selected'; ?>>Snacks</option>
                                    <option value="Frozen" <?php if (isset($_GET['category']) && $_GET['category'] == "Frozen")
                                        echo 'selected'; ?>>Frozen</option>
                                    <option value="Household" <?php if (isset($_GET['category']) && $_GET['category'] == "Household")
                                        echo 'selected'; ?>>Household</option>
                                    <option value="Personal Care" <?php if (isset($_GET['category']) && $_GET['category'] == "Personal Care")
                                        echo 'selected'; ?>>Personal Care</option>
                                    <option value="Other" <?php if (isset($_GET['category']) && $_GET['category'] == "Other")
                                        echo 'selected'; ?>>Other</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Image / QR</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Stock</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Remove pagination for debugging
                                    $ret = "SELECT * FROM rpos_products $where_sql";
                                    $stmt = $mysqli->prepare($ret);
                                    if ($stmt === false) {
                                        die("Query Error: " . $mysqli->error);
                                    }
                                    if ($params) {
                                        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
                                    }
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    if ($res === false) {
                                        echo '<div style="color:red;">get_result() failed: ' . $stmt->error . '</div>';
                                    }
                                    if ($res && $res->num_rows > 0) {
                                        while ($prod = $res->fetch_object()) {
                                            ?>
                                    <tr>
                                        <td>
                                            <?php
                                                    if ($prod->prod_img) {
                                                        echo "<img src='../admin/assets/img/products/$prod->prod_img' height='60' width='60' class='img-thumbnail'>";
                                                    } else {
                                                        echo "<img src='../admin/assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                                                    }
                                                    ?>
                                            <br>
                                            <div id="qr_<?php echo $prod->prod_id; ?>" class="qr-div"></div>
                                            <button type="button" class="btn btn-sm btn-info mt-1"
                                                onclick="downloadQR('<?php echo $prod->prod_id; ?>')">Download
                                                QR</button>
                                        </td>
                                        <td><?php echo $prod->prod_code; ?></td>
                                        <td><?php echo $prod->prod_name; ?></td>
                                        <td><?php echo $prod->category ? htmlspecialchars($prod->category) : 'Uncategorized'; ?>
                                        </td>
                                        <td>$ <?php echo $prod->prod_price; ?></td>
                                        <td>
                                            <span
                                                class="badge badge-<?php echo $prod->status == 'active' ? 'success' : 'danger'; ?> p-2"
                                                style="font-size: 1em;">
                                                <?php echo ucfirst($prod->status); ?>
                                            </span>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="toggle_id"
                                                    value="<?php echo $prod->prod_id; ?>">
                                                <input type="hidden" name="new_status"
                                                    value="<?php echo $prod->status == 'active' ? 'inactive' : 'active'; ?>">
                                                <button type="submit" name="toggleStatus"
                                                    class="btn btn-sm btn-outline-<?php echo $prod->status == 'active' ? 'danger' : 'success'; ?> ml-2">
                                                    <?php echo $prod->status == 'active' ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="badge badge-info p-2" style="font-size: 1em;">
                                                <?php echo $prod->quantity; ?> in stock
                                            </span>
                                            <?php if ($prod->quantity <= $prod->min_stocks) { ?>
                                            <span class="badge badge-warning p-2 ml-1" style="font-size: 1em;">Low
                                                Stock!</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="update_product.php?update=<?php echo $prod->prod_id; ?>">
                                                <button class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                    Update
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php }
                                    } else {
                                        echo '<tr><td colspan="8" class="text-center">No products found.</td></tr>';
                                    }
                                    ?>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.qr-div').forEach(function(div) {
            var prodId = div.id.replace('qr_', '');
            var prodCode = div.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
            new QRCode(div, {
                text: prodCode,
                width: 60,
                height: 60,
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    });

    function downloadQR(prodId) {
        var qrDiv = document.getElementById('qr_' + prodId);
        var img = qrDiv.querySelector('img');
        if (img) {
            var a = document.createElement('a');
            a.href = img.src;
            a.download = 'qr_' + prodId + '.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    }

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
            // Redirect directly to make_order.php with the scanned product code
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
    <!-- For local HTTPS testing, see:
    // ngrok: https://ngrok.com/
    // localhost.run: https://localhost.run/ -->
</body>

</html>