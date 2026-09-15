<?php
/**
 * =====================================================
 * ໄຟລ໌: profile.php
 * ໜ້າທີ່: ໜ້າຂໍ້ມູນສ່ວນຕົວ (Profile) ແລະ ປ່ຽນຮູບໂປຣຟາຍ
 * =====================================================
 */

session_start();
require_once 'connect.php';

// ກວດສອບວ່າໄດ້ເຂົ້າສູ່ລະບົບແລ້ວຫຼືບໍ່
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$successMsg = '';
$errorMsg = '';

// ==========================================
// ຈັດການການ Form Submit (ອັບໂຫຼດຮູບ / ອັບເດດຂໍ້ມູນ)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. ກໍລະນີ: ອັບໂຫຼດຮູບໂປຣຟາຍ
    if (isset($_POST['action']) && $_POST['action'] === 'update_avatar') {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($ext, $allowed)) {
                // ສ້າງຊື່ໄຟລ໌ໃໝ່ເພື່ອປ້ອງກັນຊື່ຊ້ຳ
                $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                $uploadDir = __DIR__ . '/uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // ອັບເດດຊື່ໄຟລ໌ລົງ Database
                    $updateStmt = $pdo->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
                    $updateStmt->execute(['avatar' => $newFileName, 'id' => $userId]);
                    $successMsg = 'ປ່ຽນຮູບໂປຣຟາຍສົມບູນແລ້ວ!';
                } else {
                    $errorMsg = 'ບໍ່ສາມາດບັນທຶກໄຟລ໌ຮູບໄດ້';
                }
            } else {
                $errorMsg = 'ກະລຸນາເລືອກໄຟລ໌ຮູບພາບ (JPG, PNG, WEBP, GIF) ເທົ່ານັ້ນ';
            }
        } else {
            $errorMsg = 'ກະລຸນາເລືອກຮູບພາບທີ່ຕ້ອງການອັບໂຫຼດ';
        }
    }

    // 2. ກໍລະນີ: ອັບເດດຂໍ້ມູນສ່ວນຕົວ (Username / Phone)
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!empty($username)) {
            $updateInfo = $pdo->prepare("UPDATE users SET username = :username, phone = :phone WHERE id = :id");
            $updateInfo->execute([
                'username' => $username,
                'phone' => $phone,
                'id' => $userId
            ]);
            $successMsg = 'ອັບເດດຂໍ້ມູນສ່ວນຕົວຮຽບຮ້ອຍແລ້ວ!';
        } else {
            $errorMsg = 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ງານ';
        }
    }
}

// ດຶງຂໍ້ມູນຜູ້ໃຊ້ຄົນປັດຈຸບັນ
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

