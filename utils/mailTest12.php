<?php 
$to="damiano.perri@studenti.unipg.it";
$subject="senza parametro -f";
$message = "email di prova, con header modificato";
$file="../views/Certificates/testByAdmin/new/20170906--Damiano--Perri--damiano.perri@studenti.unipg.it--GC1--ntc.it--ats.perugia.it.pdf";
$filename="Certificate.pdf";
$content = file_get_contents($file);
$content = chunk_split(base64_encode($content));
$uid = md5(uniqid(time()));
$name = basename($file);
$header  ="From: Prova <damiano.perri@gmail.com>\r\n";
$header .= "Reply-To: Prova <damiano.perri@gmail.com>\r\n";
$header .= "Return-Path: Prova <damiano.perri@gmail.com>\r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n";
$header .= "X-Priority: 3\r\n";
$header .= "X-Mailer: PHP". phpversion() ."\r\n";

echo $file."<br>";
if(file_exists($file))
        echo "File trovato<br><br>";
else die("File non trovato");
// header

// message & attachment
$nmessage = "--".$uid."\r\n";
$nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
$nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$nmessage .= $message."\r\n\r\n";
$nmessage .= "--".$uid."\r\n";
$nmessage .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
$nmessage .= "Content-Transfer-Encoding: base64\r\n";
$nmessage .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
$nmessage .= $content."\r\n\r\n";
$nmessage .= "--".$uid."--";
if (mail(
                $to,
        $subject,
        $nmessage,        
        $header
        )
    )
    echo "ACK";
else echo "NACK";


?>



