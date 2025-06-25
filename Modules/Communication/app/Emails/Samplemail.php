<?php

namespace Modules\Communication\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Samplemail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string Email body content
     */
    public string $messageBody;

    /**
     * @var string|null Attachment file path
     */
    public ?string $attachment;

    /**
 * @param array{subject: string, message: string, attachment?: string|null} $messageBody
 */
    public function __construct(array $messageBody)
    {
        $this->subject = $messageBody['subject'];
        $this->messageBody = $messageBody['message'];
        $this->attachment = $messageBody['attachment'] ?? null; // Add attachment path if provided
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $email = $this->subject($this->subject)
                      ->html($this->messageBody);

        // Check if attachment exists and attach it to the email
        if ($this->attachment && file_exists($this->attachment)) {
            $email->attach($this->attachment, [
                'as'   => basename($this->attachment), // Set the attachment name
                'mime' => mime_content_type($this->attachment), // Automatically detects mime type
            ]);
        }

        return $email;
    }
}
