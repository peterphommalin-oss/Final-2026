<?php
/**
 * includes/chart_panel.php
 * --------------------------------------------------
 * ກຣາຟ ລາຍຮັບ-ລາຍຈ່າຍ ລາຍເດືອນ
 * ໃຊ້ Chart.js (ໂຫຼດ CDN ໃນ footer)
 */
$chart = get_monthly_chart();
?>
<section class="panel">
    <div class="panel-header">
        <h3>ລາຍຮັບ-ລາຍຈ່າຍ ລາຍເດືອນ (<?= COPY_YEAR ?>)</h3>
        <div class="legend">
            <span class="legend-item">
                <span class="legend-swatch" style="background:var(--color-orange)"></span> ລາຍຮັບ
            </span>
            <span class="legend-item">
                <span class="legend-swatch" style="background:var(--color-gold-light)"></span> ລາຍຈ່າຍ
            </span>
        </div>
    </div>

    <div class="chart-wrap">
        <canvas id="financeChart"></canvas>
    </div>  
</section>

<!-- ສົ່ງຂໍ້ມູນ PHP -> JavaScript -->
<script>
    window.CHART_DATA = <?= json_encode($chart, JSON_UNESCAPED_UNICODE) ?>;
</script>
