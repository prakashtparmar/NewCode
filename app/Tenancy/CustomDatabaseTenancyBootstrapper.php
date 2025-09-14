<?php

namespace App\Tenancy;

use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Database\DatabaseManager;

class CustomDatabaseTenancyBootstrapper extends DatabaseTenancyBootstrapper
{
    public function __construct(DatabaseManager $database)
    {
        parent::__construct($database);
    }
    
    public function bootstrap($tenant)
    {
        $databaseName = $tenant->getDatabaseName();
        
        // Set the database name in the tenant connection
        Config::set("database.connections.tenant.database", $databaseName);
        
        // Purge and reconnect to ensure the new database is used
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Verify the connection is working
        try {
            $pdo = DB::connection('tenant')->getPdo();
            $currentDb = DB::connection('tenant')->getDatabaseName();
            
            // Log for debugging
            \Log::info("Custom bootstrapper: Connected to tenant database '{$currentDb}' for tenant '{$tenant->id}'");
            
        } catch (\Exception $e) {
            \Log::error("Custom bootstrapper failed: " . $e->getMessage());
            throw new \Exception("Failed to connect to tenant database '{$databaseName}': " . $e->getMessage());
        }
    }
}
