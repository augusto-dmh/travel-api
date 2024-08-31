<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Travel extends Model
{
    use HasFactory;

    protected $table = 'travels';

    protected $fillable = [
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days'
    ];

    protected function numberOfNights(): Attribute {
        return Attribute::make(
            get: fn () => $this->number_of_days - 1,
        );
    }

    public function name(): Attribute {
        return Attribute::make(set: function ($value) {
            $slug = str($value)->slug();
            $count = 1;

            $firstIteration = true;
            while (Travel::where('slug', $slug)->exists()) {
                if ($firstIteration) {
                    $slug = $slug . '-' . $count;
                    $count++;
                    $firstIteration = false;
                    continue;
                }

                $lastDashPosition = strrpos($slug, '-');
                $slug = substr($slug, 0, $lastDashPosition);

                $slug = $slug . '-' . $count;
                $count++;
            }

            return [
                'name' => $value,
                'slug' => $slug,
            ];
        });
    }

    public function tours(): HasMany {
        return $this->hasMany(Tour::class);
    }
}
