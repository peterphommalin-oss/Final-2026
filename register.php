<?php
// 3. ໄຟລ໌ register.php: ໜ້າລົງທະບຽນ
require_once 'connect.php'; // ລວມເອົາໄຟລ໌ connect.php (ເຊິ່ງມີຕົວແປ $pdo)
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $phone = trim($_POST['phone']); // ດຶງຄ່າເບີໂທລະສັບ
    $pass = trim($_POST['password']);
    $confirm_pass = trim($_POST['confirm_password']);
    $role = trim($_POST['role'] ?? 'viewer'); // ຮັບຄ່າ role (ເລີ່ມຕົ້ນເປັນ viewer)

    if (empty($user) || empty($phone) || empty($pass) || empty($confirm_pass)) {
        $error = "ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ!";
    } elseif ($pass !== $confirm_pass) {
        $error = "ລະຫັດຜ່ານ ແລະ ຢືນຢັນລະຫັດຜ່ານບໍ່ຕົງກັນ!";
    } else {
        // ແກ້ Bug: ປ່ຽນຈາກ $conn ເປັນ $pdo ໃຫ້ກົງກັບ connect.php
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $user]);
        if ($stmt->rowCount() > 0) {
            $error = "ຊື່ຜູ້ໃຊ້ນີ້ຖືກນໍາໃຊ້ໄປແລ້ວ!";
        } else {
            // ເຂົ້າລະຫັດຜ່ານເພື່ອຄວາມປອດໄພ
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            
            // ບັນທຶກລົງຖານຂໍ້ມູນ (ເພີ່ມ column phone)
            $insert = $pdo->prepare("INSERT INTO users (username, password, phone, role) VALUES (:username, :password, :phone, :role)");
            if ($insert->execute(['username' => $user, 'password' => $hashed_password, 'phone' => $phone, 'role' => $role])) {
                $message = "ລົງທະບຽນສໍາເລັດແລ້ວ! ກະລຸນາເຂົ້າສູ່ລະບົບ.";
            } else {
                $error = "ເກີດຂໍ້ຜິດພາດ ບໍ່ສາມາດລົງທະບຽນໄດ້!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລົງທະບຽນ - ລະບົບວັດໂພນໄຮຄຳຈັນທະມາຮາມ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Phetsarath', sans-serif; }
    </style>
</head>
<body class="bg-[#faf6f0] min-h-screen flex flex-col justify-between">

    <div class="w-full bg-[#d97706] h-16 shadow-md flex items-center justify-center relative">
        <div class="absolute top-12 bg-white p-2 rounded-full shadow-lg border-2 border-[#d97706]">
            <img src="logo.jpeg" alt="ໂລໂກ້ວັດ" class="w-24 h-24 rounded-full object-cover" onerror="this.src='https://via.placeholder.com/80?text=Logo'">
        </div>
    </div>

    <div class="flex-grow flex items-center justify-center pt-16 pb-8">
        <div class="bg-white p-8 rounded-xl shadow-xl border border-orange-100 w-full max-w-md mx-4">
            <h2 class="text-2xl font-bold text-center text-[#b45309] mb-2 mt-4">ລົງທະບຽນຜູ້ໃຊ້ໃໝ່</h2>
            <p class="text-center text-gray-500 text-sm mb-6">ວັດໂນນໄຮຄຳ ຈັນທະມາຮາມ</p>

            <?php if(!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm rounded">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ຊື່ຜູ້ໃຊ້ (Username)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            👤
                        </span>
                        <input type="text" name="username" required value="<?php echo isset($user) ? htmlspecialchars($user) : ''; ?>" placeholder="ປ້ອນຊື່ຜູ້ໃຊ້..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ເບີໂທລະສັບ (Phone Number)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            📞
                        </span>
                        <input type="text" name="phone" required value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" placeholder="ປ້ອນເບີໂທລະສັບ 020..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ລະຫັດຜ່ານ (Password)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            🔒
                        </span>
                        <input type="password" name="password" required placeholder="ປ້ອນລະຫັດຜ່ານ..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ຢືນຢັນລະຫັດຜ່ານ (Confirm Password)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            🛡️
                        </span>
                        <input type="password" name="confirm_password" required placeholder="ປ້ອນລະຫັດຜ່ານຄືນອີກຄັ້ງ..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent">
                    </div>
                </div>
                <!-- ໃນຟອມ HTML ຂອງ register.php ໃຫ້ເພີ່ມ Select input ນີ້ເຂົ້າໄປ: -->
                <div>
                   <label class="block text-sm font-semibold text-gray-700 mb-1">ສິດການໃຊ້ງານ (Role)</label>
                   <select name="role" class="w-full text-gray-600 mb-1 px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706]">
                   <option value="viewer">👁️ ຜູ້ເບິ່ງ (Viewer - ເບິ່ງໄດ້ຢ່າງດຽວ)</option>
                   <option value="admin">⚡ ແອດມິນ (Admin - ເພີ່ມ, ແກ້ໄຂ, ລຶບ)</option>
                  </select>
                </div>

                <button type="submit" class="w-full bg-[#d97706] hover:bg-[#b45309] text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 shadow-md flex items-center justify-center gap-2">
                    <span>📝 ບັນທຶກລົງທະບຽນ</span>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                ມີບັນຊີຜູ້ໃຊ້ແລ້ວ? 
                <a href="login.php" class="text-[#d97706] font-bold hover:underline">ເຂົ້າສູ່ລະບົບ</a>
            </div>
        </div>
    </div>

    <div class="w-full bg-[#f3f4f6] text-center py-4 text-xs text-gray-500 border-t border-gray-200">
        Copyright &copy; 2026 ລະບົບຄຸ້ມຄອງລາຍຮັບ-ລາຍຈ່າຍ ວັດໂນນໄຮຄຳຈັນທະມາຮາມ. All Rights Reserved.
    </div>

</body>
</html>