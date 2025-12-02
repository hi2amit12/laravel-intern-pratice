<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumni_committee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_member_id')
                ->constrained('alumni_members')
                ->onDelete('cascade');
            $table->string('position');
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique('alumni_member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumni_committee');
    }
};
