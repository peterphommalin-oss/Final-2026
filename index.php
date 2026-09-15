<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ແກ້ໄຂ Error Warning: Undefined variable $userRole
// ດຶງຄ່າ role ຈາກ Session (ຖ້າບໍ່ມີໃຫ້ຄ່າເລີ່ມຕົ້ນເປັນ 'guest' ຫຼື ຄ່າຫວ່າງ)
$userRole = $_SESSION['user_role'] ?? $_SESSION['role'] ?? '';

require_once __DIR__ . '/process.php';

function formatKip(float $amount): string {
    return number_format($amount, 0, '.', ',') . ' ກີບ';
}

function e(?string $str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລະບົບຈັດການການເງິນຂອງວັດ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { lao: ['"Noto Sans Lao"', 'sans-serif'] },
                    colors: {
                        saffron: {
                            50: '#fff8ed', 100: '#ffefd4', 200: '#ffdba8', 300: '#ffbf71',
                            400: '#ff9838', 500: '#ff7a14', 600: '#f05c0a', 700: '#c7430b',
                            800: '#9e3611', 900: '#7f2e12',
                        },
                        cream: '#fdf6e3',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; }
        .bg-temple { background: linear-gradient(135deg, #fdf6e3 0%, #fff8ed 50%, #ffefd4 100%); min-height: 100vh; }
        .card-shadow { box-shadow: 0 10px 25px -5px rgba(240, 92, 10, 0.15), 0 8px 10px -6px rgba(240, 92, 10, 0.1); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); }
        @media print {
            header, footer, .print\:hidden, #transactionModal, td:last-child, th:last-child { display: none !important; }
            body { background: white !important; color: black !important; }
            main { padding: 0 !important; margin: 0 !important; }
            .card-shadow { box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-temple font-lao text-gray-800">

    <header class="bg-gradient-to-r from-saffron-600 via-saffron-500 to-saffron-400 shadow-lg print:hidden">
        <div class="container mx-auto px-4 py-5 flex items-center gap-4">
            <img src="logo.jpeg" alt="ໂລໂກ້ວັດ" class="w-16 h-16 md:w-24 md:h-24 rounded-full border-4 border-white shadow-md object-cover">
            <div class="text-white">
                <h1 class="text-xl md:text-3xl font-extrabold drop-shadow-md">ລະບົບຈັດການລາຍຮັບ-ລາຍຈ່າຍຂອງວັດ</h1>
                <p class="text-sm md:text-base opacity-90 mt-1">Temple Finance Dashboard</p>
                <nav class="flex flex-wrap gap-2 text-sm font-medium mt-2">
                    <a href="index.php" class="px-3 py-1.5 rounded-lg bg-saffron-700 hover:bg-saffron-800 transition">📊 Dashboard</a>
                    <a href="register.php" class="px-3 py-1.5 rounded-lg hover:bg-saffron-700 transition">📝 ລົງທະບຽນ</a>
                    <a href="login.php" class="px-3 py-1.5 rounded-lg hover:bg-saffron-700 transition">🔑 ເຂົ້າສູ່ລະບົບ</a>
                    <a href="income.php" class="px-3 py-1.5 rounded-lg hover:bg-saffron-700 transition">💰 ລາຍຮັບ</a>
                    <a href="expense.php" class="px-3 py-1.5 rounded-lg hover:bg-saffron-700 transition">💸 ລາຍຈ່າຍ</a>
                    <a href="profile.php" class="px-3 py-1.5 rounded-lg bg-saffron-700 hover:bg-saffron-800 transition">👤 ໂປຣຟາຍ</a>
                    <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 transition">🚪 ອອກລະບົບ</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">

        <?php if (!empty($flash)): ?>
            <div class="mb-6 p-4 rounded-xl shadow-md <?= $flash['type'] === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-800' : 'bg-red-100 border-l-4 border-red-500 text-red-800' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 print:hidden">
            <div class="card-hover card-shadow bg-gradient-to-br from-amber-400 via-saffron-500 to-saffron-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-lg font-semibold opacity-90">ລາຍຮັບລວມ</span>
                    <span class="text-3xl">💰</span>
                </div>
                <p class="text-3xl md:text-4xl font-bold drop-shadow"><?= formatKip($totalIncome) ?></p>
                <p class="text-sm mt-2 opacity-80">ລວມລາຍຮັບທັງຫມົດ</p>
            </div>

            <div class="card-hover card-shadow bg-gradient-to-br from-saffron-600 via-red-500 to-red-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-lg font-semibold opacity-90">ລວມລາຍຈ່າຍທັງຫມົດ</span>
                    <span class="text-3xl">💸</span>
                </div>
                <p class="text-3xl md:text-4xl font-bold drop-shadow"><?= formatKip($totalExpense) ?></p>
                <p class="text-sm mt-2 opacity-80">ຄ່າໃຊ້ຈ່າຍຕ່າງໆ</p>
            </div>

            <div class="card-hover card-shadow bg-gradient-to-br from-yellow-400 via-saffron-400 to-saffron-500 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-lg font-semibold opacity-90">ລວມຍອດຄົງເຫຼືອ</span>
                    <span class="text-3xl">🏛️</span>
                </div>
                <p class="text-3xl md:text-4xl font-bold drop-shadow"><?= formatKip($balance) ?></p>
                <p class="text-sm mt-2 opacity-80"><?= $balance >= 0 ? 'ສະຖານະ: ປົກກະຕິ ✓' : 'ສະຖານະ: ຂາດດຸນ ⚠️' ?></p>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10 print:hidden">
            <div class="card-shadow bg-white rounded-2xl p-6 border-t-4 border-saffron-500 lg:col-span-2">
                <h2 class="text-xl font-bold text-saffron-700 mb-4 flex items-center gap-2">
                    <span class="text-2xl">📊</span> ລາຍຮັບ-ລາຍຈ່າຍ ລາຍເດືອນ (2026)
                </h2>
                <div class="relative" style="height: 320px;">
                    <canvas id="monthlyBarChart"></canvas>
                </div>
            </div>

            <div class="card-shadow bg-white rounded-2xl p-6 border-t-4 border-saffron-500">
                <h2 class="text-xl font-bold text-saffron-700 mb-4 flex items-center gap-2">
                    <span class="text-2xl">🍩</span> ສັດສ່ວນລາຍຮັບ vs ລາຍຈ່າຍ
                </h2>
                <div class="relative flex justify-center" style="height: 320px;">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>
        </section>

        <section class="card-shadow bg-white rounded-2xl p-6 border-t-4 border-saffron-500">
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-saffron-700 flex items-center justify-center gap-2 m-0">
            <span>📜</span> ປະຫວັດການເງິນລາຍຮັບ - ລາຍຈ່າຍ
        </h2>
        <p id="printMonthSubtitle" class="hidden print:block text-sm text-gray-600 mt-1 font-semibold ">
            ລາຍງານ: ທຸກໆເດືອນ
        </p>
    </div>

    <!-- แถบ Filter และ ปรับปุ่ม Export ให้เข้ากับโทนสี -->
    <div class="bg-white p-4 rounded-2xl card-shadow mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end print:hidden">
        <div>
            <label class="block text-sm font-semibold text-saffron-700 mb-1">🔍 ຄົ້ນຫາລາຍການ</label>
            <input type="text" id="searchInput" oninput="filterTransactions()" placeholder="ພິມຄຳຄົ້ນຫາ..."
                   class="w-full px-4 py-2.5 rounded-lg border-2 border-saffron-200 focus:border-saffron-500 outline-none text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold text-saffron-700 mb-1">🏷️ ປະເພດ</label>
            <select id="filterType" onchange="filterTransactions()" class="w-full px-4 py-2.5 rounded-lg border-2 border-saffron-200 focus:border-saffron-500 outline-none text-sm bg-white">
                <option value="all">ທັງໝົດ</option>
                <option value="income">+ ລາຍຮັບ</option>
                <option value="expense">- ລາຍຈ່າຍ</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-saffron-700 mb-1">📅 ແຍກຕາມເດືອນ</label>
            <select id="filterMonth" onchange="filterTransactions()" class="w-full px-4 py-2.5 rounded-lg border-2 border-saffron-200 focus:border-saffron-500 outline-none text-sm bg-white">
                <option value="all">ທຸກໆເດືອນ</option>
                <option value="-01-">ເດືອນ 1 (Jan)</option>
                <option value="-02-">ເດືອນ 2 (Feb)</option>
                <option value="-03-">ເດືອນ 3 (Mar)</option>
                <option value="-04-">ເດືອນ 4 (Apr)</option>
                <option value="-05-">ເດືອນ 5 (May)</option>
                <option value="-06-">ເດືອນ 6 (Jun)</option>
                <option value="-07-">ເດືອນ 7 (Jul)</option>
                <option value="-08-">ເດືອນ 8 (Aug)</option>
                <option value="-09-">ເດືອນ 9 (Sep)</option>
                <option value="-10-">ເດືອນ 10 (Oct)</option>
                <option value="-11-">ເດືອນ 11 (Nov)</option>
                <option value="-12-">ເດືອນ 12 (Dec)</option>
            </select>
        </div>
        <div>
            <button onclick="printPDF()" class="w-full bg-gradient-to-r from-amber-500 to-saffron-600 hover:from-amber-600 hover:to-saffron-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-md flex items-center justify-center gap-2 transition duration-200">
                🖨️ Export ເປັນ PDF
            </button>
        </div>
        
        <!-- ຊ່ອນປຸ່ມບັນທຶກ ຖ້າ Role ເປັນ viewer -->
        <?php if ($userRole !== 'viewer'): ?>
        <div class="flex gap-2 justify-end">
            <button onclick="openModal('income')" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-3 py-2.5 rounded-lg text-sm font-bold shadow-md transition duration-200">
                + ລາຍຮັບ
            </button>
            <button onclick="openModal('expense')" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-3 py-2.5 rounded-lg text-sm font-bold shadow-md transition duration-200">
                - <b>ລາຍຈ່າຍ</b>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- ຕາຕະລາງສະແດງຂໍ້ມູນ -->
    <div class="overflow-x-auto">
        <table class="w-full text-left" id="transactionTable">
            <thead class="bg-gradient-to-r from-saffron-500 to-saffron-600 text-white">
                <tr>
                    <th class="px-4 py-3 rounded-tl-lg">#</th>
                    <th class="px-4 py-3">ປະເພດ</th>
                    <th class="px-4 py-3">ລາຍລະອຽດ</th>
                    <th class="px-4 py-3">🧾 ໃບບິນ</th>
                    <th class="px-4 py-3 text-right">ຈຳນວນເງິນ</th>
                    <th class="px-4 py-3">ວັນທີ</th>
                    <?php if ($userRole !== 'viewer'): ?>
                        <th class="px-4 py-3 rounded-tr-lg text-center print:hidden">ຈັດການ</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="<?= $userRole !== 'viewer' ? '7' : '6' ?>" class="text-center py-8 text-gray-500">
                            ⚠️ ຍັງບໍ່ມີລາຍການ
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $i => $tx): ?>
                        <tr class="transaction-row border-b border-saffron-100 hover:bg-saffron-50 transition" 
                            data-type="<?= e($tx['type']) ?>" 
                            data-date="<?= e(date('Y-m-d', strtotime($tx['date_added']))) ?>"
                            data-amount="<?= (float)$tx['amount'] ?>"
                            data-details="<?= e(mb_strtolower($tx['details'], 'UTF-8')) ?>">
                            
                            <td class="px-4 py-3 font-medium text-gray-600"><?= $i + 1 ?></td>
                            <td class="px-4 py-3">
                                <?php if ($tx['type'] === 'income'): ?>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-300">⬆️ ລາຍຮັບ</span>
                                <?php else: ?>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-300">⬇️ ລາຍຈ່າຍ</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 details-field"><?= e($tx['details']) ?></td>
                            
                            <!-- ປັບປຸງປຸ່ມເບິ່ງຮູບໃບບິນ -->
                            <td class="px-4 py-3">
                                <?php if (!empty($tx['receipt_image'])): ?>
                                    <button type="button" onclick="viewReceiptImage('uploads/<?= e($tx['receipt_image']) ?>')" class="focus:outline-none">
                                        <img src="uploads/<?= e($tx['receipt_image']) ?>" alt="ໃບບິນ" class="w-10 h-10 object-cover rounded-lg border border-saffron-300 hover:scale-110 hover:shadow-md transition cursor-pointer">
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">❌ ບໍ່ມີຮູບ</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3 text-right font-bold <?= $tx['type'] === 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                <?= $tx['type'] === 'income' ? '+' : '-' ?> <?= formatKip((float)$tx['amount']) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600"><?= e(date('d/m/Y H:i', strtotime($tx['date_added']))) ?></td>
                            
                            <!-- ເເກ້ໄຂ Tag ປຸ່ມ Delete ໃຫ້ສົມບູນ -->
                            <?php if ($userRole !== 'viewer'): ?>
                            <td class="px-4 py-3 text-center flex justify-center gap-2 print:hidden">
                                <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($tx), ENT_QUOTES, 'UTF-8') ?>)" class="bg-blue-500 hover:bg-blue-600 text-white p-1.5 rounded transition shadow-sm" title="ແກ້ໄຂ">✏️</button>
                                <a href="process.php?delete_id=<?= $tx['id'] ?>" onclick="return confirm('ທ່ານຕ້ອງການລຶບລາຍການນີ້ແທ້ບໍ່?')" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded transition shadow-sm" title="ລຶບ">🗑️</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls (ປຸ່ມແບ່ງໜ້າ) -->
    <div id="paginationControls"></div>
</section>

<!-- Modal ສະແດງ ແລະ ດາວໂຫຼດຮູບໃບບິນ -->
<div id="imagePreviewModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border-t-8 border-saffron-500">
        <div class="p-4 bg-saffron-50 flex justify-between items-center border-b border-saffron-200">
            <h3 class="font-bold text-saffron-800 flex items-center gap-2">
                <span>🧾</span> ຫຼັກຖານໃບບິນ
            </h3>
            <button type="button" onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>
        <div class="p-4 text-center bg-gray-100 flex justify-center items-center min-h-[250px]">
            <img id="modalPreviewImg" src="" alt="ໃບບິນ" class="max-h-96 rounded-lg shadow border border-gray-300 object-contain">
        </div>
        <div class="p-4 flex gap-3 bg-white">
            <button type="button" onclick="closeImageModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl transition">
                ປິດໜ້າຕ່າງ
            </button>
            <a id="downloadImgBtn" href="" download class="flex-1 bg-gradient-to-r from-saffron-500 to-saffron-600 hover:from-saffron-600 hover:to-saffron-700 text-white font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                📥 ດາວໂຫຼດຮູບ
            </a>
        </div>
    </div>
</div>

    <footer class="mt-10 py-6 bg-gradient-to-r from-saffron-700 to-saffron-900 text-center text-white print:hidden">
        <p class="text-sm">🛕 © <?= date('Y') ?> ລະບົບຈັດການການເງິນຂອງວັດ - ສ້າງດ້ວຍ ❤️ ເພື່ອພຸດທະສາສະນາ</p>
    </footer>

    <?php if ($userRole !== 'viewer'): ?>
    <div id="transactionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border-t-8 border-saffron-500 overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-lg font-bold text-saffron-800">📝 ບັນທຶກລາຍການ</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>
                
                <form action="process.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" id="tx_id" name="id" value="">
                    <input type="hidden" id="tx_type" name="type" value="income">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ວັນທີ</label>
                        <input type="date" id="tx_date" name="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none">
                    </div>
                    <div> 
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ລາຍລະອຽດ</label>
                        <input type="text" id="tx_details" name="details" required placeholder="ຄ່ານ້ຳ, ຄ່າໄຟ..." class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ຈຳນວນເງິນ (ກີບ)</label>
                        <input type="number" id="tx_amount" name="amount" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">🧾 ແນບຮູບພາບໃບບິນ</label>
                        <input type="file" id="tx_image" name="receipt_image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 py-2 rounded-lg font-medium">ຍົກເລີກ</button>
                        <button type="submit" class="flex-1 bg-saffron-600 text-white py-2 rounded-lg font-medium shadow-md">💾 ບັນທຶກຂໍ້ມູນ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        let monthlyChartInstance = null;
        let donutChartInstance = null;

        document.addEventListener("DOMContentLoaded", function() {
            renderCharts();
        });

        function renderCharts() {
            const rows = document.querySelectorAll('.transaction-row');
            
            let monthlyIncome = Array(12).fill(0);
            let monthlyExpense = Array(12).fill(0);
            let totalInc = 0;
            let totalExp = 0;

            rows.forEach(row => {
                if (!row.classList.contains('hidden')) {
                    const type = row.getAttribute('data-type');
                    const dateStr = row.getAttribute('data-date');
                    const amount = parseFloat(row.getAttribute('data-amount')) || 0;

                    const dateObj = new Date(dateStr);
                    const monthIndex = dateObj.getMonth();

                    if (monthIndex >= 0 && monthIndex < 12) {
                        if (type === 'income') {
                            monthlyIncome[monthIndex] += amount;
                            totalInc += amount;
                        } else if (type === 'expense') {
                            monthlyExpense[monthIndex] += amount;
                            totalExp += amount;
                        }
                    }
                }
            });

            const ctxBar = document.getElementById('monthlyBarChart').getContext('2d');
            if (monthlyChartInstance) { monthlyChartInstance.destroy(); }
            
            monthlyChartInstance = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'ລາຍຮັບ',
                            data: monthlyIncome,
                            backgroundColor: '#d9531e',
                            borderRadius: 3,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'ລາຍຈ່າຍ',
                            data: monthlyExpense,
                            backgroundColor: '#ceb175',
                            borderRadius: 3,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { font: { family: 'Noto Sans Lao' } } }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { 
                            beginAtZero: true,
                            ticks: { callback: function(value) { return value.toLocaleString(); } }
                        }
                    }
                }
            });

            const ctxDonut = document.getElementById('financeChart').getContext('2d');
            if (donutChartInstance) { donutChartInstance.destroy(); }

            donutChartInstance = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['ລາຍຮັບ', 'ລາຍຈ່າຍ'],
                    datasets: [{
                        data: [totalInc, totalExp],
                        backgroundColor: ['#f05c0a', '#ef4444'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { family: 'Noto Sans Lao' } } }
                    }
                }
            });
        }

        function filterTransactions() {
            const searchVal = document.getElementById('searchInput').value.toLowerCase();
            const typeVal = document.getElementById('filterType').value;
            const monthVal = document.getElementById('filterMonth').value;
            const rows = document.querySelectorAll('.transaction-row');

            rows.forEach(row => {
                const rowType = row.getAttribute('data-type');
                const rowDate = row.getAttribute('data-date');
                const rowDetails = row.getAttribute('data-details');

                const matchSearch = rowDetails.includes(searchVal);
                const matchType = (typeVal === 'all' || rowType === typeVal);
                const matchMonth = (monthVal === 'all' || rowDate.includes(monthVal));

                if (matchSearch && matchType && matchMonth) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });

            renderCharts();

            const selectMonthText = document.getElementById('filterMonth').options[document.getElementById('filterMonth').selectedIndex].text;
            document.getElementById('printMonthSubtitle').innerText = "ລາຍງານປະຈຳ: " + selectMonthText;
        }

        function openModal(type) {
            document.getElementById('tx_id').value = '';
            document.getElementById('tx_type').value = type;
            document.getElementById('tx_details').value = '';
            document.getElementById('tx_amount').value = '';
            const modalTitle = document.getElementById('modalTitle');
            modalTitle.innerHTML = type === 'income' ? '🟢 ບັນທຶກລາຍຮັບໃໝ່' : '🔴 ບັນທຶກລາຍຈ່າຍໃໝ່';
            document.getElementById('transactionModal').classList.remove('hidden');
        }

        function openEditModal(tx) {
            document.getElementById('tx_id').value = tx.id;
            document.getElementById('tx_type').value = tx.type;
            document.getElementById('tx_date').value = tx.date_added.substring(0, 10);
            document.getElementById('tx_details').value = tx.details;
            document.getElementById('tx_amount').value = Math.floor(tx.amount);
            document.getElementById('modalTitle').innerHTML = '✏️ ແກ້ໄຂລາຍການ';
            document.getElementById('transactionModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        }

        function printPDF() {
            const originalTitle = document.title;
            const selectMonthText = document.getElementById('filterMonth').options[document.getElementById('filterMonth').selectedIndex].text;
            document.title = "ລາຍງານ_" + selectMonthText.replace(" ", "_");
            window.print();
            document.title = originalTitle;
        }

        // --- Modal ຈັດການເບິ່ງຮູບ ແລະ ດາວໂຫຼດ ---
function viewReceiptImage(imageSrc) {
    const modal = document.getElementById('imagePreviewModal');
    const img = document.getElementById('modalPreviewImg');
    const downloadBtn = document.getElementById('downloadImgBtn');

    img.src = imageSrc;
    downloadBtn.href = imageSrc;
    modal.classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imagePreviewModal').classList.add('hidden');
}

// --- ລະບົບ Pagination (10 ລາຍການ/ໜ້າ) ---
let currentPage = 1;
const rowsPerPage = 10;

function renderPagination() {
    const allRows = Array.from(document.querySelectorAll('.transaction-row'));
    const visibleRows = allRows.filter(row => !row.classList.contains('filter-hidden'));
    
    const totalPages = Math.ceil(visibleRows.length / rowsPerPage) || 1;
    if (currentPage > totalPages) currentPage = totalPages;

    visibleRows.forEach((row, index) => {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        if (index >= start && index < end) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    const paginationContainer = document.getElementById('paginationControls');
    if (paginationContainer) {
        paginationContainer.innerHTML = `
            <div class="flex items-center justify-between mt-4 px-2 print:hidden">
                <span class="text-xs text-gray-500 font-semibold">ໜ້າທີ ${currentPage} ຈາກທັງໝົດ ${totalPages} ໜ້າ</span>
                <div class="flex gap-1">
                    <button type="button" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="px-3 py-1.5 bg-saffron-100 text-saffron-800 rounded-lg disabled:opacity-50 text-xs font-bold hover:bg-saffron-200 transition">← ກ່ອນໜ້າ</button>
                    <button type="button" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="px-3 py-1.5 bg-saffron-100 text-saffron-800 rounded-lg disabled:opacity-50 text-xs font-bold hover:bg-saffron-200 transition">ຖັດໄປ →</button>
                </div>
            </div>
        `;
    }
}

function changePage(page) {
    currentPage = page;
    renderPagination();
}

// --- Filter Transactions ຮ່ວມກັບ Pagination ---
function filterTransactions() {
    const searchVal = document.getElementById('searchInput').value.toLowerCase();
    const typeVal = document.getElementById('filterType').value;
    const monthVal = document.getElementById('filterMonth') ? document.getElementById('filterMonth').value : 'all';
    const rows = document.querySelectorAll('.transaction-row');

    rows.forEach(row => {
        const rowType = row.getAttribute('data-type');
        const rowDate = row.getAttribute('data-date');
        const rowDetails = row.getAttribute('data-details');

        const matchSearch = rowDetails.includes(searchVal);
        const matchType = (typeVal === 'all' || rowType === typeVal);
        const matchMonth = (monthVal === 'all' || rowDate.includes(monthVal));

        if (matchSearch && matchType && matchMonth) {
            row.classList.remove('filter-hidden');
        } else {
            row.classList.add('filter-hidden');
            row.style.display = 'none';
        }
    });

    currentPage = 1;
    renderPagination();
}

// --- Print PDF ---
function printPDF() {
    const rows = document.querySelectorAll('.transaction-row');
    rows.forEach(r => {
        if (!r.classList.contains('filter-hidden')) r.style.display = '';
    });

    window.print();
    renderPagination();
}

// Render ເມື່ອ DOM loaded
document.addEventListener("DOMContentLoaded", function() {
    renderPagination();
});
    </script>
</body>
</html>