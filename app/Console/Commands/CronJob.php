<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\ReminderEmail;
use App\Models\People;

class CronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'E-mail reminder about an expiring document';

     /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*Mit tegyen a Cron */

        $people = People::All();
        $now = date('Y-m-d H:i:s');
        $now_plus1day = $date = date('Y-m-d', strtotime("+1 day")); 
        $now_plus3day = $date = date('Y-m-d', strtotime("+3 day")); 
        $now_plus1week = $date = date('Y-m-d', strtotime("+1 week")); 
        $now_plus2week = $date = date('Y-m-d', strtotime("+2 week")); 
       foreach($people as $p){
          $expires_at = date('Y-m-d', strtotime($p->expires_at)); 
        //1day
          if(strtotime($now_plus1day) == strtotime($expires_at) && $p->reminder_1day == NULL){
            $person = People::find($p->id);
            $person->reminder_1day = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 1 nap múlva lejár'));
                $this->info('Successfully sent emails.');
            }else{
                $this->info('Database error.');
            }
        //3day                     
          }else if(strtotime($now_plus3day) == strtotime($expires_at) && $p->reminder_3day == NULL){
            $person = People::find($p->id);
            $person->reminder_3day = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 3 nap múlva lejár'));
                $this->info('Successfully sent emails.');
            }else{
                $this->info('Database error.');
            }
        //1week
          }else if(strtotime($now_plus1week) == strtotime($expires_at) && $p->reminder_1week == NULL){
            $person = People::find($p->id);
            $person->reminder_1week = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 1 hét múlva lejár'));
                $this->info('Successfully sent emails.');
            }else{
                $this->info('Database error.');
            }
        //2week
          }else if(strtotime($now_plus2week) == strtotime($expires_at) && $p->reminder_2week == NULL){
            $person = People::find($p->id);
            $person->reminder_2week = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 2hét múlva lejár'));
                $this->info('Successfully sent emails.');
            }else{
                $this->info('Database error.');
            }
          }else{
            $this->info('Nothing.');
          }
       }

        //return Command::SUCCESS;
    }
}
