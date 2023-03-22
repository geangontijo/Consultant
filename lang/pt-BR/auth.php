<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Essas credenciais não foram encontradas em nossos registros.',
    'password' => 'A senha informada está incorreta.',
    'throttle' => 'Muitas tentativas de login. Tente novamente em :seconds segundos.',
    'verify.failed' => 'Seu usuário não está verificado.',
    'verify.failed_code' => 'O código digitado pelo não corresponde ao código enviado pela notificação.',
    'verify.expired' => 'Seu código de verificação foi expirado.',
    'password.forget.notification' => <<<'WHATSAPP'
Você está recebendo esta notificação porque recebemos uma solicitação de redefinição de senha para sua conta.

:url

Este link de redefinição de senha expirará em :count minutos.
Se você não solicitou uma redefinição de senha, nenhuma ação adicional será necessária.

Obrigado,
:app
WHATSAPP,
];
