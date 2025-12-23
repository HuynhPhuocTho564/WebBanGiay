<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold mb-2">Đăng nhập</h1>
                <p class="text-gray-500">Chào mừng bạn quay trở lại!</p>
            </div>

            <!-- Form -->
            <form action="<?= BASE_URL ?>/auth/login" method="POST">
                <?= csrfField() ?>
                
                <!-- Username/Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tên đăng nhập hoặc Email</label>
                    <input type="text" name="username" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent"
                           placeholder="Nhập username hoặc email">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Mật khẩu</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent"
                           placeholder="Nhập mật khẩu">
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-accent">
                        <span class="text-sm text-gray-600">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="<?= BASE_URL ?>/auth/forgot" class="text-sm text-accent hover:underline">
                        Quên mật khẩu?
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        class="w-full py-3 bg-accent text-white rounded-lg font-medium hover:bg-red-600 transition">
                    Đăng nhập
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">or</span>
                </div>
            </div>

            <!-- Social Login -->
            <a href="<?= BASE_URL ?>/auth/google" 
               class="flex items-center justify-center gap-3 w-full py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="w-5 h-5">
                <span>Đăng nhập với Google</span>
            </a>

            <!-- Register Link -->
            <p class="text-center mt-6 text-gray-600">
                Chưa có tài khoản? 
                <a href="<?= BASE_URL ?>/auth/register" class="text-accent font-medium hover:underline">
                    Đăng ký ngay
                </a>
            </p>
        </div>
    </div>
</div>
