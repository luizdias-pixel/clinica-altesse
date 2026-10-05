<div class="procedimentos">
    <?php foreach (procedimentos() as $proc): ?>
        <div class="proc-card">
            <div class="proc-img-wrap">
                <img src="<?= e(url('assets/img/' . $proc['imagem'])) ?>"
                    alt="<?= e($proc['nome']) ?>"
                    width="<?= (int) $proc['largura'] ?>" height="<?= (int) $proc['altura'] ?>"
                    loading="lazy">
            </div>
            <div class="proc-body">
                <h3><?= e($proc['nome']) ?></h3>
                <p><?= e($proc['descricao']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
