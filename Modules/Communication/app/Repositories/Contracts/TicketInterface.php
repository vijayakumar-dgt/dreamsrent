<?php

namespace Modules\Communication\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TicketInterface
{
    public function index(): Collection;
    public function create(array $data): mixed;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function find(int $id): ?object;
    public function getTicketsForUser(int $userId, int $userType, array $filters = []): Collection;
    public function assignTicket(int $ticketId, int $assigneeId, ?string $reply = null): object;
    public function updateStatus(int $ticketId, int $status, string $reply): object;
    public function addHistory(int $ticketId, int $userId, string $description): object;
}