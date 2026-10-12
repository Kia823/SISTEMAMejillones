<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolicitudBajaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $codigoActivo,
        public string $motivo,
        public string $verificador,
        public string $ambiente,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud de baja de activo')
            ->line('El verificador ' . $this->verificador . ' ha registrado una solicitud de baja para el activo ' . $this->codigoActivo . '.')
            ->line('Ambiente: ' . $this->ambiente)
            ->line('Motivo: ' . $this->motivo)
            ->action('Revisar verificaciones', url('/verificaciones-aprobacion'))
            ->line('Debe revisarlo a la brevedad.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'codigo_activo' => $this->codigoActivo,
            'motivo' => $this->motivo,
            'verificador' => $this->verificador,
            'ambiente' => $this->ambiente,
            'tipo' => 'solicitud_baja',
        ];
    }
}
