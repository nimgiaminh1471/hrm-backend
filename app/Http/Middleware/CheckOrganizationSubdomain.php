<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganizationSubdomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $request->route('subdomain');
        
        $organization = Organization::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found or inactive'
            ], 404);
        }

        // Add organization to request for later use
        $request->merge(['organization' => $organization]);
        
        return $next($request);
    }
} 