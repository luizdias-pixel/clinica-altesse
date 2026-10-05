    </main>

    <footer>
        &nbsp;·&nbsp; Clínica Altesse © <?= date('Y') ?> Todos os direitos reservados
    </footer>

    <script src="<?= e(url('assets/js/menu.js')) ?>"></script>
    <?php foreach (($scripts ?? []) as $arquivoJs): ?>
        <script src="<?= e(url('assets/js/' . $arquivoJs)) ?>"></script>
    <?php endforeach; ?>

    </body>

    </html>