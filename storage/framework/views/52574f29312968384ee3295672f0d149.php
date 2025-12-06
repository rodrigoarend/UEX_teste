<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo $__env->yieldContent('title', 'App Contatos'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-white shadow mb-4">
            <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between">
                <div class="font-bold">UEX CONTATOS</div>
                <div>
                    <button id="logout-btn" class="text-sm text-red-600 hidden">
                        Sair
                    </button>
                </div>
            </div>
        </nav>

        <main class="flex-1">
            <div class="max-w-5xl mx-auto px-4">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
    </div>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\php\teste-app\resources\views/layouts/app.blade.php ENDPATH**/ ?>