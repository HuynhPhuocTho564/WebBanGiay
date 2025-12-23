        </main>

        <!-- Footer -->
        <footer class="bg-white border-t px-6 py-4">
            <p class="text-sm text-gray-500 text-center">
                &copy; <?= date('Y') ?> <?= SITE_NAME ?> Admin Panel
            </p>
        </footer>
    </div>
</div>

<!-- Overlay for mobile sidebar -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

<script>
// Sidebar Toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
});

function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.add('hidden');
}

// Confirm Delete
function confirmDelete(message, callback) {
    if (confirm(message || 'Bạn có chắc chắn muốn xóa?')) {
        callback();
    }
}
</script>

<style>
@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.animate-slide-in { animation: slide-in 0.3s ease-out; }
</style>

</body>
</html>
