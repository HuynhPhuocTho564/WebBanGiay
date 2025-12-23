<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/adminuser" class="text-gray-500 hover:text-accent">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold mb-6">Sửa thông tin người dùng</h2>

        <form action="<?= BASE_URL ?>/adminuser/edit/<?= $user['id'] ?>" method="POST">
            <?= csrfField() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="fullname" required value="<?= htmlspecialchars($user['fullname']) ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tên đăng nhập</label>
                    <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled
                           class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Số điện thoại</label>
                    <input type="tel" name="phone_number" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Mật khẩu mới</label>
                    <input type="password" name="password" minlength="6" placeholder="Để trống nếu không đổi"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Quyền hạn <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        <option value="0" <?= $user['role'] == 0 ? 'selected' : '' ?>>Khách hàng</option>
                        <option value="1" <?= $user['role'] == 1 ? 'selected' : '' ?>>Nhân viên</option>
                        <option value="2" <?= $user['role'] == 2 ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-save mr-2"></i> Cập nhật
                </button>
                <a href="<?= BASE_URL ?>/adminuser" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Hủy</a>
            </div>
        </form>
    </div>
</div>
