<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class MailController extends Controller
{
    
    public function index()
    {
        return view('send-mail');
    }

   
    public function store(Request $request)
    {
        try {
          
            $email = auth()->user()->email;
            $subject = "Let's Connect";
            $body = "test";

            Mail::to($email)->send(new SendMail($subject, $body));

            
            return redirect('/success');
        } catch (Exception $e) {
            
            return redirect('/success');
        }
    }
}