<?php

use App\Enums\ConsultationAppointmentTimesStatus;
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
        Schema::create('consultation_appointment_times', function (Blueprint $table) {
            $table->id();
            $table->dateTimeTz('start');
            $table->dateTimeTz('end');
            $table->decimal('price');
            $table->char('user_id', 24)->nullable()->default(null);
            $table->enum('status', ConsultationAppointmentTimesStatus::casesAsArrayString());
            $table->bigInteger('consultation_announcement_id')->unsigned();
            $table->foreign('consultation_announcement_id', 'appointment_times_consultation_announcement_fk')
                  ->references('id')
                  ->on('consultation_announcements')
                  ->cascadeOnDelete();

            $table->unique(['consultation_announcement_id', 'start', 'end'], 'appointment_times_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultation_appointment_times');
    }
};
