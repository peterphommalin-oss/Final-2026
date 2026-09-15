<?php
session_start();
require_once 'connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $new_pass = trim($_POST['new_password']);
    $confirm_pass = trim($_POST['confirm_password']);

    if (empty($user) || empty($phone) || empty($new_pass) || empty($confirm_pass)) {
        $error = "ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ!";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "ລະຫັດຜ່ານໃໝ່ ແລະ ຢືນຢັນລະຫັດຜ່ານບໍ່ຕົງກັນ!";
    } else {
        // ກວດສອບຊື່ຜູ້ໃຊ້ ແລະ ເບີໂທ
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND phone = :phone");
        $stmt->execute(['username' => $user, 'phone' => $phone]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            // ອັບເດດລະຫັດຜ່ານໃໝ່
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            
            if ($updateStmt->execute(['password' => $hashed_password, 'id' => $userData['id']])) {
                $success = "ປ່ຽນລະຫັດຜ່ານສຳເລັດແລ້ວ! <a href='login.php' class='font-bold underline text-[#b45309]'>ກົດບ່ອນນີ້ເພື່ອເຂົ້າສູ່ລະບົບ</a>";
            } else {
                $error = "ເກີດຂໍ້ຜິດພາດ ບໍ່ສາມາດອັບເດດລະຫັດຜ່ານໄດ້!";
            }
        } else {
            $error = "ຊື່ຜູ້ໃຊ້ ຫຼື ເບີໂທລະສັບ ບໍ່ຖືກຕ້ອງ!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລືມລະຫັດຜ່ານ - ລະບົບວັດໂພນໄຮຄຳຈັນທະມາຮາມ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath&display=swap" rel="stylesheet">
    <style> body { font-family: 'Phetsarath', sans-serif; } </style>
</head>
<body class="bg-[#faf6f0] min-h-screen flex flex-col justify-between">

    <div class="w-full bg-[#d97706] h-16 shadow-md flex items-center justify-center relative">
        <div class="absolute top-12 bg-white p-2 rounded-full shadow-lg border-2 border-[#d97706]">
            <img src="logo.jpeg" alt="ໂລໂກ້ວັດ" class="w-24 h-24 rounded-full object-cover" onerror="this.src='https://via.placeholder.com/80?text=Logo'">
        </div>
    </div>

    <div class="flex-grow flex items-center justify-center pt-16 pb-8">
        <div class="bg-white p-8 rounded-xl shadow-xl border border-orange-100 w-full max-w-md mx-4">
            <h2 class="text-2xl font-bold text-center text-[#b45309] mb-2 mt-4">🔑 ຕັ້ງລະຫັດຜ່ານໃໝ່</h2>
            <p class="text-center text-gray-500 text-sm mb-6">ກະລຸນາປ້ອນຂໍ້ມູນເພື່ອຢືນຢັນຕົວຕົນ</p>

            <?php if(!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm rounded">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ຊື່ຜູ້ໃຊ້ (Username)</label>
                    <input type="text" name="username" required placeholder="ປ້ອນຊື່ຜູ້ໃຊ້..." 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ເບີໂທລະສັບ (Phone)</label>
                    <input type="text" name="phone" required placeholder="ປ້ອນເບີໂທລະສັບທີ່ລົງທະບຽນ..." 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ລະຫັດຜ່ານໃໝ່</label>
                    <input type="password" name="new_password" required placeholder="ປ້ອນລະຫັດຜ່ານໃໝ່..." 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ຢືນຢັນລະຫັດຜ່ານໃໝ່</label>
                    <input type="password" name="confirm_password" required placeholder="ປ້ອນລະຫັດຜ່ານໃໝ່ອີກຄັ້ງ..." 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706]">
                </div>

                <button type="submit" class="w-full bg-[#d97706] hover:bg-[#b45309] text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 shadow-md">
                    ບັນທຶກລະຫັດຜ່ານໃໝ່
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <a href="login.php" class="text-[#d97706] font-bold hover:underline">← ກັບໄປໜ້າເຂົ້າສູ່ລະບົບ</a>
            </div>
        </div>
    </div>

    <div class="w-full bg-[#f3f4f6] text-center py-4 text-xs text-gray-500 border-t border-gray-200">
        Copyright &copy; 2026 ລະບົບຄຸ້ມຄອງລາຍຮັບ-ລາຍຈ່າຍ ວັດໂນນໄຮຄຳ ຈັນທະມາຮາມ. 
    </div>

</body>
</html>