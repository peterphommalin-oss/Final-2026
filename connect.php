<?php
/**
 * =====================================================
 * ໄຟລ໌: connect.php
 * ໜ້າທີ່: ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ MySQL (XAMPP) ດ້ວຍ PDO
 * =====================================================
 *
 * ການຕັ້ງຄ່າ XAMPP ມາດຕະຖານ:
 *  - host: localhost
 *  - user: root
 *  - pass: (ວ່າງ)
 *  - database: project_nonghainoy
 *
 * ໝາຍເຫດ: ໄຟລ໌ນີ້ຈະຖືກ require ໂດຍໄຟລ໌ອື່ນ
 *         (process.php, index.php) ເພື່ອໃຊ້ຕົວແປ $pdo ຮ່ວມກັນ
 */

// 1) ກຳນົດຄ່າຄົງທີ່ (constants) ສຳລັບການເຊື່ອມຕໍ່
//    ການແຍກຄ່າອອກມາແບບນີ້ ຊ່ວຍໃຫ້ປ່ຽນແປງງ່າຍ ແລະ ປອດໄພກວ່າ
define('DB_HOST', 'localhost');
define('DB_NAME', 'project_nonghainoy');
define('DB_USER', 'root');
define('DB_PASS', '');           // XAMPP default: ລະຫັດຜ່ານວ່າງ
define('DB_CHARSET', 'utf8mb4'); // ຮອງຮັບໂຕອັກສອນລາວ

// 2) ສ້າງ DSN (Data Source Name) - string ບອກ PDO ວ່າຈະຕໍ່ຫາໃສ
$dsn = "mysql:host=" . DB_HOST
     . ";dbname=" . DB_NAME
     . ";charset=" . DB_CHARSET;

// 3) ກຳນົດ options ສຳລັບ PDO ເພື່ອຄວາມປອດໄພ ແລະ ປະສິດທິພາບ
$options = [
    // ໃຫ້ throw exception ເມື່ອມີ error → ຈັບ error ໄດ້ງ່າຍ
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    // ຄືນຄ່າເປັນ associative array (ໃຊ້ຊື່ column ເປັນ key)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // ປິດການ emulate prepared statements
    // → ໃຫ້ MySQL ປະມວນຜົນ prepared statement ຈິງໆ ປ້ອງກັນ SQL Injection
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4) ສ້າງການເຊື່ອມຕໍ່ ໂດຍຄອບໄວ້ໃນ try/catch ເພື່ອຈັດການ error
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // ໃນ Production ບໍ່ຄວນສະແດງ error ຕັ້ງໆ ໃຫ້ຜູ້ໃຊ້ເຫັນ
    // ຄວນ log ໄວ້ ແລ້ວສະແດງຂໍ້ຄວາມທົ່ວໄປ
    // ສຳລັບການພັດທະນາ (XAMPP local) ສະແດງ error ໄດ້ ເພື່ອ debug ງ່າຍ
    http_response_code(500);
    die("❌ ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນໄດ້: " . htmlspecialchars($e->getMessage()));
}

// ເຖິງຈຸດນີ້ ໝາຍຄວາມວ່າເຊື່ອມຕໍ່ສຳເລັດ
// → ໄຟລ໌ອື່ນສາມາດໃຊ້ $pdo ໄດ້ທັນທີ
