<?php
// Масив для зберігання даних користувача
$userData = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Отримання даних з форми
    $username = trim($_POST['username']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    // Валідація: перевірка на непорожні поля
    if (empty($username)) {
        $errors[] = "Ім'я користувача не може бути порожнім";
    }
    if (empty($firstName)) {
        $errors[] = "Ім'я не може бути порожнім";
    }
    if (empty($lastName)) {
        $errors[] = "Прізвище не може бути порожнім";
    }
    if (empty($email)) {
        $errors[] = "E-mail не може бути порожнім";
    }
    if (empty($password)) {
        $errors[] = "Пароль не може бути порожнім";
    }
    if (empty($address)) {
        $errors[] = "Адреса не може бути порожньою";
    }
    if (empty($phone)) {
        $errors[] = "Номер телефону не може бути порожнім";
    }

    // Валідація формату email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Невірний формат E-mail";
    }

    // Якщо немає помилок, збереження даних у масив
    if (empty($errors)) {
        $userData = [
            'користувач' => $username,
            'імя' => $firstName,
            'прізвище' => $lastName,
            'пошта' => $email,
            'пароль' => $password,
            'адреса' => $address,
            'телефон' => $phone
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Форма реєстрації</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: red; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h2>Реєстрація користувача</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Ім'я користувача:</label>
            <input type="text" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="firstName">Ім'я:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="lastName">Прізвище:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="address">Адреса:</label>
            <input type="text" id="address" name="address" value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="phone">Номер телефону:</label>
            <input type="text" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
        </div>
        <button type="submit">Зареєструватися</button>
    </form>

    <?php
    // Виведення помилок, якщо вони є
    if (!empty($errors)) {
        echo '<div class="error"><ul>';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul></div>';
    }

    // Виведення масиву з даними, якщо валідація успішна
    if (!empty($userData)) {
        echo '<h3>Дані користувача:</h3>';
        echo '<pre>';
        print_r($userData);
        echo '</pre>';
    }
    ?>
</body>
</html>
