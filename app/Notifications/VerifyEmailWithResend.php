<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailWithResend extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60), // Tautan verifikasi berlaku selama 60 menit
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $resendUrl = URL::signedRoute(
            'verification.resend_new',
            ['user' => $notifiable->getKey()]
        );

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda')
            ->line('Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.')

            // Tombol utama untuk verifikasi
            ->action('Verifikasi Alamat Email', $verificationUrl)

            ->line('Tautan verifikasi ini akan kedaluwarsa dalam 60 menit.')

            // Tautan sekunder untuk kirim ulang (menggunakan Markdown)
            ->line("Jika tombol tidak berfungsi atau tautan kedaluwarsa, Anda dapat meminta yang baru dengan mengklik tautan berikut: [Kirim Ulang Tautan Verifikasi]({$resendUrl})")

            ->line('Jika Anda tidak membuat akun, abaikan email ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
