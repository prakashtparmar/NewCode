<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Tenant;

class EnsureTenantDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the current domain
        $domain = $request->getHost();
        
        // Skip for central domains
        $centralDomains = ['127.0.0.1', 'localhost'];
        if (in_array($domain, $centralDomains)) {
            return $next($request);
        }
        
        // Find tenant by domain
        $tenant = Tenant::whereHas('domains', function($query) use ($domain) {
            $query->where('domain', $domain);
        })->first();
        
        if ($tenant) {
            $databaseName = $tenant->getDatabaseName();
            
            // Ensure the tenant database is set in the connection
            if ($databaseName && $databaseName !== 'default_tenant_db') {
                // Set the database name in the tenant connection
                Config::set("database.connections.tenant.database", $databaseName);
                
                // Purge and reconnect to ensure the new database is used
                DB::purge('tenant');
                DB::reconnect('tenant');
                
                // Initialize tenancy context
                tenancy()->initialize($tenant);
                
                // Verify the connection is working
                try {
                    $pdo = DB::connection('tenant')->getPdo();
                    $currentDb = DB::connection('tenant')->getDatabaseName();
                    
                    // Log for debugging
                    \Log::info("Tenant database connected: {$currentDb} for domain: {$domain}");
                    
                } catch (\Exception $e) {
                    // Log the error
                    \Log::error("Failed to connect to tenant database '{$databaseName}' for domain '{$domain}': " . $e->getMessage());
                    
                    // Return error response
                    return response()->json([
                        'error' => 'Database connection failed',
                        'message' => "Cannot connect to tenant database: {$databaseName}",
                        'domain' => $domain,
                        'tenant_id' => $tenant->id
                    ], 500);
                }
            }
        } else {
            // If no tenant found for the domain, return error
            return response()->json([
                'error' => 'Tenant not found',
                'message' => "No tenant found for domain: {$domain}",
                'domain' => $domain
            ], 404);
        }
        
        return $next($request);
    }
}
