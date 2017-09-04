<?php

use MX\Parser\EmailParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers MX\Parser\EmailParser
 */
final class PlainEmailParserTest extends TestCase // TODO: More detailed tests
{
    public function testDefaultTypeWithBody()
    {
        $file = __DIR__.'/Samples/no-type_body.txt';
        $this->assertEquals('9ea114ce7e74915863336f022e04560d', md5_file($file));
        $parser = EmailParser::parse(__DIR__.'/Samples/no-type_body.txt');

        $this->assertCount(21, $parser->headers);
        $this->assertCount(21, $parser->headers->indexed);
        $this->assertEquals('contact@example.info', $parser->headers->Delivered_To);
        $this->assertEquals('from root by server.example.eu with local (Exim 4.89) (envelope-from <root@server.example.eu>) id 1doZ04-0011vC-20 for root@server.example.eu; Sun, 03 Sep 2017 19:48:00 +0200', $parser->headers->Received);
        $this->assertEquals('ADKCNb7oIyTDE2MOjWx1A7R8KhA0Zm88yByNyw5GkklPGyqUFyEHsy4XWYbWaJjNBujem6R6NRh8', $parser->headers->{'X-Google-Smtp-Source'});
        $this->assertEquals('by 2.3.4.5 with SMTP id l38mr11128311qtk.6.1504460800914; Sun, 03 Sep 2017 10:48:00 -0700 (PDT)', $parser->headers->X_Received);
        $this->assertEquals('i=1; a=rsa-sha256; t=1504460880; cv=none; d=google.com; s=arc-20160816; b=uCGEBOuyd3THFllkRx4laLnBUjs2CY+K0JMr8w/MpnV5a5hIHUQjmGXVZK1EBmKkw3 yFiukm/EEwjooBOA51yjeYa13teuMvR57uSYtpQpJgoyHQ+eUz98f/dAuwEfvw8fYbXv r4xFx3alw9o27k333Ya7/To4fY566pxhkqjCPEjduyFUM7CYDdbCo5405/1ySiHUfHwI S4rYRmxtH+HlA+XHbyRGcjjcbnxQYwEzdM8tpHmWDYuO2ke8ff4U8ReOD0s7R1fJmjh7 pMi2/398iUVUYB2hVvhKA3h7ZWKGxaHTMQj16WGnRyOLo+uhJS9Gq2x725eVSZoZzIND SiTQ==', $parser->headers->ARC_Seal);
        $this->assertEquals('i=1; a=rsa-sha256; c=relaxed/relaxed; d=google.com; s=arc-20160816; h=date:message-id:from:subject:to:arc-authentication-results; bh=D4aON9tMAV8ZCz/3EHLCYvzw3085sL0hRZ5m9PtmWLw=; b=Sfkjb9ynAe4zQb29wzCcIbWwhcLilVyNIJD8fzp/kxY913t9RMvqW4ReW/gFFNqrWJ 70wNpCA1n6MDHzRLKps8HTDmsiCU8jlU9G3wYlPksM39o3Y8/EogA+nSycTHVyFKrCTI zMMr58JdCH7/ehzwULWv0SLeVYf7Jd3dwF/xMZxx4SaA+Au/9mGFfiShbnymcP40SFuD cV8nITN638AiKHVAZuxSswtMZaMr6pStxO/VtvbsGSuFbYZR2JN1eIqvWOklpJMlQtdc lV6tetH3mH3JhLu5Xen2/El+pLDQGQ+ccNbhowZdOAKqYh/Rhn0nGx+JzFGO/mywZDmR Su8w==', $parser->headers->ARC_Message_Signature);
        $this->assertEquals('i=1; mx.google.com; spf=pass (google.com: best guess record for domain of root@server.example.eu designates 3.4.5.6 as permitted sender) smtp.mailfrom=root@server.example.eu', $parser->headers->ARC_Authentication_Results);
        $this->assertEquals('<root@server.example.eu>', $parser->headers->Return_Path);
        $this->assertEquals('pass (google.com: best guess record for domain of root@server.example.eu designates 3.4.5.6 as permitted sender) client-ip=3.4.5.6;', $parser->headers->Received_SPF);
        $this->assertEquals('mx.google.com; spf=pass (google.com: best guess record for domain of root@server.example.eu designates 3.4.5.6 as permitted sender) smtp.mailfrom=root@server.example.eu', $parser->headers->Authentication_Results);
        $this->assertEquals('root@server.example.eu', $parser->headers->To);
        $this->assertEquals('lfd on server.example.eu: Suspicious File Alert', $parser->headers->Subject);
        $this->assertEquals('<root@server.example.eu>', $parser->headers->From);
        $this->assertEquals('<E1doZ04-0011vC-20@server.example.eu>', $parser->headers->Message_Id);
        $this->assertEquals('Sun, 03 Sep 2017 19:48:00 +0200', $parser->headers->Date);
        $this->assertEquals('Sender Address Domain - server.example.eu', $parser->headers->X_AntiAbuse);
        $this->assertEquals('server.example.eu: sender_ident via received_protocol == local: root/primary_hostname/system user', $parser->headers->X_Get_Message_Sender_Via);
        $this->assertEquals('server.example.eu: root', $parser->headers->X_Authenticated_Sender);
        $this->assertEquals('/usr/local/user/bin/perl', $parser->headers->X_Source);
        $this->assertEquals('lfd - (child) suspicious file alert for /var/tmp/AjgKoFymT', $parser->headers->X_Source_Args);
        $this->assertEquals('/etc/csf', $parser->headers->X_Source_Dir);

        $this->assertNull($parser->headers->boundary);
        $this->assertEquals('ascii', $parser->headers->charset);
        $this->assertEquals('7bit', $parser->headers->encoding);
        $this->assertEquals('text', $parser->headers->type);
        $this->assertEquals('plain', $parser->headers->subtype);

        $this->assertCount(0, $parser->body->attachments);
        $this->assertEquals(
            "Time:   Sun Sep  3 19:48:00 2017 +0200\r\nFile:   /var/tmp/AjgKoFymT\r\nReason: Linux Binary\r\nOwner:  : (1288:1289)\r\nAction: No action taken\r\n",
            $parser->body->raw
        );
        $this->assertEquals($parser->body->raw, $parser->body->html);
        $this->assertEquals($parser->body->raw, $parser->body->plain);
    }
}
