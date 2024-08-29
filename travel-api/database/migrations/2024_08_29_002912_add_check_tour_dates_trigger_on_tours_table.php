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
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER check_tour_dates_before_insert_on_tours
            BEFORE INSERT ON tours
            FOR EACH ROW
            BEGIN
                DECLARE travel_days INT;
                DECLARE date_diff INT;

                SELECT number_of_days INTO travel_days FROM travels WHERE id = NEW.travel_id;
                SET date_diff = DATEDIFF(NEW.ending_date, NEW.starting_date);

                IF date_diff != travel_days THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Inconsistent tour dates with travel number of days";
                END IF;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_tour_dates_before_update_on_tours
            BEFORE UPDATE ON tours
            FOR EACH ROW
            BEGIN
                DECLARE travel_days INT;
                DECLARE date_diff INT;

                SELECT number_of_days INTO travel_days FROM travels WHERE id = NEW.travel_id;
                SET date_diff = DATEDIFF(NEW.ending_date, NEW.starting_date);

                IF date_diff != travel_days THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Inconsistent tour dates with travel number of days";
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS check_tour_dates_before_insert_on_tours');
        DB::unprepared('DROP TRIGGER IF EXISTS check_tour_dates_before_update_on_tours');
    }
};
