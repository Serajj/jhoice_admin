<?php

namespace App\Models;
use Eloquent as Model;



class InstallLink extends Model
{
    
	protected $table = 'install_links';
     protected $fillable = [
        'email', 
        'phone',
        'type',
        
    ];
}
