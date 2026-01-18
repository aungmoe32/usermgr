<?php
require __DIR__ . '/../layout/header.php';

$user_id = $_GET['id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    header('Location: /users');
    exit;
}

$user = db()->query("
    SELECT 
        u.id,
        u.name,
        u.role_id,
        u.is_active
    FROM users u
    WHERE u.id = ?
", [$user_id])->find();

if (!$user) {
    header('Location: /users');
    exit;
}

$roles = db()->query("SELECT id, name FROM roles ORDER BY name")->get();

$errors = Core\Session::get('errors', []);
$success = Core\Session::get('success');
$old = Core\Session::get('old', []);

$name = $old['name'] ?? $user['name'];
$role_id = $old['role_id'] ?? $user['role_id'];
$is_active = isset($old['is_active']) ? $old['is_active'] : $user['is_active'];
?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Edit User
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Update user information and settings
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($success) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="/users/update" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                <?= csrf_field() ?>

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            value="<?= htmlspecialchars($name) ?>"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?= in_array('name', array_column($errors, 0)) ? 'border-red-300' : '' ?>"
                            placeholder="Enter full name">
                    </div>
                </div>

                <!-- Role Field -->
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">
                        Role
                    </label>
                    <div class="mt-1">
                        <select
                            id="role_id"
                            name="role_id"
                            required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select a role</option>
                            <?php foreach ($roles as $role): ?>
                                <option
                                    value="<?= htmlspecialchars($role['id']) ?>"
                                    <?= $role_id == $role['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(ucfirst($role['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Active Status Field -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="is_active" class="block text-sm font-medium text-gray-700">
                            User Status
                        </label>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input
                                    id="active"
                                    name="is_active"
                                    type="radio"
                                    value="1"
                                    <?= $is_active ? 'checked' : '' ?>
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="active" class="ml-2 block text-sm text-gray-900">
                                    <span class="font-medium">Active</span>
                                    <span class="text-gray-500 text-xs block">User can access the system</span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    id="inactive"
                                    name="is_active"
                                    type="radio"
                                    value="0"
                                    <?= !$is_active ? 'checked' : '' ?>
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="inactive" class="ml-2 block text-sm text-gray-900">
                                    <span class="font-medium">Inactive</span>
                                    <span class="text-gray-500 text-xs block">User cannot access the system</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-3">
                    <button
                        type="submit"
                        class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Update User
                    </button>
                    <a
                        href="/users"
                        class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Cancel
                    </a>
                </div>
            </form>

            <!-- Additional Info -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="text-xs text-gray-500 space-y-1">
                    <p><strong>User ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
                    <p><strong>Last Updated:</strong> Just now</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Method Override for PUT request -->
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        // Convert to PUT request by changing the method
        this.method = 'POST';

        // Add method override for frameworks that don't support PUT in forms
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';

        // Check if method input already exists to avoid duplicates
        if (!this.querySelector('input[name="_method"]')) {
            this.appendChild(methodInput);
        }
    });
</script>

<?php
require __DIR__ . '/../layout/footer.php';
?>