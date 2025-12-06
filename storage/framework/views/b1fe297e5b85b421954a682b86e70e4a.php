

<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-md mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-bold mb-4">Login</h1>

    <form id="login-form" class="space-y-4">
        <div>
            <label class="block text-sm mb-1">E-mail</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Senha</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>

        <div id="login-error" class="text-red-600 text-sm mb-2 hidden"></div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">
            Entrar
        </button>

        <div class="mt-2 text-sm text-center">
            <a href="<?php echo e(route('register.form')); ?>" class="text-blue-600">Criar conta</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
const loginForm = document.getElementById('login-form');
const loginError = document.getElementById('login-error');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    loginError.classList.add('hidden');

    const formData = new FormData(loginForm);

    const res = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
        },
        body: formData
    });

    const data = await res.json();

    if (!res.ok) {
        loginError.textContent = data.message || 'Erro ao fazer login.';
        loginError.classList.remove('hidden');
        return;
    }

    // salva token e redireciona
    localStorage.setItem('auth_token', data.token);
    window.location.href = "<?php echo e(route('contacts.index')); ?>";
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\php\teste-app\resources\views/auth/login.blade.php ENDPATH**/ ?>