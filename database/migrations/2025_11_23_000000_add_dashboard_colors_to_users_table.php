<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('dashboard_color_primary', 7)->nullable()->after('remember_token');
            $table->string('dashboard_color_secondary', 7)->nullable()->after('dashboard_color_primary');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dashboard_color_primary', 'dashboard_color_secondary']);
        });
    }
};
