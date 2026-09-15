# 🛕 ລະບົບຈັດການລາຍຮັບ-ລາຍຈ່າຍຂອງວັດ (Temple Finance Dashboard)

ລະບົບເວັບແອັບສຳລັບບັນທຶກ ແລະ ສະຫຼຸບການເງິນຂອງວັດ — ໃຊ້ **PHP + MySQL + Tailwind CSS + Chart.js**

---

## 📂 ໂຄງສ້າງໄຟລ໌

```
temple_finance/
├── connect.php       # ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ (PDO)
├── process.php       # Backend: INSERT + ຄຳນວນຍອດ
├── index.php         # ໜ້າ Dashboard (Frontend)
├── chart-config.js   # ສ້າງ Pie Chart ດ້ວຍ Chart.js
├── database.sql      # SQL ສ້າງຖານຂໍ້ມູນ + ຕາຕະລາງ
├── logo.jpeg         # ໂລໂກ້ວັດ
└── README.md         # ໄຟລ໌ນີ້
```

---

## 🚀 ວິທີຕິດຕັ້ງ (XAMPP)

### ຂັ້ນຕອນ 1: ກ໊ອບປີ້ໄຟລ໌ໄປ htdocs

ກ໊ອບປີ້ໂຟນເດີ້ `temple_finance` ທັງໝົດໄປວາງໃນ:

```
C:\xampp\htdocs\temple_finance\
```

### ຂັ້ນຕອນ 2: ເປີດ XAMPP

ເປີດໂປຣແກຣມ XAMPP Control Panel → ກົດ **Start**:
- ✅ Apache
- ✅ MySQL

### ຂັ້ນຕອນ 3: ສ້າງຖານຂໍ້ມູນ

1. ເປີດ browser ໄປທີ່ `http://localhost/phpmyadmin`
2. ກົດແທັບ **Import** ດ້ານເທິງ
3. ກົດ **Choose File** → ເລືອກໄຟລ໌ `database.sql`
4. ກົດປຸ່ມ **Import** ດ້ານລຸ່ມ

(ຫຼື ໂດຍກົງ: ສ້າງ database ຊື່ `project_nonghainoy` → ກົດແທັບ **SQL** → paste ເນື້ອຫາໃນ `database.sql` → Go)

### ຂັ້ນຕອນ 4: ເປີດເວັບ

ໄປທີ່:

```
http://localhost/temple_finance/
```

---

## 🎨 ຄຸນສົມບັດ

| ຄຸນສົມບັດ | ລາຍລະອຽດ |
|---------|-----------|
| 💰 ກາດສະຫຼຸບ 3 ກາດ | ລາຍຮັບ / ລາຍຈ່າຍ / ຍອດຄົງເຫຼືອ - ໄລ່ສີສົ້ມ |
| ✏️ ຟອມເພີ່ມລາຍການ | ມີ Validation ທັງຝ່າຍ Client ແລະ Server |
| 📜 ຕາຕະລາງປະຫວັດ | Badge ສີຂຽວ/ແດງ ຈັດລຽງຈາກໃໝ່→ເກົ່າ |
| 📊 ກຣາຟວົງມົນ | Chart.js ໂທນສົ້ມ-ເຫຼືອງ-ແດງ |
| 📱 Responsive | ໃຊ້ໄດ້ທັງມືຖື/Tablet/PC |
| 🔒 ປອດໄພ | PDO Prepared Statements (ປ້ອງກັນ SQL Injection) + htmlspecialchars (ປ້ອງກັນ XSS) |

---

## 🛡️ ມາດຕະການຄວາມປອດໄພທີ່ໃຊ້

1. **PDO Prepared Statements** — ປ້ອງກັນ SQL Injection
2. **`htmlspecialchars()`** — ປ້ອງກັນ XSS ໃນ output
3. **Input Validation** — ກວດສອບປະເພດ, ຄວາມຍາວ, ຄ່າຕົວເລກ
4. **PRG Pattern (Post-Redirect-Get)** — ປ້ອງກັນ form resubmit
5. **`PDO::ATTR_EMULATE_PREPARES = false`** — ໃຊ້ prepared statement ຈິງ
6. **utf8mb4** — ຮອງຮັບໂຕອັກສອນລາວທັງໝົດ

---

## 💡 ການປັບແຕ່ງເພີ່ມເຕີມ

- **ປ່ຽນລະຫັດຜ່ານ DB**: ແກ້ໄຂ `DB_PASS` ໃນ `connect.php`
- **ປ່ຽນສີ Theme**: ແກ້ໄຂ object `saffron` ໃນ `tailwind.config` ໃນໄຟລ໌ `index.php`
- **Production**: ຄວນຕິດຕັ້ງ Tailwind ຜ່ານ npm ແທນ CDN

---

🛕 **ສ້າງດ້ວຍ ❤️ ເພື່ອວັດ**
