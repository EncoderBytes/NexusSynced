<?php
$is_devil = (isset($body_class) && strpos($body_class, 'devil') !== false);
?>
<script src="/assets/js/main.js"></script>
<?php if (strpos($current_page, 'admin') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false): ?>
<script src="/assets/js/admin.js"></script>
<?php endif; ?>
</body>
</html>
