<?php

use App\Enums\ConsultationAnnouncementCategories;
use App\Enums\ConsultationAnnouncementType;
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
        Schema::create('consultation_announcements', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ConsultationAnnouncementCategories::casesAsArrayString());
            $table->longText('description');
            $table->char('professional_id', 24);
            # $table->foreignId('professional_id')->constrained('professionals', 'user_id');
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
        Schema::dropIfExists('consultation_announcements');
    }
};
