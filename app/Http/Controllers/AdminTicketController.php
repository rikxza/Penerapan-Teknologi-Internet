<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')->orderByDesc('created_at')->paginate(10);
        return view('admin.tickets.index', compact('tickets'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        $ticket->update([
            'admin_reply' => $request->reply,
            'status' => 'answered',
        ]);

        return redirect()->back()->with('status', 'Balasan terkirim!');
    }

    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'closed']);
        return redirect()->back()->with('status', 'Tiket ditutup!');
    }
}
