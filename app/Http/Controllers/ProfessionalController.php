<?php

namespace App\Http\Controllers;

use App\Models\ConsultationAnnouncement;
use App\Models\ConsultationAppointmentTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfessionalController extends Controller
{

    public function storeAnnounce(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $consultation_announcement = new ConsultationAnnouncement();
            $consultation_announcement->fill($request->all());
            $consultation_announcement->professional_id = $request->user()->id;
            $consultation_announcement->save();

            $consultation_announcement->createByArray($request->appointment_times);

            return new JsonResponse(compact('consultation_announcement'));
        });
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

}
