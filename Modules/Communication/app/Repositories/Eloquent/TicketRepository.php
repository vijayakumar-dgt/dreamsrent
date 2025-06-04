<?php

namespace Modules\Communication\Repositories\Eloquent;

use Modules\Communication\Models\Ticket;
use Modules\Communication\Models\TicketHistory;
use Modules\Communication\Repositories\Contracts\TicketInterface;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository implements TicketInterface
{
    public function index(): Collection
    {
        return Ticket::with([
            'user:id,name,email',
            'user.userDetail:id,user_id,first_name,last_name,profile_image',
            'category:id,name',
            'assignee:id,name,email',
            'assignee.userDetail:id,user_id,first_name,last_name,profile_image',
            'ticketHistories:id,ticket_id,user_id,description,created_by,updated_by,created_at',
            'ticketHistories.user:id,name,email',
            'ticketHistories.user.userDetail:id,user_id,first_name,last_name,profile_image',
        ])->get();
    }

    public function create(array $data): mixed
    {
        return Ticket::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $ticket = Ticket::findOrFail($id);
        return $ticket->update($data);
    }

    public function delete(int $id): bool
    {
        return Ticket::findOrFail($id)->delete();
    }

    public function find(int $id): ?object
    {
        return Ticket::with([
            'user:id,name,email',
            'user.userDetail:id,user_id,first_name,last_name,profile_image',
            'category:id,name',
            'assignee:id,name,email',
            'assignee.userDetail:id,user_id,first_name,last_name,profile_image',
            'ticketHistories:id,ticket_id,user_id,description,created_by,updated_by,created_at',
            'ticketHistories.user:id,name,email',
            'ticketHistories.user.userDetail:id,user_id,first_name,last_name,profile_image',
        ])->find($id);
    }

    public function getTicketsForUser(int $userId, int $userType, array $filters = []): Collection
    {
        $query = Ticket::with([
            'user:id,name,email',
            'user.userDetail:id,user_id,first_name,last_name,profile_image',
            'category:id,name',
            'assignee:id,name,email',
            'assignee.userDetail:id,user_id,first_name,last_name,profile_image',
            'ticketHistories:id,ticket_id,user_id,description,created_by,updated_by,created_at',
            'ticketHistories.user:id,name,email',
            'ticketHistories.user.userDetail:id,user_id,first_name,last_name,profile_image',
        ]);

        // Restrict by user type
        if ($userType === 1) {
            // Admin sees all tickets
        } elseif ($userType === 3) {
            $query->where('user_id', $userId);
        } elseif ($userType === 2) {
            $query->where('assignee_id', $userId);
        }

        // If ticketId is passed, return only that ticket
        if (!empty($filters['ticketId'])) {
            $query->where('id', $filters['ticketId']);
            return $query->get();
        }

        // Apply filters
        if (!empty($filters['priority'])) {
            $query->whereIn('priority', (array) $filters['priority']);
        }

        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('ticket_id', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('user', function ($q2) use ($filters) {
                        $q2->where('name', 'like', '%' . $filters['search'] . '%');
                    })
                    ->orWhereHas('category', function ($q2) use ($filters) {
                        $q2->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        // Sorting
        switch ($filters['sort_by'] ?? 'latest') {
            case 'ascending':
                $query->orderBy('created_at', 'asc');
                break;
            case 'descending':
                $query->orderBy('created_at', 'desc');
                break;
            case 'last month':
                $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                break;
            case 'last 7 days':
                $query->whereBetween('created_at', [now()->subDays(7), now()]);
                break;
            default:
                $query->latest();
                break;
        }

        return $query->get();
    }


    public function assignTicket(int $ticketId, int $assigneeId, ?string $reply = null): object
    {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->assignee_id = $assigneeId;
        $ticket->status = 2; // Assigned status
        $ticket->save();

        if ($reply) {
            $this->addHistory($ticketId, auth()->id(), $reply);
        }

        return $ticket;
    }

    public function updateStatus(int $ticketId, int $status, string $reply): object
    {
        $ticket = Ticket::findOrFail($ticketId);
        $currentStatus = (int) $ticket->status;

        $allowedTransitions = [
            1 => [2],
            2 => [3],
            3 => [4],
        ];

        if ($currentStatus !== $status) {
            if (!isset($allowedTransitions[$currentStatus]) || !in_array($status, $allowedTransitions[$currentStatus])) {
                throw new \Exception(__('admin.support.invalid_status_transition'), 403);
            }

            $ticket->status = $status;
        }

        $user = auth()->user();
        $ticket->updated_by = $user->id ?? null;
        $ticket->save();

        if ($currentStatus !== 3) {
            throw new \Exception(__('admin.support.reply_allowed_only_in_status_3'), 403);
        }

        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'description' => strip_tags($reply),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return $ticket;
    }

    public function addHistory(int $ticketId, int $userId, string $description): object
    {
        return TicketHistory::create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'description' => strip_tags($description),
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }
}
