<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$response = 'ok';

$fio = isset($_POST['fio']) ? trim(strip_tags($_POST['fio'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$feedback = isset($_POST['feedback']) ? trim(strip_tags($_POST['feedback'])) : '';

if (!$fio) {
  $response = "Представьтесь, пожалуйста<br>";
} elseif (!$phone && !$email) {
  $response = "Укажите, как с Вами связаться (телефон и/или e-mail)<br>";
} elseif ($email && !Class_Shared::ValidateEmail($email)) {
  $response = "E-mail указан неверно<br>";
} elseif (!$feedback) {
  $response = "Укажите ваш вопрос<br>";
}

if ($response == 'ok') {
  $feedbackCommon = new Class_AnonymousCommon('feedback');
  $data = array(
    'append_date' => date('Y-m-d H:i:s'),
    'fio' => $fio,
    'phone' => $phone,
    'email' => $email,
    'feedback' => $feedback,
  );
  $r = $feedbackCommon->Create($data);
  if ($r) {
    $adminEmail = $feedbackCommon->GetSetting('admin_email');
    if ($adminEmail) {
      $subj = "Обратная связь с сайта " . $_SERVER['HTTP_HOST'];
      $message = "Ф.И.О.: {$fio}\n
Телефон: {$phone}\n
E-mail: {$email}\n
Вопрос: {$feedback}\n
";
      @mail($adminEmail, $subj, $message, "Content-type: text/plain; charset=utf-8 \r\nFrom: {$adminEmail}");
    }
  } else {
    $response = "Не удалось отправить ваш вопрос. Попробуйте еще раз.";
  }
}

echo json_encode(array('response' => $response));