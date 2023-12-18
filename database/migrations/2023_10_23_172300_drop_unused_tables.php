<?php

use Illuminate\Database\Migrations\Migration;
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
        Schema::dropIfExists('assignment_major');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('assignmentstatuses');
        Schema::dropIfExists('consultant_user');
        Schema::dropIfExists('consultation_major');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('consultationstatuses');
        Schema::dropIfExists('disk_file');
        Schema::dropIfExists('educationalcontent_file');
        Schema::dropIfExists('files');
        Schema::dropIfExists('temp_bucket_logs');
        Schema::dropIfExists('temp_festival_visits');
        Schema::dropIfExists('temp_oasis_attendants');
        Schema::dropIfExists('userseensitepages');
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
