<?php
// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключаем PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Проверяем, что форма была отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"]);

    // Проверяем данные
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Пожалуйста, заполните все поля формы и укажите корректный email.";
        exit;
    }

    // Настройки SMTP для TimeWeb
    $smtpHost = 'smtp.timeweb.ru'; // SMTP-сервер TimeWeb
    $smtpUsername = 'login@promobankss.ru'; // Ваш email на TimeWeb (из вашего HTML-кода)
    $smtpPassword = 'BO25IS3oR'; // Пароль от почты
    $smtpPort = 2525; // Порт для SSL
    $smtpEncryption = 'tls'; // Шифрование

    // Создаем объект PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = $smtpEncryption;
        $mail->Port = $smtpPort;

        // Включаем отладку (опционально)
        $mail->SMTPDebug = 2;

        // Отправитель и получатель
        $mail->setFrom($smtpUsername, 'login@promobankss.ru'); // От кого (ваш email)
        $mail->addAddress('sgruppy1312@gmail.com'); // Почта заказчика
        $mail->addReplyTo($email, $name); // Ответить на email отправителя

        // Содержание письма
        $mail->isHTML(false); // Отправляем письмо в текстовом формате
        $mail->Subject = "Новое сообщение с сайта Кадастрового Центра";
        $mail->Body = "Имя: $name\nEmail: $email\nСообщение:\n$message";

        // Отправка письма
        $mail->send();
        http_response_code(200);
        echo "Спасибо! Ваше сообщение отправлено.";
    } catch (Exception $e) {
        http_response_code(500);
        echo "Произошла ошибка при отправке сообщения. Пожалуйста, попробуйте позже.";
        error_log("Ошибка отправки письма: {$mail->ErrorInfo}");
    }
} else {
    http_response_code(403);
    echo "Произошла ошибка, попробуйте снова.";
}
?>

