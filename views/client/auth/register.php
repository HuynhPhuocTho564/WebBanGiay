<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold mb-2">Đăng ký tài khoản</h1>
                <p class="text-gray-500">Tạo tài khoản để nhận nhiều ưu đãi</p>
            </div>

            <!-- Form -->
            <form action="<?= BASE_URL ?>/auth/register" method="POST" enctype="multipart/form-data" id="registerForm">
                <?= csrfField() ?>
                
                <!-- Avatar Upload -->
                <div class="mb-6 text-center">
                    <div class="relative inline-block">
                        <img id="avatarPreview" 
                             src="https://www.gravatar.com/avatar/?d=mp&s=150" 
                             alt="Avatar" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 mx-auto">
                        <label for="avatarInput" 
                               class="absolute bottom-0 right-0 w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-red-600 transition">
                            <i class="fas fa-camera text-sm"></i>
                        </label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Chọn ảnh đại diện (không bắt buộc)</p>
                </div>

                <!-- Fullname -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="fullname" required minlength="2"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent"
                           placeholder="Nguyễn Văn A">
                </div>

                <!-- Username -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tên đăng nhập <span class="text-red-500">*</span></label>
                    <input type="text" name="username" required minlength="3" pattern="[a-zA-Z0-9_]+"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent"
                           placeholder="username123">
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone_number" required pattern="[0-9]{10,11}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent"
                           placeholder="0901234567">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required minlength="6"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent"
                           placeholder="Tối thiểu 6 ký tự">
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nhập lại mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="confirm_password" id="confirm_password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-accent"
                           placeholder="Nhập lại mật khẩu">
                </div>

                <!-- Terms -->
                <div class="mb-6">
                    <p class="text-sm text-gray-600 italic">
                        Bằng việc đăng ký, bạn đã đồng ý với <?= SITE_NAME ?> về 
                        <a href="#" class="text-accent hover:underline">Điều khoản</a> và 
                        <a href="#" class="text-accent hover:underline">Chính sách</a>.
                    </p>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        class="w-full py-3 bg-accent text-white rounded-lg font-medium hover:bg-red-600 transition">
                    Đăng ký
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
                <span>Đăng ký với Google</span>
            </a>

            <!-- Login Link -->
            <p class="text-center mt-6 text-gray-600">
                Đã có tài khoản? 
                <a href="<?= BASE_URL ?>/auth/login" class="text-accent font-medium hover:underline">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
    }
});
</script>
