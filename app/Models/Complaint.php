<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
   
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subject',
        'email',
        'address',
        'description',
        'file_path',
        'created_at',
        'updated_at',
        'is_verified',
        'is_assigned',
        'assigned_to',
        'assigned_date',
        'is_resolved',
    ]; 
}
