<?php

// Modules/GeneralSetting/Repositories/Eloquent/TestimonialRepository.php
namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\Testimonial;
use Modules\GeneralSetting\Repositories\Contracts\TestimonialInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class TestimonialRepository implements TestimonialInterface
{
    public function create(array $data): Testimonial
    {
        return Testimonial::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Testimonial::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        $testimonial = Testimonial::findOrFail($id);
        if ($testimonial->image) {
            Storage::delete($testimonial->image);
        }
        return $testimonial->delete();
    }

    public function find(int $id): ?Testimonial
    {
        return Testimonial::find($id);
    }

    public function all(array $filters = []): Collection
    {
        $query = Testimonial::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('customer_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('review', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['ratings']) && is_array($filters['ratings'])) {
            $query->whereIn('ratings', $filters['ratings']);
        }

        switch (strtolower($filters['sort'] ?? '')) {
            case 'ascending':
                $query->orderBy('created_at', 'asc');
                break;
            case 'last month':
                $query->whereBetween('created_at', [now()->subMonth(), now()]);
                break;
            case 'last 7 days':
                $query->whereBetween('created_at', [now()->subDays(7), now()]);
                break;
            case 'descending':
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    public function paginate(int $perPage = 10, array $filters = [])
    {
        $query = Testimonial::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('customer_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('review', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($perPage);
    }
}
