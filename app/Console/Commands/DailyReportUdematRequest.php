<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReportUdematMail;
use App\Models\User;

class DailyReportUdematRequest extends Command
{
    protected $signature = 'events:daily-udemat-report';

    protected $description = 'Genera un reporte diario y lo envía a servicios.psicologia@unam.mx';

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'app:daily-report-udemat-request';

    /**
     * The console command description.
     *
     * @var string
     */
    //protected $description = 'Command description';
    

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = now()->subDay();
        $events = Event::whereDate('created_at', $yesterday->toDateString())
            ->where(function ($query) {
                $query->where('recording_required', 1)
                      ->orWhere('transmission_required', 1)
                      ->orWhere('photography_required', 1);
            })->get();

        if ($events->isEmpty()) {
            $this->info('No hay eventos con servicios especiales registrados el día anterior.');
            return Command::SUCCESS;
        }

        $eventCount = $events->count();
        $eventData = $events->map(function ($event) {
            $responsible=User::find($event->responsible_id);
            return [
                'event_id'=>$event->id,
                'name' => $event->title,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'responsible_name' => $responsible->name,
                'responsible_email' => $responsible->email,
                'transmission_required' => $event->transmission_required ? 'Sí' : 'No',
                'recording_required' => $event->recording_required ? 'Sí' : 'No',
                'photography_required' => $event->photography_required ? 'Sí' : 'No',
            ];
        })->toArray();

        Mail::to('udemat.psicologia@unam.mx')
            ->send(new DailyReportUdematMail($eventCount, $eventData));

        $this->info("Correo enviado con {$eventCount} eventos registrados el día anterior.");
        return Command::SUCCESS;
    }
}
