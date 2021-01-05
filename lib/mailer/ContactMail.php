<?php


namespace Lib\mailer;


class ContactMail
{
    public static function send(string $from, string $to , string $subject , string $message) : void
    {
        $headers = 'From: '. $from . "\r\n" .
            'Reply-To: '. $from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $content = '
            <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    </style>
                </head>
                <body style="width: 100vw;" class="d-flex flex-row align-items-center">
                    <div style="background-color: #505c90; color: #ffffff; width: 100vw">
                        <h1 style="text-align: center; padding-top: 20px; padding-bottom: 20px;">Vous avez reçu un message!</h1>
                    </div>
                
                    <div style="width: 80vw; margin: auto; margin-top: 60px">
                        <h4 style="margin-bottom: 8px;">De: ' . $from . '</h4>

                        <h4 style="margin-top: 40px; margin-bottom: 8px;">Message:</h4>
                        <p style="text-align: justify;">' . $message . '</p>
                    </div>
                
                    <div style="position: fixed; bottom: 0; left: 10vw; width: 80vw; border-top: 1px solid #505c90;">
                        <p style="text-align: center; margin-top: 20px; margin-bottom: 20px;">Copyright &copy; 2021 simplecoder.com</p>
                    </div>
                </body>
            </html>
        ';

        mail($to, $subject, $content, $headers);
    }
}