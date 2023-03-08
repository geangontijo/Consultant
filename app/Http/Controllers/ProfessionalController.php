<?php

namespace App\Http\Controllers;

use App\Enums\ConsultationAnnouncementCategories;
use App\Enums\ConsultationAnnouncementCategoriesPtBr;
use App\Models\ConsultationAnnouncement;
use App\Models\ConsultationAppointmentTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfessionalController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function storeAnnounce(Request $request)
    {
        $request->validate([
            'category' => ['required', new Enum(ConsultationAnnouncementCategoriesPtBr::class)],
            'description' => ['required', 'string', 'max:255'],
            'appointment_times' => ['required', 'array'],
            'appointment_times.*.start' => ['required', 'iso_date'],
            'appointment_times.*.end' => ['required', 'iso_date'],
            'appointment_times.*.price' => ['required', 'decimal:2'],
        ]);

        $enum = $request->enum('category', ConsultationAnnouncementCategoriesPtBr::class);
        $category = $enum->getBaseEnum();

        DB::transaction(function () use ($category, $request) {

            $consultationAnnouncement = new ConsultationAnnouncement([
                'category' => $category,
                'description' => $request->description,
                'professional_id' => $request->user()->id,
            ]);

            $consultationAnnouncement->save();
            $consultationAnnouncement->createByArray($request->appointment_times);
        });

        Cache::tags('home')->clear();
        return Redirect::route('dashboard');
    }

    public function updateAnnounce(Request $request)
    {
//        return DB::transaction(function () use ($request) {
//            /** @var ConsultationAnnouncement $consultation_announcement */
//            $consultation_announcement = DB::table('consultation_announcement')->findOr(
//                id: $request->id,
//                callback: function () {
//                    throw new \Exception('Consultation announcement not found');
//                }
//            )->get();
//
//            $consultation_announcement->fill($request->all());
//            $consultation_announcement->save();
//
//            $consultation_announcement->createByArray($request->create_appointment_times);
//            if (!empty($request->delete_appointment_times)) {
//                DB::table('consultation_appointment_times')
//                  ->whereIn('consultation_announcement_id', implode(',', $request->delete_appointment_times))
//                  ->delete();
//            }
//
//            $consultation_announcement->appointment_times();
//
//            return new JsonResponse(compact('consultation_announcement'));
//        });
    }

    public function destroyAnnounce(int $id)
    {
        ConsultationAnnouncement::findOrFail($id)->delete();
    }
}
