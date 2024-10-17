<?php

namespace App\Observers;

use App\Models\User;
use App\Models\PreDemandLetter;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */


     public function deleting(User $user)
     {

        //User must be delete using foreach loop

         // Find all PreDemandLetters where the user's ID is in the bd_agency_agree array
         PreDemandLetter::whereJsonContains('bd_agency_agree', strval($user->id))->get()
             ->each(function ($preDemandLetter) use ($user) {
                 // Remove the user's ID from the array
                 $bd_agency_agree = $preDemandLetter->bd_agency_agree;
                 $filtered_agency_list = array_filter($bd_agency_agree, function ($agencyId) use ($user) {
                     return $agencyId !== strval($user->id);  // Keep IDs that don't match the deleted user
                 });

                 // Update the PreDemandLetter's bd_agency_agree array
                 $preDemandLetter->bd_agency_agree = array_values($filtered_agency_list);
                 $preDemandLetter->save();
             });
     }



    /**
     * Handle the User "deleted" event.
     */


    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {

    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
