<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manager Home</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">UserMgr</h1>
                    </div>
                    <?php if (\Core\Authenticator::check()): ?>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="/" class="text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
                                Home
                            </a>
                            <a href="/users" class="text-gray-500 hover:text-gray-700 inline-flex items-center px-1 pt-1 text-sm font-medium">
                                Users
                            </a>
                            <a href="/roles" class="text-gray-500 hover:text-gray-700 inline-flex items-center px-1 pt-1 text-sm font-medium">
                                Roles
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right side navigation -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <?php if (\Core\Authenticator::check()): ?>
                        <?php $authUser = \Core\Authenticator::user(); ?>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">
                                Welcome, <strong><?= htmlspecialchars($authUser['name']) ?></strong>
                                <span class="text-xs text-gray-500">(<?= htmlspecialchars(ucfirst($authUser['role_name'])) ?>)</span>
                            </span>
                            <form method="POST" action="/logout" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>