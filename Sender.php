<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';


spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
  });

class Sender {
    
    public $client_tmp;
    public $admin_tmp;
    public $order;

    private $smtp_server = 'mail.motiontalentafrica.co.ke';
    // private $smtp_port = 465;
    private $sender_email = 'mail@motiontalentafrica.co.ke';
    private $email_pass= 'm0t10nt@lent';



    public function __construct(){
        $tmp = fopen("order_email_tmp.php", "r") or die ("unable to open file!");
        $this->client_tmp = fread($tmp, filesize("order_email_tmp.php"));
        fclose($tmp);
    }

    public function getOrder($id){
        $url = 'https://b23.pythonanywhere.com/orders/list/'.$id.'/';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $this->order = json_decode(curl_exec($ch));
        return $this->order;  
    }

    public function drawTable(){
        

        $len =  sizeof($this->order->order_items);
        $items = $this->order->order_items;
        $rows = '';

        for ($i=0; $i<$len; $i++){
            $new_row = '
            <tr style="height: 20px">
                <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee"> '. $items[$i]->quantity .'</td>
                <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee">'. $items[$i]->name .'</td>
                <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee" align="right">Ksh '. number_format($items[$i]->buying_price * $items[$i]->quantity)  .' </td>
            </tr>
            ';
            $rows .= $new_row;
        }

        return $rows;
    }


    public function make_plain_admin_email(){
        
        $list = '';
        $len = sizeof($this->order->order_items);
        $items = $this->order->order_items;

        for ($i=0; $i<$len; $i++){
            $new_line = $items[$i]->name.'x'.$items[$i]->quantity.' @ Ksh '. number_format($items[$i]->buying_price);
            $list .= $new_line;
        }

        $total = 'Total: Ksh '. number_format($this->order->amount);
        $list .= $total;


        $email_tmp = 
        '
            An order () has been made by ;

            The items include;

                '. $list .'
        ';

        return $email_tmp;
    }

    public function make_admin_html_email(){
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

                                            '. 
                                            
                                            $this->drawTable()
                                            
                                            .'
            
                                            <tr style="height: 20px">
                                                <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee" colspan="2" align="right"><b>Delivery Fee</b></td>
                                                <td style="height:30px; padding: 20px px; border-bottom: 1px solid #eee" align="right">Ksh 250</td>
                                            </tr>
                                            <tr style="height: 20px">
                                                <td style="height:30px; padding: 20px px; " colspan="2" align="right"><b>Total</b></td>
                                                <td style="height:30px; padding: 20px px;"  align="right">Ksh '. number_format($this->order->amount) .'</td>
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


    public function send_email($id){

        $mail = new PHPMailer;
        $ftf = $this->getOrder($id);

        try {
            // Server settings
            // $mail->SMTPDebug = 2;
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            // $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = $this->smtp_server;                    // Set the SMTP server to send through
            // $mail->SMTPAuth   = false;                                   // Enable SMTP authentication
            // // $mail->Username   = $this->sender_email;                     // SMTP username
            // // $mail->Password   = $this->email_pass;                               // SMTP password
            // // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            // $mail->Port       = 25;                                    // TCP port to connect to
        
            //Recipients
            $mail->setFrom('mail@motiontalentafrica.co.ke', 'Motion Talent Africa Store');
            $mail->addAddress($this->order->user_email);     // Add a recipient
            $mail->addReplyTo('info@motiontalentafrica.co.ke', 'Information');
            $mail->addBCC('mail@motiontalentafrica.co.ke');
        
            
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'A new order has been recieved!';
            $mail->Body    = $this->make_admin_html_email();
            $mail->AltBody = $this->make_plain_admin_email();
        
            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }



}