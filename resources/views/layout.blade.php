<!DOCTYPE html>
<!--suppress JSUnresolvedLibraryURL -->
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Database Manager{{ config('database-manager.name') ? ' - ' . config('database-manager.name') : '' }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{ BehzadHosseinPoor\DatabaseManager\DatabaseManager::css() }}
    {{ BehzadHosseinPoor\DatabaseManager\DatabaseManager::js() }}
</head>
<body>
<div id="database-manager">

</div>
</body>
</html>