<?php
/**
 * Alert Messages Component
 * Displays flash messages (success, error, warning, info)
 */

// Success message
if (isset($_SESSION['alert_success'])): ?>
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-900 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="font-medium"><?= htmlspecialchars($_SESSION['alert_success']) ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['alert_success']); ?>
<?php endif; ?>

<?php
// Error message
if (isset($_SESSION['alert_error'])): ?>
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-900 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="font-medium"><?= htmlspecialchars($_SESSION['alert_error']) ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['alert_error']); ?>
<?php endif; ?>

<?php
// Warning message
if (isset($_SESSION['alert_warning'])): ?>
    <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-900 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="font-medium"><?= htmlspecialchars($_SESSION['alert_warning']) ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-yellow-500 hover:text-yellow-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['alert_warning']); ?>
<?php endif; ?>

<?php
// Info message
if (isset($_SESSION['alert_info'])): ?>
    <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 text-blue-900 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="font-medium"><?= htmlspecialchars($_SESSION['alert_info']) ?></p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-blue-500 hover:text-blue-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['alert_info']); ?>
<?php endif; ?>
