<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name','identity', 'last_name', 'phone', 'role_id', 'email', 'password', 'google_id', 'facebook_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function addNew($input)
    {
        $check = static::where('facebook_id', $input['facebook_id'])->first();

        if (is_null($check)) {
            return static::create($input);
        }
        return $check;
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function moderatedComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'moderated_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $role): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return $this->role && $this->role->name === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Scope a query to only include users with active subscriptions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithActiveSubscription($query)
    {
        return $query->whereHas('payments', function ($q) {
            $q->where('status', 'active')
              ->where('due_date', '>', now());
        });
    }

    /**
     * Scope a query to only include users who never subscribed.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithNeverSubscribed($query)
    {
        return $query->whereDoesntHave('payments');
    }

    /**
     * Scope a query to only include users with expired subscriptions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithExpiredSubscription($query)
    {
        return $query->whereHas('payments', function ($q) {
            $q->where('due_date', '<', now());
        })->whereDoesntHave('payments', function ($q) {
            $q->where('status', 'active')
              ->where('due_date', '>', now());
        });
    }

    /**
     * Get the user's subscriber status.
     *
     * @return string|null
     */
    public function getSubscriberStatusAttribute()
    {
        // Check for active subscription
        $activePayment = $this->payments()
            ->where('status', 'active')
            ->where('due_date', '>', now())
            ->exists();

        if ($activePayment) {
            return 'active';
        }

        // Check for expired subscription
        $expiredPayment = $this->payments()
            ->where('due_date', '<', now())
            ->exists();

        if ($expiredPayment) {
            return 'expired_non_renewed';
        }

        // Otherwise, never subscribed
        return 'never_subscribed';
    }

    /**
     * Get the user's last payment date.
     *
     * @return string|null
     */
    public function getLastPaymentDateAttribute()
    {
        $lastPayment = $this->payments()
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastPayment ? $lastPayment->created_at : null;
    }
}
