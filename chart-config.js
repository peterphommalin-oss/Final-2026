/* =====================================================
 * ໄຟລ໌: chart-config.js
 * ໜ້າທີ່: ສ້າງກຣາຟວົງມົນ (Pie Chart) ດ້ວຍ Chart.js
 *         ສະແດງສັດສ່ວນ ລາຍຮັບ vs ລາຍຈ່າຍ
 * ໂທນສີ: ສົ້ມ - ເຫຼືອງ - ແດງ (ໃຫ້ເຂົ້າກັບທີມວັດ)
 * =====================================================
 *
 * ໝາຍເຫດ: ໄຟລ໌ນີ້ໃຊ້ window.financeData ທີ່ index.php
 *         ສົ່ງມາໃຫ້ (ບັນຈຸ income, expense, balance)
 */

// ລໍຖ້າ DOM ໂຫຼດສຳເລັດກ່ອນ ຈິ່ງ render chart
document.addEventListener('DOMContentLoaded', function () {

    // 1) ດຶງ canvas element ທີ່ກຳໜົດໄວ້ໃນ index.php
    const canvas = document.getElementById('financeChart');
    if (!canvas) {
        console.error('❌ ບໍ່ພົບ canvas#financeChart');
        return;
    }

    // 2) ດຶງຂໍ້ມູນທີ່ PHP ສົ່ງມາຜ່ານ window object
    //    (ໃຊ້ ||0 ກັນ undefined ກໍລະນີຂໍ້ມູນບໍ່ມາ)
    const data = window.financeData || { income: 0, expense: 0, balance: 0 };

    const income  = Number(data.income)  || 0;
    const expense = Number(data.expense) || 0;
    // balance ອາດເປັນຄ່າລົບ ຖ້າຈ່າຍຫຼາຍກວ່າຮັບ → ໃຊ້ Math.max ໃຫ້ chart ບໍ່ພັງ
    const balance = Math.max(Number(data.balance) || 0, 0);

    // 3) ກຳນົດສີ ໂທນສົ້ມ-ເຫຼືອງ-ແດງ
    const COLORS = {
        income:  '#ff9838',   // ສົ້ມເຫຼືອງ (saffron-400) ສຳລັບລາຍຮັບ
        expense: '#dc2626',   // ແດງ (red-600)            ສຳລັບລາຍຈ່າຍ
        balance: '#fbbf24',   // ເຫຼືອງ (amber-400)        ສຳລັບຍອດຄົງເຫຼືອ
    };

    const BORDERS = {
        income:  '#c7430b',   // saffron-700
        expense: '#991b1b',   // red-800
        balance: '#d97706',   // amber-600
    };

    // 4) ສ້າງ Pie Chart
    new Chart(canvas, {
        type: 'pie',

        data: {
            // ປ້າຍຊື່ສ່ວນຕ່າງໆ
            labels: [
                '💰 ລາຍຮັບ',
                '💸 ລາຍຈ່າຍ',
                '🏛️ ຍອດຄົງເຫຼືອ'
            ],
            datasets: [{
                // ຄ່າ - ຮຽງຕາມ labels ຂ້າງເທິງ
                data: [income, expense, balance],

                // ສີຂອງແຕ່ລະຊິ້ນ
                backgroundColor: [
                    COLORS.income,
                    COLORS.expense,
                    COLORS.balance,
                ],

                // ສີຂອບ
                borderColor: [
                    BORDERS.income,
                    BORDERS.expense,
                    BORDERS.balance,
                ],
                borderWidth: 3,

                // ໃຫ້ shadow ເບົາໆເວລາ hover
                hoverOffset: 12,
                hoverBorderWidth: 4,
            }]
        },


        options: {
            responsive: true,
            maintainAspectRatio: false, // ໃຫ້ສູງ-ກວ້າງປ່ຽນຕາມ container

            plugins: {
                // ການຕັ້ງຄ່າ legend (ປ້າຍຊື່)
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: '"Noto Sans Lao", sans-serif',
                            size: 13,
                            weight: '600',
                        },
                        color: '#7f2e12',  // saffron-900
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle',
                    }
                },

                // Title ຂອງກຣາຟ
                title: {
                    display: true,
                    text: 'ສະຫຼຸບສະຖານະການເງິນ',
                    font: {
                        family: '"Noto Sans Lao", sans-serif',
                        size: 16,
                        weight: 'bold',
                    },
                    color: '#c7430b',  // saffron-700
                    padding: { top: 10, bottom: 20 }
                },

                // ການຕັ້ງຄ່າ tooltip (ປະອບອັບເມື່ອ hover)
                tooltip: {
                    backgroundColor: 'rgba(127, 46, 18, 0.95)',  // saffron-900 ໂປ່ງໃສ
                    titleFont: {
                        family: '"Noto Sans Lao", sans-serif',
                        size: 14,
                        weight: 'bold',
                    },
                    bodyFont: {
                        family: '"Noto Sans Lao", sans-serif',
                        size: 13,
                    },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,

                    // ປັບແຕ່ງຂໍ້ຄວາມໃນ tooltip
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;

                            // ຄຳນວນ %
                            const total = context.dataset.data.reduce(
                                (sum, v) => sum + v, 0
                            );
                            const percent = total > 0
                                ? ((value / total) * 100).toFixed(1)
                                : 0;

                            // ຈັດຮູບແບບຕົວເລກ
                            const formatted = new Intl.NumberFormat('en-US').format(value);

                            return `${label}: ${formatted} ກີບ (${percent}%)`;
                        }
                    }
                }
            },

            // Animation ຕອນເລີ່ມຕົ້ນ
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1200,
                easing: 'easeOutQuart',
            }
        }
    });

    console.log('✅ ກຣາຟວົງມົນຖືກໂຫຼດສຳເລັດ', { income, expense, balance });
