<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth0\Login\Facade\Auth0;

class Outgo extends Model
{
    use Uuids;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $table = 'outgoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'quantity', 'notes', 'share_outgo', 'points', 'gas_liters', 'gas_price', 'finished_at',
        'created_at',
    ];

    protected $appends = [
        'category',
        'am_i_owner',
        //'distributions',
    ];

    /**
     * Get the related vehicle.
     */
    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle', 'vehicle_id');
    }

    /**
     * Get the related user.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Get the original outgo.
     */
    public function originalOutgo()
    {
        return $this->belongsTo('App\Outgo', 'original_outgo');
    }

    /**
     * Get the distributed outgoes of outgo.
     */
    public function distributions()
    {
        return $this->hasMany('App\Outgo', 'original_outgo');
    }

    /**
     * Get the related receiver user.
     */
    public function receiver()
    {
        return $this->belongsTo('App\User', 'receiver_id');
    }

    /**
     * Get the outgo category.
     */
    public function outgoCategory()
    {
        return $this->belongsTo('App\OutgoCategory', 'outgo_category_id');
    }

    public function getCategoryAttribute()
    {
        return $this->outgoCategory()->first()->getKeyName();
    }

    /**
     * Get the related action.
     */
    public function action()
    {
        return $this->hasOne('App\Action');
    }

    public function getAmIOwnerAttribute()
    {
        $userInfo = Auth0::jwtUser();
        $user = User::where('auth0id', $userInfo->sub)->first();

        return $this->user()->first()->id === $user->id;
    }

    /*public function getDistributionsAttribute()
    {
        return $this->distributions()->with(['user', 'receiver'])->get();
    }*/
}
