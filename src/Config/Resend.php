
<?php
echo 1;
require __DIR__ . '/vendor/autoload.php';

function send (){

$resend = Resend::client('re_F5feTq5Q_LycTnkXJjTDndCuYYuaQJT4s');

try {
    $result = $resend->emails->send([
        'from' => 'Acme <onboarding@resend.dev>',
        'to' => ['cesarcunyarache@gmail.com'],
        'subject' => 'Hello world',
        'html' => '<strong>It works!</strong>',
    ]);
} catch (\Exception $e) {
    print_r('Error: ' . $e->getMessage());
}


print_r ($result->toJson());

}


?>