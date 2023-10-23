<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnusedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('afterloginformcontrols');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('articlecategories');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('assignmentstatuses');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('consultationstatuses');
        Schema::dropIfExists('files');
        Schema::dropIfExists('temp_bucket_logs');
        Schema::dropIfExists('temp_festival_visits');
        Schema::dropIfExists('temp_oasis_attendants');
        Schema::dropIfExists('websitepages');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
