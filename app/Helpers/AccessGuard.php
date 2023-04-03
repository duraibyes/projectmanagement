<?php
namespace App\Helpers;

use App\Models\ProjectCollabarator;
use App\Models\TaskCollabarator;
use App\Models\User;

class AccessGuard {
    public function hasAccess($module, $permission_module = '', $id = '') {
        if( auth()->user()->is_super_admin ) {
            return true;
        } else {
            $info = User::find(auth()->id());
            /***
             * 1.project model 
             */
            switch ($module) {
                case 'project':
                    if( $id ) {
                        
                        $collabarator = ProjectCollabarator::where('project_id', $id)
                                        ->where(['user_id' => $info->id, 'is_owner' => 'yes'])->first();
                        if( $collabarator ) {
                            return true;
                        }
                    }
                    break;
                case 'task':
                    if( $id ) {
                        
                        $collabarator = TaskCollabarator::where('task_id', $id)
                                        ->where(['user_id' => $info->id, 'is_owner' => 'yes'])->first();
                        if( $collabarator ) {
                            return true;
                        }
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
            
            
            return false;
        }   
    }

}