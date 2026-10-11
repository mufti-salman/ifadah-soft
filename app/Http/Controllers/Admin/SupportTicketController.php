<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Super Admin: সব Support Ticket দেখাবে।
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with([
            'institution',
            'creator',
            'assignedUser',
        ])->latest();

        // Ticket Number অথবা Subject দিয়ে Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority Filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query
            ->paginate(15)
            ->withQueryString();

        // Dashboard Summary
        $stats = [
            'total' => SupportTicket::count(),

            'open' => SupportTicket::where(
                'status',
                'open'
            )->count(),

            'in_progress' => SupportTicket::where(
                'status',
                'in_progress'
            )->count(),

            'resolved' => SupportTicket::where(
                'status',
                'resolved'
            )->count(),

            'closed' => SupportTicket::where(
                'status',
                'closed'
            )->count(),
        ];

        return view('super-admin.support-tickets.index', compact(
            'tickets',
            'stats'
        ));
    }

    /**
     * একটি Ticket-এর বিস্তারিত তথ্য দেখাবে।
     */
public function show(SupportTicket $supportTicket)
{
    $supportTicket->load([
        'institution',
        'creator',
        'assignedUser',
        'replies.user',
    ]);

    $supportAdmins = $this->getActiveSupportAdmins();

    return view(
        'super-admin.support-tickets.show',
        compact(
            'supportTicket',
            'supportAdmins'
        )
    );
}

    /**
 * Update support ticket status and priority.
 */
public function update(Request $request, SupportTicket $supportTicket)
{
    $validated = $request->validate([
        'status' => [
            'required',
            'in:open,in_progress,resolved,closed',
        ],

        'priority' => [
            'required',
            'in:low,medium,high,urgent',
        ],
    ]);

    $supportTicket->update([
        'status' => $validated['status'],
        'priority' => $validated['priority'],
    ]);

    return redirect()
        ->route('dashboard.support-tickets.show', $supportTicket->id)
        ->with('success', 'সাপোর্ট টিকিট সফলভাবে আপডেট হয়েছে।');
}

/**
 * Assign a ticket to an active Support Admin.
 */
public function assign(Request $request, SupportTicket $supportTicket)
{
    $validated = $request->validate([
        'assigned_to' => [
            'required',
            'integer',
            'exists:users,id',
        ],
    ]);

    $supportAdminRole = Role::where(
        'slug',
        'support_admin'
    )->first();

    if (!$supportAdminRole) {
        return back()->with(
            'error',
            'Support Admin Role পাওয়া যায়নি।'
        );
    }

    $supportAdmin = User::where(
        'id',
        $validated['assigned_to']
    )
        ->where('role_id', $supportAdminRole->id)
        ->where('status', 1)
        ->first();

    if (!$supportAdmin) {
        return back()
            ->withInput()
            ->with(
                'error',
                'নির্বাচিত Active Support Admin পাওয়া যায়নি।'
            );
    }

    $supportTicket->assigned_to = $supportAdmin->id;
    $supportTicket->save();

    return back()->with(
        'success',
        'Support Admin সফলভাবে Assign করা হয়েছে।'
    );
}


/**
 * Return active Support Admins for the assignment form.
 */
private function getActiveSupportAdmins()
{
    $supportAdminRole = Role::where(
        'slug',
        'support_admin'
    )->first();

    if (!$supportAdminRole) {
        return collect();
    }

    return User::where(
        'role_id',
        $supportAdminRole->id
    )
        ->where('status', 1)
        ->orderBy('name')
        ->get([
            'id',
            'name',
            'name_bn',
            'phone',
        ]);
}

/**
 * Store a reply for a support ticket.
 */
public function reply(Request $request, SupportTicket $supportTicket)
{
    $validated = $request->validate([
        'message' => ['required', 'string', 'max:10000'],
    ]);

    $supportTicket->replies()->create([
        'user_id' => $request->user()->id,
        'message' => trim($validated['message']),
    ]);

    return redirect()
        ->route('dashboard.support-tickets.show', $supportTicket->id)
        ->with('success', 'রিপ্লাই সফলভাবে পাঠানো হয়েছে।');
}
}