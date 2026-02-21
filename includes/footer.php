</main>

<footer class="bg-dark text-white text-center py-3 mt-4">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
<?php if (isset($page_scripts)): foreach ($page_scripts as $script): ?>
    <script src="<?= base_url('assets/js/' . $script) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>