<?php
/** FAQ di beranda.php. Butuh $db_aktif dan $faq. */
?>
<?php if ($db_aktif && $faq): ?>
<section class="section-title" id="faq">
    <div>
        <h2><i class="bi bi-question-circle text-primary"></i> Pertanyaan Umum</h2>
        <p>Yang paling sering ditanyakan pelanggan.</p>
    </div>
</section>

<section class="accordion" id="faqAcc">
    <?php foreach ($faq as $i => $f): ?>
        <div class="faq-item">
            <button class="btn w-100 d-flex justify-content-between align-items-center collapsed"
                    data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>" aria-expanded="false">
                <span><?= e($f['pertanyaan']) ?></span>
                <i class="bi bi-plus-lg"></i>
            </button>
            <div id="faq<?= $i ?>" class="collapse" data-bs-parent="#faqAcc">
                <div class="card-body pt-0"><p><?= nl2br(e($f['jawaban'])) ?></p></div>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>
