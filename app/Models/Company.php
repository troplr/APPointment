<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use App\Http\Requests\UrlParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    /**
     * The current company.
     *
     * @var Company
     */
    private $company;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'address', 'phonenumber'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * All the days of the week
     *
     * @var array
     */
    public $days = [
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday'
    ];

    /**
     * Get and return the current company.
     *
     * @return Company
     */
    public function get()
    {
        // If we already have the company, return it.
        if ($this->company) {
            return $this->company;
        }

        return $this->company = Auth::user()->company ?? Company::where('subdomain', UrlParser::getSubdomain())->first();
    }

    /**
     * Get users
     *
     * @param  boolean $customers
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function users($customers = false)
    {
        $users = $this->hasMany(User::class);

        if ($customers) {
            return $users->where('role', User::role('customer'))->get();
        }

        return $users;
    }

    /**
     * A hasMany relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointmentTypes()
    {
        return $this->hasMany(AppointmentType::class);
    }

    /**
     * Decode the json array
     *
     * @return object
     */
    public function openingHours()
    {
        return json_decode($this->opening_hours) ?? (object) $this->days;
    }

    /**
     * A hasManyThrough relationship
     *
     * @param  array $between
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function appointments($between = [])
    {
        $appointments = $this->hasManyThrough(Appointment::class, AppointmentType::class);

        if (! empty($between)) {
            $appointments->whereBetween('scheduled_at', $between);
        }

        return $appointments;
    }

    /**
     * Get the start and end time from the given date as a Carbon object
     *
     * @param  Carbon $date
     * @return array
     */
    public function dayTimes($date)
    {
        $day = strtolower($date->format('D'));
        $opening_hours = $this->openingHours()->$day;
        $times = [];

        foreach ($opening_hours as $key => $opening_hour) {
            // This array consists of the hour and minutes
            list($hour, $minutes) = explode(':', $opening_hours->$key);

            $times[$key] = Carbon::parse($date)->setTime($hour, $minutes);
        }

        return $times;
    }
}
