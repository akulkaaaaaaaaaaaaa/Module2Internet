<?php
session_start();

// Дебаг: записуємо POST і сесії в лог
error_log("POST data: " . print_r($_POST, true));
error_log("Session data: " . print_r($_SESSION, true));

// Ініціалізація змінних
$maxValue = isset($_SESSION['max_value']) ? $_SESSION['max_value'] : 10;
$operator = isset($_SESSION['sign']) ? $_SESSION['sign'] : '+';
$operand1 = isset($_SESSION['operand1']) ? $_SESSION['operand1'] : 0;
$operand2 = isset($_SESSION['operand2']) ? $_SESSION['operand2'] : 0;
$userInput = isset($_SESSION['user_input']) ? $_SESSION['user_input'] : '';
$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : '???';

// Обробка вибору діапазону
if (isset($_POST['max_value'])) {
    $maxValue = (int)$_POST['max_value'];
    $_SESSION['max_value'] = $maxValue;
    $_SESSION['user_input'] = '';
    $_SESSION['feedback'] = '???';
    error_log("MaxValue set to: $maxValue");
}

// Обробка вибору оператора
if (isset($_POST['sign'])) {
    $operator = $_POST['sign'];
    $_SESSION['sign'] = $operator;
    $_SESSION['user_input'] = '';
    $_SESSION['feedback'] = '???';
    error_log("Operator set to: $operator");
}

// Генерація нових операндів
if (isset($_POST['generate'])) {
    error_log("Generate button clicked");
    $operand1 = rand(0, $maxValue);
    $operand2 = ($operator === '*' && $maxValue > 0) ? rand(0, $maxValue) : rand(0, max(0, $maxValue - $operand1));
    $_SESSION['operand1'] = $operand1;
    $_SESSION['operand2'] = $operand2;
    $_SESSION['user_input'] = '';
    $_SESSION['feedback'] = '???';
    error_log("Generated: operand1=$operand1, operand2=$operand2");
}

