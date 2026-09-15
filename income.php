<?php
/**
 * =====================================================
 * ໄຟລ໌: income.php
 * ໜ້າທີ່: ໜ້າຈັດການປະຫວັດລາຍຮັບສະເພາະ
 * =====================================================
 */

require_once __DIR__ . '/process.php';

function formatKip(float $amount): string {
    return number_format($amount, 0, '.', ',') . ' ກີບ';
}

function e(?string $str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// ດຶງຂໍ້ມູນສະເພາະ "ລາຍຮັບ" (income)
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE type = 'income' ORDER BY date_added DESC");
$stmt->execute();
$incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ຄິດໄລ່ລວມລາຍຮັບທັງໝົດ
$totalIncome = 0;
foreach ($incomes as $inc) {
    $totalIncome += (float)$inc['amount'];
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການລາຍຮັບ - ລະບົບວັດໂນນໄຮຄຳຈັນທະມາຮາມ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;600;700;800&family=Phetsarath&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Lao', 'Phetsarath', sans-serif; }
        .bg-temple { background: linear-gradient(135deg, #fdf6e3 0%, #fff8ed 50%, #ffefd4 100%); min-height: 100vh; }
        .card-shadow { box-shadow: 0 10px 25px -5px rgba(240, 92, 10, 0.15); }
    </style>
</head>
<body class="bg-temple text-gray-800 flex flex-col justify-between">

    <!-- Header -->
    <header class="bg-gradient-to-r from-amber-600 via-orange-500 to-amber-500 shadow-lg text-white">
        <div class="container mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="logo.jpeg" alt="Logo" class="w-14 h-14 rounded-full border-2 border-white object-cover shadow" onerror="this.src='https://via.placeholder.com/60'">
                <div>
                    <h1 class="text-xl font-bold">🟢 ປະຫວັດ ແລະ ຈັດການລາຍຮັບ</h1>
                    <p class="text-xs opacity-90">ວັດໂນນໄຮຄຳຈັນທະມາຮາມ</p>
                </div>
            </div>
            <nav class="flex gap-2 text-sm font-medium">
                <a href="index.php" class="px-3 py-1.5 rounded-lg bg-amber-700 hover:bg-amber-800 transition">📊 Dashboard</a>
                <a href="income.php" class="px-3 py-1.5 rounded-lg bg-green-700 font-bold transition">🟢 ລາຍຮັບ</a>
                <a href="expense.php" class="px-3 py-1.5 rounded-lg bg-amber-700 hover:bg-amber-800 transition">🔴 ລາຍຈ່າຍ</a>
                <a href="profile.php" class="px-3 py-1.5 rounded-lg bg-amber-700 hover:bg-amber-800 transition">👤 ໂປຣຟາຍ</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 flex-grow">
        <!-- ບັດສະຫຼຸບຍອດລວມລາຍຮັບ -->
        <div class="mb-6 card-shadow bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white flex justify-between items-center">
            <div>
                <p class="text-sm font-semibold opacity-90">ລວມຍອດລາຍຮັບທັງໝົດ</p>
                <h2 class="text-3xl font-extrabold mt-1"><?= formatKip($totalIncome) ?></h2>
            </div>
            <?php if ($userRole !== 'viewer'): ?>
            <button onclick="openModal('income')" class="bg-white text-green-700 hover:bg-green-50 font-bold px-5 py-3 rounded-xl shadow-md transition flex items-center gap-2">
                ➕ ເພີ່ມລາຍຮັບໃໝ່
            </button>
            <?php endif; ?>
        </div>

        <!-- ຕາຕະລາງປະຫວັດລາຍຮັບ -->
        <div class="card-shadow bg-white rounded-2xl p-6 border-t-4 border-green-500">
            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span>📜</span> ຕາຕະລາງປະຫວັດລາຍຮັບທັງໝົດ
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">#</th>
                            <th class="px-4 py-3">ລາຍລະອຽດ</th>
                            <th class="px-4 py-3">🧾 ໃບບິນ</th>
                            <th class="px-4 py-3 text-right">ຈຳນວນເງິນ</th>
                            <th class="px-4 py-3">ວັນທີ</th>
                            <?php if ($userRole !== 'viewer'): ?>
                            <th class="px-4 py-3 rounded-tr-lg text-center">ຈັດການ</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                        <?php if (empty($incomes)): ?>
                            <tr>
                                <td colspan="<?= $userRole !== 'viewer' ? '6' : '5' ?>" class="text-center py-8 text-gray-400">
                                    ⚠️ ຍັງບໍ່ມີປະຫວັດລາຍຮັບ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($incomes as $i => $tx): ?>
                                <tr class="hover:bg-green-50 transition">
                                    <td class="px-4 py-3 font-medium"><?= $i + 1 ?></td>
                                    <td class="px-4 py-3 font-semibold text-gray-800"><?= e($tx['details']) ?></td>
                                    <td class="px-4 py-3">
                                        <?php if (!empty($tx['receipt_image'])): ?>
                                            <button type="button" onclick="viewReceiptImage('uploads/<?= e($tx['receipt_image']) ?>')" class="focus:outline-none">
                                                <img src="uploads/<?= e($tx['receipt_image']) ?>" alt="ໃບບິນ" class="w-10 h-10 object-cover rounded-lg border border-green-300 hover:scale-110 transition shadow-sm cursor-pointer">
                                            </button>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">❌ ບໍ່ມີຮູບ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-green-600">
                                        + <?= formatKip((float)$tx['amount']) ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500"><?= e(date('d/m/Y H:i', strtotime($tx['date_added']))) ?></td>
                                    
                                    <!-- ປຸ່ມຈັດການ (ປັບປຸງ UI ແລ້ວ) -->
                                    <?php if ($userRole !== 'viewer'): ?>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" onclick='openEditModal(<?= json_encode($tx, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 hover:border-transparent px-3 py-1.5 rounded-lg text-xs font-bold transition duration-200 flex items-center gap-1 shadow-sm">
                                                ✏️ ແກ້ໄຂ
                                            </button>
                                            <a href="process.php?delete_id=<?= $tx['id'] ?>" onclick="return confirm('ທ່ານຕ້ອງການລຶບລາຍການນີ້ແທ້ບໍ່?')" class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-transparent px-3 py-1.5 rounded-lg text-xs font-bold transition duration-200 flex items-center gap-1 shadow-sm">
                                                🗑️ ລຶບ
                                            </a>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal ເພີ່ມ/ແກ້ໄຂ (ປັບປຸງ UI ແລ້ວ) -->
    <div id="transactionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border-t-8 border-green-600 overflow-hidden">
            <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex justify-between items-center">
                <h3 id="modalTitle" class="text-lg font-bold text-green-800 flex items-center gap-2">
                    🟢 ບັນທຶກລາຍຮັບ
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none">&times;</button>
            </div>
            
            <form action="process.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" id="tx_id" name="id" value="">
                <input type="hidden" id="tx_type" name="type" value="income">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">📅 ວັນທີ</label>
                    <input type="date" id="tx_date" name="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">📝 ລາຍລະອຽດລາຍຮັບ</label>
                    <input type="text" id="tx_details" name="details" required placeholder="ເຊັ່ນ: ເຮັດບຸນບໍລິຈາກ, ເງິນຕັກບາດ..." class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">💰 ຈຳນວນເງິນ (ກີບ)</label>
                    <input type="number" id="tx_amount" name="amount" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">🧾 ແນບຮູບໃບບິນ/ຫຼັກຖານ (ຖ້າມີ)</label>
                    <input type="file" id="tx_image" name="receipt_image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                </div>
                
                <div class="flex gap-2 pt-4">
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-xl font-bold transition">ຍົກເລີກ</button>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-2.5 rounded-xl font-bold shadow-md transition">💾 ບັນທຶກ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal ເບິ່ງ ແລະ ດາວໂຫຼດຮູບໃບບິນ -->
    <div id="imagePreviewModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border-t-8 border-green-600">
            <div class="p-4 bg-green-50 flex justify-between items-center border-b border-green-200">
                <h3 class="font-bold text-green-800 flex items-center gap-2">
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
                <a id="downloadImgBtn" href="" download class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    📥 ດາວໂຫຼດຮູບ
                </a>
            </div>
        </div>
    </div>

    <script>
        function openModal(type) {
            document.getElementById('tx_id').value = '';
            document.getElementById('tx_type').value = type;
            document.getElementById('tx_details').value = '';
            document.getElementById('tx_amount').value = '';
            
            // ກຳນົດວັນທີປັດຈຸບັນ
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tx_date').value = today;

            document.getElementById('modalTitle').innerHTML = '🟢 ບັນທຶກລາຍຮັບໃໝ່';
            document.getElementById('transactionModal').classList.remove('hidden');
        }

        function openEditModal(tx) {
            document.getElementById('tx_id').value = tx.id;
            document.getElementById('tx_type').value = tx.type;
            
            if (tx.date_added) {
                document.getElementById('tx_date').value = tx.date_added.substring(0, 10);
            }
            
            document.getElementById('tx_details').value = tx.details;
            document.getElementById('tx_amount').value = Math.floor(tx.amount);
            
            document.getElementById('modalTitle').innerHTML = '✏️ ແກ້ໄຂລາຍຮັບ';
            document.getElementById('transactionModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        }

        // ຈັດການ Modal ເບິ່ງ/ດາວໂຫຼດຮູບ
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
    </script>

</body>
</html>