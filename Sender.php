<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require './vendor/PHPMailer/src/Exception.php';
// require './vendor/PHPMailer/src/PHPMailer.php';
// require './vendor/PHPMailer/src/SMTP.php';


spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
  });

class Sender {
    
    public $client_tmp;
    public $admin_tmp;
    public $order;

    private $smtp_server = 'host25.safaricombusiness.co.ke';
    private $smtp_port = '465';
    private $sender_email = 'mail@motiontalentafrica.co.ke';
    private $email_pass= 'm0t10nt@lent';



    public function __construct(){
        $tmp = fopen("order_email_tmp.php", "r") or die ("unable to open file!");
        $this->client_tmp = fread($tmp, filesize("order_email_tmp.php"));
        fclose($tmp);
    }

    public function getOrder($id){
        $url = 'http://localhost:8000/orders/list/'.$id.'/';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $this->order = json_decode(curl_exec($ch));
        return $this->order;  
    }

    public function make_admin_email($id){

        $ftf = $this->getOrder($id);

        $email_tmp  =
        '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Your Order is successful!</title>
        </head>
        <body style="width: 100%; margin: 0; padding: 0;background-color: #fff;">
        
            <table style="width: 600px; margin: 0 auto;  border: 1px solid #d4af37;">
                <tbody>
                    <tr style="background-color: #fffdd0;">
                        <th style="border-bottom: 1px solid #d4af37; padding: 20px 0px">
                            <img src="http://store.motiontalentafrica.co.ke/static/media/MTA.0336bcf8.svg" alt="">
                        </th>
                    </tr>
        
        
        
                    <tr>
                        <td style="padding: 20px">
                            
                            <h2 style="text-align: center">Order Recieved!</h2>
        
                            <p>
                                Dear Manager,
                            </p>
        
                            <p>An order <b>('. $this->order->name .')</b> on the Motion Talent Africa Store has just been made by '. $this->order->user_fname. ' ' .  $this->order->user_lname .' ('.  $this->order->user_email .'). Head over to the <a href="#" style="color: #d4af37">dashboard </a> to confirm the order.</p>
                            <p>The Payment mode is cash on delivery.</p>
                        </td>
                    </tr>
        
                    <tr>
                        <td style="padding: 20px">
                            <table style="width: 100%; border: .5px solid #d4af37;background-color: #fffdd0; padding: 10px">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%; border-bottom: 1px solid #d4af37; height: 30px;" align="left">Qty</th>
                                        <th style="border-bottom: 1px solid #d4af37; height: 30px;" align="left">Item</th>
                                        <th style="width: 30%; border-bottom: 1px solid #d4af37; height: 30px;" align="right">Price</th>
                                    </tr>

                                    
                                    <tr style="height: 20px">
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee">1</td>
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee">Shenai</td>
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee" align="right">7,000</td>
                                    </tr>
        
                                    <tr style="height: 20px">
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee">2</td>
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee">Flutes</td>
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee" align="right">10,000</td>
                                    </tr>
                                    <tr style="height: 20px">
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee" colspan="2" align="right"><b>Delivery Fee</b></td>
                                        <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee" align="right">250</td>
                                    </tr>
                                    <tr style="height: 20px">
                                        <td style="height:30px; padding: 20px px; " colspan="2" align="right"><b>Total</b></td>
                                        <td style="height:30px; padding: 20px px;"  align="right">17,250</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
        
                    <tr>
                        <td style="padding: 20px">
                            <p>
                                Warmest Regards,<br>
                                The Motion Talent Africa Store Email Bot.
                            </p> 
                        </td>
                    </tr>
        
                    
                </tbody>
            </table>    
        
        </body>
        </html>

        ';

        return $email_tmp;
    }
}