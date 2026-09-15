<?php
// 4. ໄຟລ໌ login.php: ໜ້າເຂົ້າສູ່ລະບົບ
session_start();
require_once 'connect.php'; // ລວມເອົາໄຟລ໌ connect.php
$error = "";

// ຖ້າຫາກເຂົ້າສູ່ລະບົບໄວ້ແລ້ວ ໃຫ້ຂ້າມໄປໜ້າ index.php ທັນທີ
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if (empty($user) || empty($pass)) {
        $error = "ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $user]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($pass, $userData['password'])) {
            $_SESSION['user_id'] = $userData['id']; 
            $_SESSION['username'] = $userData['username'];
            $_SESSION['role'] = $userData['role'];
            
            header("Location: index.php");
            exit();
        } else {
            $error = "ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເຂົ້າສູ່ລະບົບ - ລະບົບວັດໂພນໄຮຄຳຈັນທະມາຮາມ</title>
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
            <h2 class="text-2xl font-bold text-center text-[#b45309] mb-2 mt-4">ເຂົ້າສູ່ລະບົບ</h2>
            <p class="text-center text-gray-500 text-sm mb-6">ວັດໂນນໄຮຄຳ ຈັນທະມາຮາມ</p>

            <?php if(!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ຊື່ຜູ້ໃຊ້ (Username)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            👤
                        </span>
                        <input type="text" name="username" required placeholder="ປ້ອນຊື່ຜູ້ໃຊ້ຂອງທ່ານ..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-semibold text-gray-700">ລະຫັດຜ່ານ (Password)</label>
                        <!-- ແກ້ໄຂບ່ອນນີ້: ໃສ່ href ໄປຫາ forgot_password.php -->
                        <a href="forgot_password.php" class="text-xs text-[#d97706] font-semibold hover:underline">ລືມລະຫັດຜ່ານ?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            🔒
                        </span>
                        <input type="password" name="password" required placeholder="ປ້ອນລະຫັດຜ່ານຂອງທ່ານ..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent">
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#d97706] hover:bg-[#b45309] text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 shadow-md flex items-center justify-center gap-2">
                    <span>🔑 ເຂົ້າສູ່ລະບົບ</span>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                ຍັງບໍ່ມີບັນຊີຜູ້ໃຊ້? 
                <a href="register.php" class="text-[#d97706] font-bold hover:underline">ລົງທະບຽນທີ່ນີ້</a>
            </div>
        </div>
    </div>

    <div class="w-full bg-[#f3f4f6] text-center py-4 text-xs text-gray-500 border-t border-gray-200">
        Copyright &copy; 2026 ລະບົບຄຸ້ມຄອງລາຍຮັບ-ລາຍຈ່າຍ ວັດໂນນໄຮຄຳ ຈັນທະມາຮາມ. 
    </div>

</body>
</html>