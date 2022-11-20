<?php

namespace App\Http\Controllers;
use App\Models\People;

use Illuminate\Http\Request;
use App\Mail\ReminderEmail;
use Mail;

class PeopleController extends Controller
{
/**
 * The index function returns a view called index, and passes the variable people to the view
 * 
 * @return The index view is being returned.
 */
    public function index()
    {
        $people = People::All();
        return view('index',compact('people'));
    }

/**
 * It imports a csv file to the database.
 * 
 * @param Request request The request object.
 * 
 * @return the view of the page.
 */
    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xls,xlx,csv|max:2048',
        ]);

        $fileName = $request->file('file')->getClientOriginalName();
     
        $path = $request->file('file')->storeAs('uploads',$fileName);
        //dd($path);
        $people = [];

        if (($open = fopen(storage_path() . "/app/uploads/" .$fileName, "r")) !== FALSE) {
            $row = 0;
            $skip_row_number = array("1");
            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                $row++;	
                if (in_array($row, $skip_row_number))	
                {   
                    //Header kihagyása
                    continue; 
                }else{
                    $people[] = $data;
                }   
            }
            for($i=0;$i<count($people);$i++){
                //Ha létezik a TAJ szám a táblában, akkor ne töltse fel az adatot
                if(!People::where('taj', '=', $people[$i][0])->exists()){
                    $record=array(
                        "taj"=>$people[$i][0],
                        "name"=>$people[$i][1],
                        "email"=>$people[$i][2],
                        "expires_at"=>$people[$i][3]
                    );
                    People::create($record);
                }
            }    
            fclose($open);
        }else{
            return redirect()->back()->with('error','Fájl megnyitási hiba!');;
        }
        return redirect()->back()->with('success','Sikeres importálás!');
     }

/**
 * It takes the id of a user, and renews his/her document for one month
 * 
 * @param Request request The request object.
 * @param id The id of the user you want to renew.
 * 
 * @return the view of the people.index page.
 */
     public function renews(Request $request, $id){
        $people=People::find($id);
        $new_date = date('Y-m-d H:i:s', strtotime(' + 1 months'));
        $people->expires_at = $new_date;
        $people->reminder_2week = NULL;
        $people->reminder_1week = NULL;
        $people->reminder_3day = NULL;
        $people->reminder_1day = NULL;
        if($people->save()){
            return redirect()->back()->with('success','Sikeres újítás!');
        }else{
            return redirect()->back()->with('error','Sikertelen!');
        }
     }

/**
 * It sends an email to the given email address.
 * 
 * @param email The email address you want to send the email to.
 * @param title The title of the email
 * @param body The body of the email.
 * 
 * @return A boolean value.
 */
     public function sendEmail($email,$title,$body){
        $mailData = [
            'title' => 'Mail from Localhost',
            'body' => 'Lejáró dokumentum'
        ];  
        //Létező SMTP adatok nélkül hibára fut a küldés
        return Mail::to($email)->send(new ReminderEmail($mailData));
     }

/**
 * It checks the database for people who have a document that expires in 1 day, 3 days, 1 week or 2
 * weeks. If the document expires in one of these timeframes, and the person hasn't received a reminder
 * email yet, it sends an email to the person
 * 
 * @return The view is being returned.
 */
     public function cron(){
        $people = People::All();
        $now = date('Y-m-d H:i:s');
        $now_plus1day = $date = date('Y-m-d', strtotime("+1 day")); 
        $now_plus3day = $date = date('Y-m-d', strtotime("+3 day")); 
        $now_plus1week = $date = date('Y-m-d', strtotime("+1 week")); 
        $now_plus2week = $date = date('Y-m-d', strtotime("+2 week")); 
       foreach($people as $p){
   
          $expires_at = date('Y-m-d', strtotime($p->expires_at));
          echo 'Név:'.$p->name.'<br>';
          echo 'Lejár ekkor:'.$p->expires_at.'<br>';
    
        //1day
          if(strtotime($now_plus1day) == strtotime($expires_at) && $p->reminder_1day == NULL){
            $person = People::find($p->id);
            $person->reminder_1day = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 1 nap múlva lejár'));
                echo $p->email.' címre email kiküldve, mert 1nap múlva lejár a dokumentuma<br>';   
            }else{
                echo 'Adatbázis mező frissítés hiba!';
            }
        //3day                     
          }else if(strtotime($now_plus3day) == strtotime($expires_at) && $p->reminder_3day == NULL){
            $person = People::find($p->id);
            $person->reminder_3day = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 3 nap múlva lejár'));
                echo $p->email.' címre email kiküldve, mert 3nap múlva lejár a dokumentuma<br>'; 
            }else{
                echo 'Adatbázis mező frissítés hiba!';
            }
        //1week
          }else if(strtotime($now_plus1week) == strtotime($expires_at) && $p->reminder_1week == NULL){
            $person = People::find($p->id);
            $person->reminder_1week = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 1 hét múlva lejár'));
                echo $p->email.' címre email kiküldve, mert 1hét múlva lejár a dokumentuma<br>'; 
            }else{
                echo 'Adatbázis mező frissítés hiba!';
            }
        //2week
          }else if(strtotime($now_plus2week) == strtotime($expires_at) && $p->reminder_2week == NULL){
            $person = People::find($p->id);
            $person->reminder_2week = $now;          
            if($person->save()){
                //sendEmail($p->email,'Emlékeztető','Dokumentuma 2hét múlva lejár'));
                echo $p->email.' címre email kiküldve, mert 2hét múlva lejár a dokumentuma<br>'; 
            }else{
                echo 'Adatbázis mező frissítés hiba!';
            }
          }else{
               echo 'Nincs teendő<br>';
          }
          echo '<br><br>';
       }
        return view('cronjobtest',compact('people'));
     }
}
