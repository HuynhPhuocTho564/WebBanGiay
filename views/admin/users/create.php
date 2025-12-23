<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/adminuser" class="text-gray-500 hover:text-accent">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold mb-6">Thêm người dùng mới</h2>

        <form action="<?= BASE_URL ?>/adminuser/create" method="POST">
            <?= csrfField() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="fullname" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tên đăng nhập <span class="text-red-500">*</span></label>
                    <input type="text" name="username" required pattern="[a-zA-Z0-9_]+"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Số điện thoại</label>
                    <input type="tel" name="phone_number"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Quyền hạn <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        <option value="0">Khách hàng</option>
                        <option value="1">Nhân viên</option>
                        <option value="2">Admin</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-save mr-2"></i> Lưu
                </button>
                <a href="<?= BASE_URL ?>/adminuser" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
