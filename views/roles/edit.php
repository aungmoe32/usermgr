<?php
require __DIR__ . '/../layout/header.php';

$role_id = $_GET['id'] ?? null;

if (!$role_id || !is_numeric($role_id)) {
    header('Location: /roles');
    exit;
}

// Fetch role data
$role = db()->query("SELECT id, name FROM roles WHERE id = ?", [$role_id])->find();

if (!$role) {
    redirect('/roles');
}

$errors = Core\Session::get('errors', []);
$success = Core\Session::get('success');
$old = Core\Session::get('old', []);

// Use old values if available, otherwise use role data
$name = $old['name'] ?? $role['name'];
$selectedPermissions = $old['permissions'] ?? [];

if (empty($selectedPermissions) && empty($old)) {
    $currentPermissions = db()->query("
        SELECT permission_id 
        FROM roles_permission 
        WHERE role_id = ?
    ", [$role_id])->get();
    $selectedPermissions = array_column($currentPermissions, 'permission_id');
}

// Fetch all features and permissions
$features = db()->query("
    SELECT 
        f.id,
        f.name,
        f.description,
        p.id as permission_id,
        p.name as permission_name,
        p.description as permission_description
    FROM features f
    JOIN permissions p ON f.id = p.feature_id
    ORDER BY f.name, p.name
")->get();

// Group permissions by feature
$groupedPermissions = [];
foreach ($features as $feature) {
    $groupedPermissions[$feature['name']][] = [
        'id' => $feature['permission_id'],
        'name' => $feature['permission_name'],
        'description' => $feature['permission_description']
    ];
}
?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Edit Role
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Update role information and permissions
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
                                <?php if (count($errors) === 1): ?>
                                    <p><?= htmlspecialchars($errors['name'] ?? $errors['permissions'] ?? $errors[0]) ?></p>
                                <?php else: ?>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <?php foreach ($errors as $field => $error): ?>
                                            <li><?= htmlspecialchars(is_array($error) ? $error[0] : $error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="/roles/update" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" value="<?= htmlspecialchars($role['id']) ?>">
                <?= csrf_field() ?>

                <!-- Role Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Role Name *
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        maxlength="50"
                        class="mt-1 block w-full px-3 py-2 border <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?> rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter role name"
                        value="<?= htmlspecialchars($name) ?>">
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['name']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Permissions Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Permissions *
                    </label>
                    <div class="space-y-6">
                        <?php foreach ($groupedPermissions as $featureName => $permissions): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3 capitalize">
                                    <?= htmlspecialchars($featureName) ?> Management
                                </h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <?php foreach ($permissions as $permission): ?>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    id="permission_<?= $permission['id'] ?>"
                                                    name="permissions[]"
                                                    type="checkbox"
                                                    value="<?= $permission['id'] ?>"
                                                    <?= in_array($permission['id'], $selectedPermissions) ? 'checked' : '' ?>
                                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="permission_<?= $permission['id'] ?>" class="font-medium text-gray-700 capitalize cursor-pointer">
                                                    <?= htmlspecialchars($permission['name']) ?>
                                                </label>
                                                <?php if (!empty($permission['description'])): ?>
                                                    <p class="text-gray-500 text-xs"><?= htmlspecialchars($permission['description']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (isset($errors['permissions'])): ?>
                        <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['permissions']) ?></p>
                    <?php endif; ?>
                    <p class="mt-2 text-xs text-gray-500">Select at least one permission for this role</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-3">
                    <button
                        type="submit"
                        class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Update Role
                    </button>
                    <a
                        href="/roles"
                        class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Cancel
                    </a>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-xs text-gray-500 space-y-1">
                        <p><strong>Role ID:</strong> <?= htmlspecialchars($role['id']) ?></p>
                        <p><strong>Current Name:</strong> <?= htmlspecialchars($role['name']) ?></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/../layout/footer.php';
?>