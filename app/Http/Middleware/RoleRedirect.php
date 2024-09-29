<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $panel = filament()->getCurrentPanel()->getId();


        if ($user->role === 'Manager') {
            return $next($request);
        }

        // If the user is not authenticated, redirect to login
        if (!$user) {
            return redirect('/login');
        }


        switch ($user->role) {
            case 'Manager':
                if ($panel !== 'management') {
                    return redirect()->route('filament.management.pages.dashboard'); // Manager Dashboard
                }
                break;
            case 'FrontDesk':
                if ($panel !== 'frontdesk') {
                    return redirect()->route('filament.frontdesk.pages.dashboard'); // FrontDesk Dashboard
                }
                break;
            case 'Housekeeper':
                if ($panel !== 'housekeeper') {
                    return redirect()->route('filament.housekeeper.pages.dashboard'); // Housekeeper Dashboard
                }
                break;
            case 'Restaurant':
                if ($panel !== 'restaurant') {
                    return redirect()->route('filament.restaurant.pages.dashboard'); // Restaurant Dashboard
                }
                break;
            default:
                return redirect('/login'); // If role is undefined
        }
        return $next($request);

    }
}
