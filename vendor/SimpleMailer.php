<?php
class SimpleMailer
{
    private $to = [];
    private $from = [];
    private $bcc = [];
    private $cc = [];
    private $replyTo = [];
    private $embeddedImage = [];
    private $attachment = [];
    private $subject = '';
    private $message = '';
    private $html = false;

    public function addAttachment($path, $filename = '')
    {
        if (file_exists($path)) {
            $this->attachment[] = [
                'path' => $path,
                'fileName' => $filename
            ];
        }
    }

    public function addBcc($email)
    {
        $this->bcc[] = $email;
    }

    public function addCc($email)
    {
        $this->cc[] = $email;
    }

    public function addEmbeddedImage($imagePath, $contentId) {
        if (file_exists($imagePath)) {
            $this->embeddedImage[] = [
                "path" => $imagePath,
                "contentId" => $contentId
            ];
        }
    }

    public function addTo($email, $name = '')
    {
        $this->to[] = [
            "email" => $email,
            "name" => $name
        ];
    }

    public function clearTo()
    {
        $this->to = [];
    }

    public function clearCc()
    {
        $this->cc = [];
    }

    public function clearBcc()
    {
        $this->bcc = [];
    }

    public function clearAttachment()
    {
        $this->attachment = [];
    }

    public function clearEmbeddedImage()
    {
        $this->embeddedImage = [];
    }

    public function clearReplyTo()
    {
        $this->replyTo = [];
    }

    public function setFrom($email, $name = '')
    {
        $this->from["email"] = $email;
        $this->from["name"] = $name;
    }

    public function addReplyTo($email, $name = '')
    {
        $this->replyTo[] = [
            "email" => $email,
            "name" => $name
        ];
    }

    public function setHtml($html) {
        $this->html = $html;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function send()
    {
        if (empty($this->to) && empty($this->bcc) && empty($this->cc) || empty($this->from) || empty($this->message)) {
            return false;
        }
        $headers = [];
        $message = [];
        if (empty($this->from['name'])) {
            $headers[] = 'From: ' . $this->from["email"];
        } else {
            $headers[] = 'From: =?UTF-8?B?' . base64_encode($this->from["name"]) . '?= <' . $this->from['email'] . '>';
        }
        if (!empty($this->bcc)) {
            $headers[] = 'Bcc: ' . implode(', ', $this->bcc);
        }
        if (!empty($this->cc)) {
            $headers[] = 'Cc: ' . implode(', ', $this->cc);
        }
        $headers[] = 'Date: ' .date("D, d M Y H:i:s O");
        $headers[] = 'MIME-Version: 1.0';
        if (!empty($this->replyTo)) {
            $replyToString = '';
            foreach ($this->replyTo as $item) {
                if (!empty($item['name'])) {
                    $replyToString .= '=?UTF-8?B?' . base64_encode($item['name']) . '?=';
                    $replyToString .= ' <' . $item['email'] . '>, ';
                } else {
                    $replyToString .= $item['email'] . ', ';
                }
            }
            $replyToString = rtrim($replyToString, ', ');
            $headers[] = 'Reply-To: ' . $replyToString;
        }

        $headers[] = "X-Mailer: LibMail";
        $messageMime = $this->html ? 'text/html' : 'text/plain';
        if (empty($this->embeddedImage) && empty($this->attachment)) {
            $headers[] = "Content-Type: $messageMime; charset=\"utf-8\"";
            $headers[] = 'Content-Transfer-Encoding: base64';
            $message[] = base64_encode($this->message);
        } else {
            try {
                $chunkSeparator = bin2hex(random_bytes(16));
            } catch (Exception $e) {
                $chunkSeparator = "chunk";
            }
            $headers[] = "Content-Type: multipart/mixed; boundary=\"$chunkSeparator\"";
            $message[] = "--$chunkSeparator";
            $message[] = "Content-Type: $messageMime;charset=\"utf-8\"";
            $message[] = 'Content-Transfer-Encoding: base64';
            $message[] = '';
            $message[] = base64_encode($this->message);
            foreach ($this->embeddedImage as $image) {
                $mime = mime_content_type($image['path']);
                $base64 = base64_encode(file_get_contents($image['path']));
                $message[] = '';
                $message[] = "--$chunkSeparator";
                $message[] = "Content-Type: $mime";
                $message[] = 'Content-Transfer-Encoding: base64';
                $message[] = "Content-ID: $image[contentId]";
                $message[] = 'Content-Disposition: inline';
                $message[] = '';
                $message[] = $base64;
            }
            foreach ($this->attachment as $attachment) {
                $mime = mime_content_type($attachment['path']);
                $base64 = base64_encode(file_get_contents($attachment['path']));
                $message[] = '';
                $message[] = "--$chunkSeparator";
                if (!empty($attachment['fileName'])) {
                    $message[] = "Content-Type: $mime; name=\"$attachment[fileName]\"";
                } else {
                    $message[] = "Content-Type: $mime; name=\"" . basename($attachment['path']) . "\"";
                }
                $message[] = 'Content-Transfer-Encoding: base64';
                if (!empty($attachment['fileName'])) {
                    $message[] = "Content-Disposition: attachment; filename=\"$attachment[fileName]\"";
                } else {
                    $message[] = "Content-Disposition: attachment; filename=\"" . basename($attachment['path']) . "\"";
                }
                $message[] = 'Content-Disposition: attachment';
                $message[] = '';
                $message[] = $base64;
            }
            $message[] = '';
            $message[] = "--$chunkSeparator--";
        }
        $toString = '';
        foreach ($this->to as $item) {
            if (!empty($item['name'])) {
                $toString .= '=?UTF-8?B?' . base64_encode($item['name']) . '?=';
                $toString .= ' <' . $item['email'] . '>, ';
            } else {
                $toString .= $item['email'] . ', ';
            }
        }
        $toString = rtrim($toString, ', ');
        return mail($toString, $this->subject, implode("\r\n", $message), implode("\r\n", $headers));
    }
}