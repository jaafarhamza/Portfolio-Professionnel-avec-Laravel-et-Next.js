<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Handle the contact form submission.
     */
    public function submit(Request $request)
    {
        // Basic validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);
    
        try {
            // Step 1: Try database operation first
            $contactMessage = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject ?? 'No Subject',
                'message' => $request->message,
                'is_read' => false,
            ]);
    
            try {
                Mail::to(config('mail.admin_email', 'jaafar.hamza711@gmail.com'))
                    ->send(new ContactFormMail($validated));
            } catch (\Exception $mailError) {
                \Log::error('Mail sending failed: ' . $mailError->getMessage());
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Your message has been saved successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all contact messages (admin only).
     */
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(15);
        return response()->json($messages);
    }

    /**
     * Mark a message as read (admin only).
     */
    public function markAsRead(ContactMessage $message)
    {
        $message->is_read = true;
        $message->save();

        return response()->json($message);
    }

    /**
     * Delete a message (admin only).
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return response()->json(null, 204);
    }
}