function e(?string $str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຂໍ້ມູນສ່ວນຕົວ - ລະບົບວັດໂພນໄຮຄຳຈັນທະມາຮາມ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;600;700;800&family=Phetsarath&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Lao', 'Phetsarath', sans-serif; }
        .bg-temple { background: linear-gradient(135deg, #fdf6e3 0%, #fff8ed 50%, #ffefd4 100%); min-height: 100vh; }
        .card-shadow { box-shadow: 0 10px 25px -5px rgba(240, 92, 10, 0.15); }
    </style>
</head>
<body class="bg-temple text-gray-800 flex flex-col justify-between">

    <!-- Header Navigation -->
    <header class="bg-gradient-to-r from-amber-600 via-orange-500 to-amber-500 shadow-lg text-white">
        <div class="container mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="index.php" class="bg-amber-700 hover:bg-amber-800 text-white p-2 rounded-xl transition flex items-center gap-1 text-sm font-semibold">
                    ⬅️ ກັບຄືນ Dashboard
                </a>
            </div>
            <h1 class="text-xl font-bold">👤 ຂໍ້ມູນສ່ວນຕົວຜູ້ໃຊ້ງານ</h1>
            <div class="w-24"></div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-3xl card-shadow border border-amber-100 w-full max-w-lg overflow-hidden relative">
            
            <!-- Banner Background -->
            <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 h-36 relative"></div>

            <!-- Profile Avatar Section -->
            <div class="relative px-8 text-center">
                <div class="absolute -top-20 left-1/2 transform -translate-x-1/2">
                    <div class="relative group cursor-pointer" onclick="openAvatarModal()">
                        <!-- ຮູບໂປຣຟາຍ -->
                        <?php if (!empty($user['avatar']) && file_exists(__DIR__ . '/uploads/' . $user['avatar'])): ?>
                            <img src="uploads/<?= e($user['avatar']) ?>" alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-xl transition group-hover:opacity-90">
                        <?php else: ?>
                            <div class="w-32 h-32 bg-amber-100 rounded-full flex items-center justify-center text-5xl border-4 border-white shadow-xl text-amber-600">
                                👤
                            </div>
                        <?php endif; ?>

                        <!-- ປຸ່ມກ້ອງຖ່າຍຮູບ hover overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition duration-200">
                            <span class="text-xs font-bold bg-black bg-opacity-60 px-2 py-1 rounded-full">📷 ປ່ຽນຮູບ</span>
                        </div>

                        <!-- Badge ກ້ອງຖ່າຍຮູບ góc ຂວາລຸ່ມ -->
                        <button type="button" class="absolute bottom-1 right-1 bg-amber-600 hover:bg-amber-700 text-white p-2.5 rounded-full shadow-lg border-2 border-white transition">
                            📷
                        </button>
                    </div>
                </div>

                <!-- ຂໍ້ມູນຊື່ & Role -->
                <div class="pt-16 pb-4">
                    <h2 class="text-2xl font-extrabold text-gray-800">
                        <?= e($user['username']) ?>
                    </h2>
                    <span class="mt-1 inline-block bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        🛡️ ສິດທິ: <?= e($user['role'] ?? 'User') ?>
                    </span>
                </div>

                <!-- รายละเอียดข้อมูล -->
                <div class="mt-4 border-t border-gray-100 pt-6 text-left space-y-3">
                    
                    <div class="flex items-center justify-between p-3.5 bg-amber-50/50 rounded-xl border border-amber-100/60">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🆔</span>
                            <span class="text-gray-600 text-sm font-medium">ລະຫັດຜູ້ໃຊ້ (User ID)</span>
                        </div>
                        <span class="font-bold text-gray-800">#<?= e($user['id']) ?></span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 bg-amber-50/50 rounded-xl border border-amber-100/60">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👤</span>
                            <span class="text-gray-600 text-sm font-medium">ຊື່ຜູ້ໃຊ້ງານ (Username)</span>
                        </div>
                        <span class="font-bold text-gray-800"><?= e($user['username']) ?></span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 bg-amber-50/50 rounded-xl border border-amber-100/60">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📞</span>
                            <span class="text-gray-600 text-sm font-medium">ເບີໂທລະສັບ (Phone)</span>
                        </div>
                        <span class="font-bold text-gray-800">
                            <?= !empty($user['phone']) ? e($user['phone']) : '<span class="text-gray-400 font-normal">ບໍ່ທັນມີຂໍ້ມູນ</span>' ?>
                        </span>
                    </div>

                </div>

                <!-- ປຸ່ມຈັດການ -->
                <div class="mt-8 mb-6 flex flex-col sm:flex-row gap-3 justify-center">
                    <button type="button" onclick="openEditProfileModal()" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                        ✏️ ແກ້ໄຂຂໍ້ມູນ
                    </button>
                    <a href="logout.php" onclick="return confirmLogout(event)" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                        🚪 ອອກຈາກລະບົບ
                    </a>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full bg-white border-t border-amber-100 text-center py-4 text-xs text-gray-500">
        Copyright &copy; 2026 ລະບົບຄຸ້ມຄອງລາຍຮັບ-ລາຍຈ່າຍ ວັດໂພນໄຮຄຳຈັນທະມາຮາມ. All Rights Reserved.
    </footer>

    <!-- ========================================== -->
    <!-- Modal 1: ປ່ຽນຮູບໂປຣຟາຍ -->
    <!-- ========================================== -->
    <div id="avatarModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border-t-8 border-amber-500 overflow-hidden">
            <div class="bg-amber-50 px-6 py-4 border-b border-amber-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-amber-900 flex items-center gap-2">
                    📷 ປ່ຽນຮູບໂປຣຟາຍ
                </h3>
                <button type="button" onclick="closeAvatarModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none">&times;</button>
            </div>

            <form action="profile.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="action" value="update_avatar">

                <!-- Preview ຮູບ -->
                <div class="text-center">
                    <div class="w-36 h-36 mx-auto rounded-full border-4 border-amber-200 overflow-hidden shadow-inner bg-gray-50 flex items-center justify-center mb-3">
                        <img id="avatarPreview" src="<?= (!empty($user['avatar']) && file_exists(__DIR__ . '/uploads/' . $user['avatar'])) ? 'uploads/' . e($user['avatar']) : 'https://via.placeholder.com/150?text=Avatar' ?>" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <p class="text-xs text-gray-500">ຮອງຮັບໄຟລ໌: JPG, PNG, WEBP, GIF (ບໍ່ເກີນ 5MB)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ເລືອກຮູບໃໝ່:</label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="previewImage(this)" required class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm bg-gray-50 focus:outline-none">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeAvatarModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-xl font-bold transition">ຍົກເລີກ</button>
                    <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-xl font-bold shadow-md transition">💾 ບັນທຶກຮູບ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- Modal 2: ແກ້ໄຂຂໍ້ມູນສ່ວນຕົວ -->
    <!-- ========================================== -->
    <div id="editProfileModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border-t-8 border-amber-500 overflow-hidden">
            <div class="bg-amber-50 px-6 py-4 border-b border-amber-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-amber-900 flex items-center gap-2">
                    ✏️ ແກ້ໄຂຂໍ້ມູນສ່ວນຕົວ
                </h3>
                <button type="button" onclick="closeEditProfileModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none">&times;</button>
            </div>

            <form action="profile.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="update_profile">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">👤 ຊື່ຜູ້ໃຊ້ງານ (Username)</label>
                    <input type="text" name="username" value="<?= e($user['username']) ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">📞 ເບີໂທລະສັບ (Phone)</label>
                    <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>" placeholder="020..." class="w-full px-3 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeEditProfileModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-xl font-bold transition">ຍົກເລີກ</button>
                    <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-xl font-bold shadow-md transition">💾 ບັນທຶກຂໍ້ມູນ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts Javascript -->
    <script>
        // Modal Avatar Functions
        function openAvatarModal() {
            document.getElementById('avatarModal').classList.remove('hidden');
        }
        function closeAvatarModal() {
            document.getElementById('avatarModal').classList.add('hidden');
        }

        // Modal Edit Profile Functions
        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
        }
        function closeEditProfileModal() {
            document.getElementById('editProfileModal').classList.add('hidden');
        }

        // Preview ຮູບກ່ອນບັນທຶກ
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ຢືນຢັນການອອກຈາກລະບົບ
        function confirmLogout(e) {
            e.preventDefault();
            const href = e.currentTarget.getAttribute('href');
            Swal.fire({
                title: 'ອອກຈາກລະບົບ?',
                text: "ທ່ານຕ້ອງການອອກຈາກລະບົບແທ້ບໍ່?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ອອກຈາກລະບົບ',
                cancelButtonText: 'ຍົກເລີກ',
                customClass: { popup: 'rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        }

        // ສະແດງ Alert ແຈ້ງເຕືອນ
        <?php if (!empty($successMsg)): ?>
            Swal.fire({
                icon: 'success',
                title: 'ສຳເລັດ!',
                text: '<?= e($successMsg) ?>',
                confirmButtonColor: '#d97706',
                customClass: { popup: 'rounded-2xl' }
            });
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            Swal.fire({
                icon: 'error',
                title: 'ຜິດພາດ!',
                text: '<?= e($errorMsg) ?>',
                confirmButtonColor: '#d97706',
                customClass: { popup: 'rounded-2xl' }
            });
        <?php endif; ?>
    </script>

</body>
</html>