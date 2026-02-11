<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $title ?></title>

<link href="assets/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #0d1117;
    color: white;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}
.card-maintenance {
    background: #161b22;
    padding: 40px;
    border-radius: 12px;
    max-width: 480px;
    width: 90%;
}
.icon-box {
    font-size: 70px;
    color: #ffc107;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="card-maintenance shadow-lg">
    <div class="icon-box"></div>
    <h2 class="mb-3"><?= htmlspecialchars($title) ?></h2>
    <p class="text-secondary mb-4"><?= nl2br(htmlspecialchars($msg)) ?></p>
    <small class="text-secondary">© <?= date('Y') ?> LAPOR Polres Banjar</small>
</div>

</body>
</html>