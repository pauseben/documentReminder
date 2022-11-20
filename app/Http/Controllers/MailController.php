<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\ReminderEmail;

class MailController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        $mailData = [
            'title' => 'Értesítő',
            'body' => 'Lejáró dokumentum értesítő email'
        ];
         
        Mail::to('pausz.bence@gmail.com')->send(new ReminderEmail($mailData));
           
        dd("Email is sent successfully.");
    }
}