// Обробка введення з клавіатури
if (isset($_POST['key'])) {
    if ($_POST['key'] === 'OK') {
        error_log("OK button clicked, userInput=$userInput");
        if ($operator === '+') {
            $correctAnswer = $operand1 + $operand2;
        } elseif ($operator === '-') {
            $correctAnswer = $operand1 - $operand2;
        } else {
            $correctAnswer = $operand1 * $operand2;
        }
        $feedback = ((int)$userInput === $correctAnswer) ? 'Correct!' : 'Try Again!';
        $_SESSION['feedback'] = $feedback;
        $_SESSION['user_input'] = '';
        error_log("Correct answer: $correctAnswer, Feedback: $feedback");
    } else {
        $userInput .= $_POST['key'];
        $_SESSION['user_input'] = $userInput;
        $_SESSION['feedback'] = '???';
        error_log("Key pressed: {$_POST['key']}, userInput=$userInput");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Math Test</title>
    <style>
        /* Основні стилі */
        body {
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        /* Заголовок */
        h1 {
            font-size: 2.5em;
            color: #1a237e;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* Центрування всіх елементів */
        .center {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Стилі для форми */
        form {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        /* Кнопки вибору діапазону */
        .button-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        button[name="max_value"] {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
        }

        button[name="max_value"]:hover {
            transform: scale(1.05);
        }

        button[name="max_value"][value="10"] { background-color: #ff6f61; color: white; }
        button[name="max_value"][value="20"] { background-color: #26a69a; color: white; }
        button[name="max_value"][value="28"] { background-color: #ffca28; color: #333; }
        button[name="max_value"][value="100"] { background-color: #42a5f5; color: white; }

        /* Кнопки операцій */
        button[name="sign"] {
            padding: 10px 20px;
            font-size: 1.2em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
        }

        button[name="sign"]:hover {
            transform: scale(1.05);
        }

        button[name="sign"][value="+"] { background-color: #66bb6a; color: white; }
        button[name="sign"][value="-"] { background-color: #ef5350; color: white; }
        button[name="sign"][value="*"] { background-color: #f06292; color: white; }

        /* Кнопка генерації виразу */
        button[name="generate"] {
            background: linear-gradient(90deg, #3f51b5, #5c6bc0);
            color: white;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        button[name="generate"]:hover {
            transform: scale(1.05);
        }

        /* Стилі для таблиці з виразом */
        table.output-table {
            margin: 20px auto;
            border-collapse: collapse;
            background: #f5f5f5;
            border-radius: 8px;
            overflow: hidden;
        }

        table.output-table td {
            padding: 10px;
            font-size: 1.2em;
        }

        /* Поля вводу */
        input {
            text-align: center;
            padding: 8px;
            font-size: 1.2em;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: white;
            width: 60px;
        }

        input[readonly] {
            background: #e0e0e0;
            cursor: default;
        }

        input#feedback {
            width: 100px;
            color: #d81b60;
            font-weight: bold;
        }

        /* Блок клавіатури */
        #keyboard {
            display: grid;
            grid-template-columns: repeat(3, 70px);
            gap: 10px;
            padding: 15px;
            background: #ffffff;
            border: 2px solid #3f51b5;
            border-radius: 12px;
            margin: 20px auto;
        }

        /* Стилі для кнопок клавіатури */
        #keyboard button {
            background: linear-gradient(90deg, #d81b60, #f06292);
            color: white;
            border: none;
            cursor: pointer;
            width: 70px;
            height: 70px;
            font-size: 1.2em;
            border-radius: 8px;
            transition: transform 0.2s, background 0.2s;
        }

        #keyboard button:hover {
            background: linear-gradient(90deg, #f06292, #d81b60);
            transform: scale(1.1);
        }

        /* Адаптивність */
        @media (max-width: 600px) {
            form {
                padding: 15px;
            }

            button[name="max_value"], button[name="sign"], button[name="generate"] {
                padding: 8px 15px;
                font-size: 0.9em;
            }

            #keyboard {
                grid-template-columns: repeat(3, 60px);
            }

            #keyboard button {
                width: 60px;
                height: 60px;
                font-size: 1em;
            }

            input {
                width: 50px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="center">
        <h1>Math Test</h1>
        <form method="post">
            <div class="button-row">
                <button type="submit" name="max_value" value="10">0-10</button>
                <button type="submit" name="max_value" value="20">0-20</button>
                <button type="submit" name="max_value" value="28">0-28</button>
                <button type="submit" name="max_value" value="100">0-100</button>
            </div>
            <div class="button-row">
                <button type="submit" name="sign" value="+">+</button>
                <button type="submit" name="sign" value="-">-</button>
                <button type="submit" name="sign" value="*">×</button>
            </div>
            <hr />
            <table class="output-table">
                <tr>
                    <td><input id="operand1" size="3" readonly value="<?php echo htmlspecialchars($operand1 ?? 0); ?>" /></td>
                    <td><input id="operator" size="1" readonly value="<?php echo htmlspecialchars($operator ?? '+'); ?>" /></td>
                    <td><input id="operand2" size="3" readonly value="<?php echo htmlspecialchars($operand2 ?? 0); ?>" /></td>
                    <td>=</td>
                    <td><input id="result" size="4" readonly value="<?php echo htmlspecialchars($userInput ?? ''); ?>" /></td>
                    <td><button type="submit" name="generate" value="Generate">Generate</button></td>
                    <td><input id="feedback" size="6" readonly value="<?php echo htmlspecialchars($feedback ?? '???'); ?>" /></td>
                </tr>
            </table>
            <hr />
            <div id="keyboard">
                <button type="submit" name="key" value="1">1</button>
                <button type="submit" name="key" value="2">2</button>
                <button type="submit" name="key" value="3">3</button>
                <button type="submit" name="key" value="4">4</button>
                <button type="submit" name="key" value="5">5</button>
                <button type="submit" name="key" value="6">6</button>
                <button type="submit" name="key" value="7">7</button>
                <button type="submit" name="key" value="8">8</button>
                <button type="submit" name="key" value="9">9</button>
                <button type="submit" name="key" value="0">0</button>
                <button type="submit" name="key" value="OK">OK</button>
            </div>
        </form>
    </div>
</body>
</html>
