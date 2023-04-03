<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Project;
use App\Models\ProjectCollabarator;
use App\Models\Task;
use App\Models\TaskCollabarator;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Task::select('tasks.*','projects.name as project')->join('projects', 'projects.id', '=', 'tasks.project_id');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords) {

                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('tasks.name', 'like', "%{$keywords}%")->orWhere('projects.name', 'like', "%{$keywords}%")->orWhereDate("tasks.created_at", $date);
                    }
                })
                ->editColumn('collabarator', function ($row) {
                    return count($row->collabarators);
                })

                ->editColumn('status', function ($row) {
                    $select_status = '<select name="task_status" onchange="return changeStatus(this.value, '.$row->id.')">
                                        <option value="pending" '.($row->status == 'pending' ? 'selected' : '').'>Pending</option>
                                        <option value="incompleted" '.($row->status == 'incompleted' ? 'selected' : '').'>Incompleted</option>
                                        <option value="completed" '.($row->status == 'completed' ? 'selected' : '').'>Completed</option>
                                        </select>';
                    if (access()->hasAccess('task', 'status', $row->id)) {

                        $status = $select_status;
                    } else {
                        $status = ucfirst($row->status);
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $del_btn = ''; $edit_btn = '';
                    if (access()->hasAccess('task', 'edit', $row->id)) {
                        $edit_btn = '<a href="javascript:void(0);" onclick="return  addTaskModel(
                                            ' . $row->id . ')" class="btn btn-primary btn-light-primary mx-1 w-30px h-30px" > 
                                        <i class="bi bi-pencil-square"></i>
                                    </a>';
                                    
                        $del_btn = '<a href="javascript:void(0);" onclick="return deleteTask(' . $row->id . ')" class="btn btn-danger btn-light-danger mx-1 w-30px h-30px" > 
                                        <i class="bi bi-trash3"></i></a>';
                    }

                    return $edit_btn. $del_btn;
                })
                ->rawColumns(['action', 'status', 'collabarator']);
            return $datatables->make(true);
        }
        return view('admin.pages.tasks.list');
    }

    public function add_edit(Request $request)
    {
        $title = 'Add Task';
        $id = $request->id ?? '';
        $project_id = $request->project_id ?? '';
        $info = '';
        if ($id) {
            $info = Task::find($id);
            
            $title = 'Update Task';
        }
        $projects = Project::select('projects.*')->join('project_collabarators', 'project_collabarators.project_id', '=', 'projects.id')
                    ->where('projects.status', 'active')
                    ->where('project_collabarators.user_id', auth()->id())
                    ->when( $project_id != '', function($q) use($project_id) {
                        return $q->where('projects.id', $project_id);
                    })
                    ->groupBy('project_collabarators.project_id')
                    ->get();
        $users = User::where('id', '!=', auth()->id())->where('is_super_admin', 0)->get();
        return view('admin.pages.tasks.add_edit', compact('users', 'title', 'info', 'projects'));
    }

    public function gerProjectCollabarators(Request $request)
    {
        $project_id = $request->project_id;
        return ProjectCollabarator::with(['user'])->where('project_id', $project_id)->get();
    }

    public function save(Request $request)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
            'task_name' => 'required|string|unique:tasks,name,' . $id,
            'project_id' => 'required',
            'collabarator' => 'required'
        ]);

        if ($validator->passes()) {

            $collabarators = [auth()->id()];
            if ($request->collabarator && count($request->collabarator) > 0) {
                $collabarators = array_merge($collabarators, $request->collabarator);
            }
            $ins['project_id']      = $request->project_id;
            $ins['name']              = $request->task_name;
            $ins['description']       = $request->description;
            $ins['status']            = 'pending';

            $error                      = 0;
            $task = Task::updateOrCreate(['id' => $id], $ins);
            $id = $task->id;
            // dd( $collabarators );
            if ($collabarators) {
                TaskCollabarator::where('task_id', $id)->update(['status' => 'inactive']);
                foreach ($collabarators as $item) {

                    $insParam['task_id'] = $id;
                    $insParam['user_id'] = $item;
                    $insParam['is_owner'] = $item == auth()->id() ? 'yes' : 'no';
                    $insParam['status'] = 'active';
                    TaskCollabarator::updateOrCreate(['task_id' => $id, 'user_id' => $item], $insParam);
                }
            }
            if( $request->id ) {
                $notificaton_message = $request->task_name .' task is updated by '.auth()->user()->name.' at '.date('d-m-Y H:i A');

            } else {

                $notificaton_message = $request->task_name .' task is added by '.auth()->user()->name.' at '.date('d-m-Y H:i A');
            }
            event(new MessageSent($notificaton_message));
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error                      = 1;
            $message                    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Task::find($id);
        $info->status   = $status;
        $info->update();

        $notificaton_message = $request->task_name .' task status is updated to '.$status.' by '.auth()->user()->name.' at '.date('d-m-Y H:i A');
        event(new MessageSent($notificaton_message));
        return response()->json(['message' => "You changed the Task status!", 'status' => 1]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Task::find($id);
        $notificaton_message = $info->name .' task status is deleted by '.auth()->user()->name.' at '.date('d-m-Y H:i A');
        event(new MessageSent($notificaton_message));
        $info->delete();

        return response()->json(['message' => "Successfully deleted Task!", 'status' => 1]);
    }

}
