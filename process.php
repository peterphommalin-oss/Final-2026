<?php
/**
 * =====================================================
 * ໄຟລ໌: process.php
 * ໜ້າທີ່: ປະມວນຜົນ Backend
 *   1) ຮັບຂໍ້ມູນຈາກຟອມ → INSERT ເຂົ້າຖານຂໍ້ມູນ
 *   2) ດຶງຂໍ້ມູນທັງໝົດ (ປະຫວັດ + ສະຫຼຸບຍອດ)
 *      ໃຫ້ index.php ນຳໄປສະແດງ
 * =====================================================
 *
 * ໄຟລ໌ນີ້ຈະຖືກ require ຈາກ index.php
 * ຫຼື ຮັບ POST ໂດຍກົງເມື່ອຟອມຖືກສົ່ງ
 */

// ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ → ໄດ້ຕົວແປ $pdo ມາໃຊ້
require_once __DIR__ . '/connect.php';

// ເລີ່ມ session ສຳລັບການສະແດງຂໍ້ຄວາມ flash (ສຳເລັດ / ບໍ່ສຳເລັດ)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userRole = $_SESSION['role'] ?? 'viewer';

// ປ້ອງກັນບໍ່ໃຫ້ Viewer ລຶບຂໍ້ມູນໄດ້
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    if ($userRole === 'viewer') {
        $_SESSION['flash'] = ['type' => 'error', 'message' => '⚠️ ທ່ານບໍ່ມີສິດໃນການລຶບຂໍ້ມູນ!'];
        header("Location: index.php");
        exit();
    }
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id");
    $stmt->execute([':id' => $delete_id]);
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'ລຶບລາຍການສຳເລັດແລ້ວ!'];
    header("Location: index.php");
    exit();
}

// ປ້ອງກັນບໍ່ໃຫ້ Viewer ເພີ່ມ ຫຼື ແກ້ໄຂຂໍ້ມູນໄດ້
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
    if ($userRole === 'viewer') {
        $_SESSION['flash'] = ['type' => 'error', 'message' => '⚠️ ທ່ານບໍ່ມີສິດໃນການເພີ່ມ ຫຼື ແກ້ໄຂຂໍ້ມູນ!'];
        header("Location: index.php");
        exit();
    }
    
    // ... ໂຄດການ Save / Update ເດີມ ...
}

/* -----------------------------------------------------
 * ສ່ວນທີ່ 1: ປະມວນຜົນຟອມ (INSERT)
 * ----------------------------------------------------- */

// ກວດສອບວ່າ request method ເປັນ POST ແລະ ມີ field 'type' ສົ່ງມາ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {

    // 1.1) ດຶງຄ່າຈາກຟອມ ພ້ອມຕັດຊ່ອງວ່າງ (trim)
    $type    = trim($_POST['type']    ?? '');
    $details = trim($_POST['details'] ?? '');
    $amount  = trim($_POST['amount']  ?? '');

    // 1.2) ກວດສອບຄວາມຖືກຕ້ອງຂອງຂໍ້ມູນ (Validation)
    $errors = [];

    // ປະເພດຕ້ອງເປັນ income ຫຼື expense ເທົ່ານັ້ນ
    if (!in_array($type, ['income', 'expense'], true)) {
        $errors[] = 'ກະລຸນາເລືອກປະເພດ (ລາຍຮັບ ຫຼື ລາຍຈ່າຍ)';
    }

    // ລາຍລະອຽດຫ້າມຫວ່າງ ແລະ ບໍ່ເກີນ 255 ໂຕ
    if ($details === '' || mb_strlen($details) > 255) {
        $errors[] = 'ກະລຸນາປ້ອນລາຍລະອຽດ (1-255 ໂຕອັກສອນ)';
    }

    // ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ ແລະ ຫຼາຍກວ່າ 0
    if (!is_numeric($amount) || (float)$amount <= 0) {
        $errors[] = 'ກະລຸນາປ້ອນຈຳນວນເງິນເປັນຕົວເລກ ແລະ ຫຼາຍກວ່າ 0';
    }

    // 1.3) ຖ້າບໍ່ມີ error → INSERT
    if (empty($errors)) {
        try {
            // ໃຊ້ Prepared Statement ປ້ອງກັນ SQL Injection
            $sql = "INSERT INTO transactions (type, details, amount, date_added)
                    VALUES (:type, :details, :amount, NOW())";

            $stmt = $pdo->prepare($sql);

            // bind parameter ພ້ອມລະບຸ type ຂອງຂໍ້ມູນ
            $stmt->bindValue(':type',    $type,             PDO::PARAM_STR);
            $stmt->bindValue(':details', $details,          PDO::PARAM_STR);
            $stmt->bindValue(':amount',  (float)$amount);   // DECIMAL

            $stmt->execute();

            // ບັນທຶກຂໍ້ຄວາມສຳເລັດ ໄວ້ສະແດງຫຼັງ redirect
            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => '✅ ບັນທຶກລາຍການສຳເລັດ!'
            ];
        } catch (PDOException $e) {
            $_SESSION['flash'] = [
                'type'    => 'error',
                'message' => '❌ ບັນທຶກບໍ່ສຳເລັດ: ' . $e->getMessage()
            ];
        }
    } else {
        // ມີ validation error → ເກັບໄວ້ສະແດງ
        $_SESSION['flash'] = [
            'type'    => 'error',
            'message' => '⚠️ ' . implode(' | ', $errors)
        ];
    }

    // 1.4) Redirect ກັບໄປໜ້າຫຼັກ (PRG Pattern: Post-Redirect-Get)
    //      ປ້ອງກັນການ submit ຊ້ຳເມື່ອ user refresh
    header('Location: index.php');
    exit;
}

