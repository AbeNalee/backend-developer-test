<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\AchievementService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * Many-to-many relationship with achievements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('progress', 'unlocked_at')
            ->withTimestamps();
    }

    /**
     * Many-to-many relationship with badges.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withTimestamps();
    }

    /**
     * Check if user has achievement
     */
    public function hasAchievement($achievement)
    {
        // Check if the user has the achievement by its name
        if (is_string($achievement)) {
            return $this->achievements()->where('name', $achievement)->exists();
        }

        // Check if the user has the achievement by its ID
        if (is_int($achievement)) {
            return $this->achievements->contains('id', $achievement);
        }

        if ($achievement instanceof Achievement) {
            return $this->achievements->contains('id', $achievement->id);
        }

        return false;
    }

    /**
     * check if user has unlocked achievement
     */
    public function hasUnlockedAchievement($achievement)
    {
        return $this->hasAchievement($achievement) &&
            $this->achievements()->where('name', $achievement)->first()->pivot->is_unlocked;
    }

    /**
     * Check if user has badge
     * @param $badge
     * @return bool
     */
    public function hasBadge($badge)
    {
        // Check if the user has the badge by its name
        if (is_string($badge)) {
            return $this->badges()->where('name', $badge)->exists();
        }

        // Check if the user has the badge by its ID
        if (is_int($badge)) {
            return $this->badges->contains('id', $badge);
        }

        if ($badge instanceof Badge) {
            return $this->badges->contains('id', $badge->id);
        }

        return false;
    }

    public function getCurrentBadge()
    {
        if (! $this->badges()->count()) {
            $badge =  Badge::orderBy('required_achievements')->first();
            $this->badges()->attach($badge);
        }
        return $this->badges()->orderBy('required_achievements', 'desc')->first();
    }

    public function getNextBadge()
    {
        // Get the number of achievements the user has unlocked
        $userAchievementsCount = $this->achievements->count();

        // Query for the next badge based on the number of achievements
        return Badge::where('required_achievements', '>', $userAchievementsCount)
            ->orderBy('required_achievements')
            ->first();
    }
}

