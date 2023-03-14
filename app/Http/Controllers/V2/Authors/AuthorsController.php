<?php

namespace App\Http\Controllers\V2\Authors;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiUser;

use Illuminate\Support\Facades\Auth;

class AuthorsController extends Controller
{
    
    public function index(Request $request)
    {
        
        // If user is logged in or inside university's wifi return authors, filtering and then counting every announcement each one has
        $local_ip = $request->session()->get('local_ip', 0);                
        if ($local_ip == 1 or auth('api_v2')->check()) {
            $authors = ApiUser::select('id', 'name')->where('is_author', 1)
            ->withCount(['announcements'=>function ($query) use ($request){
                $query->tags(
                    request()->input('users', []),
                    request()->input('tags', []),
                    (request()->input('title', '')),
                    (request()->input('body', '')),
                    (request()->input('updatedAfter', '')),
                    (request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('name', 'asc')->get();
	    } 
        // Else return authors filtering and then counting every public announcement each one has
        else {
            $authors = ApiUser::select('id', 'name')->where('is_author', 1)
            ->withCount(['announcements'=>function ($query) use ($request){
                $query->whereHas('tags', function ($query) {
                    $query->where('is_public', 1);
                })
                ->tags(
                    request()->input('users', []),
                    request()->input('tags', []),
                    (request()->input('title', '')),
                    (request()->input('body', '')),
                    (request()->input('updatedAfter', '')),
                    (request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('name', 'asc')->get();            
        }   
        
        return $authors;
    }
}