/* -----------------------------------------------------
 * ສ່ວນທີ່ 2: ຄຳນວນສະຫຼຸບ ແລະ ດຶງປະຫວັດ
 * (ສ່ວນນີ້ຈະຖືກເອີ້ນເມື່ອ index.php require ໄຟລ໌ນີ້)
 * ----------------------------------------------------- */

/**
 * ຄຳນວນຍອດລວມຕາມປະເພດ
 * @param PDO $pdo
 * @param string $type 'income' ຫຼື 'expense'
 * @return float
 */
function getTotalByType(PDO $pdo, string $type): float {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS total
                           FROM transactions
                           WHERE type = :type");
    $stmt->execute([':type' => $type]);
    return (float) $stmt->fetchColumn();
}

/**
 * ດຶງປະຫວັດການເງິນທັງໝົດ ຈັດລຽງຈາກໃໝ່ → ເກົ່າ
 * @param PDO $pdo
 * @param int $limit ຈຳນວນທີ່ດຶງ (default 100)
 * @return array
 */
function getTransactions(PDO $pdo, int $limit = 100): array {
    $stmt = $pdo->prepare("SELECT id, type, details, amount, date_added
                           FROM transactions
                           ORDER BY date_added DESC, id DESC
                           LIMIT :lim");
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// ຄຳນວນຄ່າສຳລັບ Dashboard
$totalIncome  = getTotalByType($pdo, 'income');   // ລາຍຮັບລວມ
$totalExpense = getTotalByType($pdo, 'expense');  // ລາຍຈ່າຍລວມ
$balance      = $totalIncome - $totalExpense;     // ຍອດຄົງເຫຼືອ
$transactions = getTransactions($pdo);            // ປະຫວັດ

// ດຶງຂໍ້ຄວາມ flash (ຖ້າມີ) ແລ້ວລົບອອກຈາກ session
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// 1. ເພີ່ມສ່ວນລຶບຂໍ້ມູນ (GET Method) ໄວ້ດ້ານເທິງໆ ຂອງ process.php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id");
    $stmt->execute([':id' => $delete_id]);
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'ลบรายการสำเร็จแล้ว!'];
    header("Location: index.php");
    exit();
}

// 2. ປັບປຸງສ່ວນ INSERT ເດີມ ໃຫ້ຮອງຮັບ UPDATE ຖ້າມີການສົ່ງ ID ມາ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
    $id      = trim($_POST['id'] ?? '');
    $type    = trim($_POST['type'] ?? '');
    $details = trim($_POST['details'] ?? '');
    $amount  = trim($_POST['amount'] ?? '');

    if (!empty($details) && !empty($amount)) {
        if (!empty($id)) {
            // ກໍລະນີມີ ID ໃຫ້ເຮັດການ UPDATE
            $stmt = $pdo->prepare("UPDATE transactions SET type = :type, details = :details, amount = :amount WHERE id = :id");
            $stmt->execute([':type' => $type, ':details' => $details, ':amount' => $amount, ':id' => $id]);
        } else {
            // ກໍລະນີບໍ່ມີ ID ໃຫ້ INSERT ໃໝ່
            $stmt = $pdo->prepare("INSERT INTO transactions (type, details, amount) VALUES (:type, :details, :amount)");
            $stmt->execute([':type' => $type, ':details' => $details, ':amount' => $amount]);
        }
    }
    header("Location: index.php");
    exit();
}


