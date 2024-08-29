<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER format_price_before_insert_on_tours
            BEFORE INSERT ON tours
            FOR EACH ROW
            BEGIN
                SET NEW.price = NEW.price * 100;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER format_price_before_update_on_tours
            BEFORE UPDATE ON tours
            FOR EACH ROW
            BEGIN
                SET NEW.price = NEW.price * 100;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS format_price_before_insert_on_tours');
    }
};
