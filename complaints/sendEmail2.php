<?php
//SMTP = smtp.secureserver.net
ini_set("SMTP","ssl://smtp.gmail.com");
ini_set("smtp_port","465");
ini_set("sendmail_from","bloombeauty90@gmail.com");
//; For win32 only
//sendmail_from = webmaster@tutorialspoint.com

$to = "khadija.k.qaraqe@gmail.com";
$subject = "This is subject";

$message = "<b>This is HTML message.</b>";
$message .= "<h1>This is headline.</h1>";

$header = "From:bloombeauty90@gmail.com \r\n";
$header .= "Cc:bloombeauty90@gmail.com \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";

$retval = mail ($to,$subject,$message,$header);

if( $retval == true ) {
   echo "Message sent successfully...";
}else {
   echo "Message could not be sent...";
}
 // recipient email address
/* ini_set("SMTP","mail2.email.gov.ps");
ini_set("smtp_port","25");
$to = "kqaraqe@bethlehem.gov.ps";

// subject of the email
$subject = "Email with Attachment";

// message body
$message = "This is a sample email with attachment.";

// from
$from = "compliant@bethlehem.gov.ps";

// boundary
$boundary = uniqid();

// header information
$headers = "From: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\".$boundary.\"\r\n";

// attachment
//$attachment = chunk_split(base64_encode(file_get_contents('file.pdf')));

// message with attachment
$message = "--".$boundary."\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n\r\n";
$message .= chunk_split(base64_encode($message));
$message .= "--".$boundary."\r\n";
$message .= "Content-Type: application/octet-stream; name=\"file.pdf\"\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n";
$message .= "Content-Disposition: attachment; filename=\"file.pdf\"\r\n\r\n";
//$message .= $attachment."\r\n";
$message .= "--".$boundary."--";
mail($to, $subject, $message, $headers);
// send email
if (mail($to, $subject, $message, $headers)) {
    echo "Email with attachment sent successfully.";
} else {
    echo "Failed to send email with attachment.";
} */
/* ?>

<?php  */
/*  ini_set("SMTP","mail2.email.gov.ps");
ini_set("smtp_port","25");
$to = 'kqaraqe@bethlehem.gov.ps';

$subject = 'Hola';

$message = 'This is a test email.';

mail($to, $subject, $message);  */
/*  $f = fsockopen('mail2.email.gov.ps', 25) ;
if ($f !== false) {
    $res = fread($f, 1024) ;
    if (strlen($res) > 0 && strpos($res, '220') === 0) {
        echo "Success!" ;
    }
    else {
        echo "Error: " . $res ;
    }
}
fclose($f) ;  */

/* 
$ports[] = array('host'=>'interspire.smtp.com','number'=>25);
$ports[] = array('host'=>'interspire.smtp.com','number'=>2525);
$ports[] = array('host'=>'interspire.smtp.com','number'=>25025);
$ports[] = array('host'=>'helpme.interspire.smtp.com','number'=>80);

$ports[] = array('host'=>'google.com','number'=>80);
$ports[] = array('host'=>'smtp.gmail.com','number'=>587);
$ports[] = array('host'=>'smtp.gmail.com','number'=>465);
$ports[] = array('host'=>'pop.gmail.com','number'=>995);
$ports[] = array('host'=>'imap.gmail.com','number'=>993);

$ports[] = array('host'=>'ftp.mozilla.org','number'=>21);
$ports[] = array('host'=>'smtp2go.com','number'=>8025);

$ports[] = array('host'=>'relay.dnsexit.com','number'=>25);
$ports[] = array('host'=>'relay.dnsexit.com','number'=>26);
$ports[] = array('host'=>'relay.dnsexit.com','number'=>940);
$ports[] = array('host'=>'relay.dnsexit.com','number'=>8001);
$ports[] = array('host'=>'relay.dnsexit.com','number'=>2525);
$ports[] = array('host'=>'relay.dnsexit.com','number'=>80);

$ports[] = array('host'=>'mail.authsmtp.com','number'=>23);
$ports[] = array('host'=>'mail.authsmtp.com','number'=>25);
$ports[] = array('host'=>'mail.authsmtp.com','number'=>26);
$ports[] = array('host'=>'mail.authsmtp.com','number'=>2525);
$ports[] = array('host'=>'213.6.146.186','number'=>25);
foreach ($ports as $port)
{
    //$connection = @fsockopen($port['host'], $port['number']);
    $connection = @fsockopen($port['host'], $port['number'], $errno, $errstr, 5); // 5 second timeout for each port.

    if (is_resource($connection))
    {
        echo '<h2>' . $port['host'] . ':' . $port['number'] . '  is open. </h2>' . "\n";

        fclose($connection);
    }

    else
    {
        echo '<h2>' . $port['host'] . ':' . $port['number'] . ' is not responding.</h2>' . "\n";
    }
} */

?>