// -------------------------------------------------------------
// 1. ຈັດການຂໍ້ມູນເງິນໂມທະນາ (Monetary Donation)
// -------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'save_donation') {
    $donar_name = trim($_POST['donar_name']);
    $amount = (float)$_POST['amount'];
    $donation_type = trim($_POST['donation_type']);
    $donation_date = $_POST['donation_date'];

    // ບັນທຶກລາຍຮັບ (Income) ກ່ອນ ເພື່ອເອົາ income_id
    $stmtInc = $pdo->prepare("INSERT INTO incomes (total_amount, date, detail) VALUES (?, ?, ?)");
    $stmtInc->execute([$amount, $donation_date, "ເງິນໂມທະນາຈາກ: " . $donar_name . " (" . $donation_type . ")"]);
    $income_id = $pdo->lastInsertId();

    // ບັນທຶກລົງ ຕາຕະລາງ donations
    $stmtDon = $pdo->prepare("INSERT INTO donations (donar_name, amount, donation_type, donation_date, income_id) VALUES (?, ?, ?, ?, ?)");
    $stmtDon->execute([$donar_name, $amount, $donation_type, $donation_date, $income_id]);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'ບັນທຶກຂໍ້ມູນເງິນໂມທະນາສຳເລັດ!'];
    header('Location: index.php');
    exit;
}

// -------------------------------------------------------------
// 2. ຈັດການຂໍ້ມູນວັດຖຸທານ (Material Offerings)
// -------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'save_material') {
    $material_name = trim($_POST['material_name']);
    $category = trim($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $unit = trim($_POST['unit']);
    $donor_name = trim($_POST['donor_name']);
    $received_date = $_POST['received_date'];
    $storage_location = trim($_POST['storage_location']);

    $stmtMat = $pdo->prepare("INSERT INTO material_offerings (material_offerings_name, category, quantity, unit, donor_name, received_date, storage_location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtMat->execute([$material_name, $category, $quantity, $unit, $donor_name, $received_date, $storage_location]);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'ບັນທຶກຂໍ້ມູນວັດຖຸທານສຳເລັດ!'];
    header('Location: index.php');
    exit;
}

// -------------------------------------------------------------
// 3. ຈັດການຂໍ້ມູນສັ່ງຈ່າຍ / ຂໍອະນຸມັດລາຍຈ່າຍ (Disbursement Request)
// -------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'save_expense_request') {
    $detail = trim($_POST['detail']);
    $amount = (float)$_POST['amount'];
    $date = $_POST['date'];
    
    $receipt_image = null;
    if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['receipt_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $newFileName = md5(time() . $_FILES['receipt_image']['name']) . '.' . $ext;
            if (!is_dir(__DIR__ . '/uploads')) { mkdir(__DIR__ . '/uploads', 0755, true); }
            move_uploaded_file($_FILES['receipt_image']['tmp_name'], __DIR__ . '/uploads/' . $newFileName);
            $receipt_image = $newFileName;
        }
    }

    $stmtExp = $pdo->prepare("INSERT INTO expenses (total_amount, date, detail, receipt_image, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmtExp->execute([$amount, $date, $detail, $receipt_image]);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'ສົ່ງຄຳຂໍສັ່ງຈ່າຍຮຽບຮ້ອຍແລ້ວ! (ລໍຖ້າການອະນຸມັດ)'];
    header('Location: index.php');
    exit;
}

// -------------------------------------------------------------
// 4. ການອະນຸມັດ/ປະຕິເສດ ໃບສັ່ງຈ່າຍ (Abbot / Admin Approval)
// -------------------------------------------------------------
if (isset($_GET['approve_exp_id'])) {
    $exp_id = (int)$_GET['approve_exp_id'];
    $stmtApp = $pdo->prepare("UPDATE expenses SET status = 'approved' WHERE id = ?");
    $stmtApp->execute([$exp_id]);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'ອະນຸມັດການສັ່ງຈ່າຍຮຽບຮ້ອຍແລ້ວ!'];
    header('Location: index.php');
    exit;
}

if (isset($_GET['reject_exp_id'])) {
    $exp_id = (int)$_GET['reject_exp_id'];
    $stmtRej = $pdo->prepare("UPDATE expenses SET status = 'rejected' WHERE id = ?");
    $stmtRej->execute([$exp_id]);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'ປະຕິເສດໃບສັ່ງຈ່າຍຮຽບຮ້ອຍແລ້ວ!'];
    header('Location: index.php');
    exit;
}

// -------------------------------------------------------------
// 5. Query ດຶງຂໍ້ມູນອອກໄປສະແດງໜ້າ Dashboard
// -------------------------------------------------------------
$totalIncome = $pdo->query("SELECT SUM(total_amount) FROM incomes")->fetchColumn() ?: 0;
$totalExpense = $pdo->query("SELECT SUM(total_amount) FROM expenses WHERE status = 'approved'")->fetchColumn() ?: 0;
$balance = $totalIncome - $totalExpense;

$donations = $pdo->query("SELECT * FROM donations ORDER BY donation_date DESC")->fetchAll();
$materials = $pdo->query("SELECT * FROM material_offerings ORDER BY received_date DESC")->fetchAll();
$expenses = $pdo->query("SELECT * FROM expenses ORDER BY date DESC")->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']); 