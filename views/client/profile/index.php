<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <?php include BASE_PATH . '/views/client/profile/_sidebar.php'; ?>

        <!-- Content -->
        <div class="flex-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold mb-6">Thông tin tài khoản</h2>

                <form action="<?= BASE_URL ?>/profile/update" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <!-- Avatar -->
                    <div class="mb-6 flex items-center gap-4">
                        <div class="relative">
                            <img id="avatarPreview" src="<?= avatar($user['avatar']) ?>" alt="Avatar" 
                                 class="w-20 h-20 rounded-full object-cover border-4 border-gray-200">
                            <label for="avatarInput" 
                                   class="absolute bottom-0 right-0 w-7 h-7 bg-accent text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-red-600">
                                <i class="fas fa-camera text-xs"></i>
                            </label>
                            <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" 
                                   onchange="if(this.files && this.files[0]) document.getElementById('avatarPreview').src = URL.createObjectURL(this.files[0])">
                        </div>
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($user['fullname']) ?></p>
                            <p class="text-sm text-gray-500"><?= $user['email'] ?></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Họ và tên</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" value="<?= $user['email'] ?>" disabled
                                   class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Số điện thoại</label>
                            <input type="tel" name="phone_number" value="<?= $user['phone_number'] ?? '' ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Ngày sinh</label>
                            <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-2">Địa chỉ</label>
                        <textarea name="address" rows="2" 
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent"><?= $user['address'] ?? '' ?></textarea>
                    </div>

                    <button type="submit" class="mt-6 px-6 py-2 bg-accent text-white rounded-lg hover:bg-red-600">
                        Lưu thay đổi
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <?php if (!empty($user['password'])): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <h2 class="text-xl font-bold mb-6">Đổi mật khẩu</h2>
                <form action="<?= BASE_URL ?>/profile/changePassword" method="POST">
                    <?= csrfField() ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Mật khẩu mới</label>
                            <input type="password" name="new_password" required minlength="6"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Xác nhận mật khẩu</label>
                            <input type="password" name="confirm_password" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-gray-700">
                        Đổi mật khẩu
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
