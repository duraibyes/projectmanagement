<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCollabarator;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Project::select('*');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords) {

                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('projects.title', 'like', "%{$keywords}%")->orWhere('projects.name', 'like', "%{$keywords}%")->orWhereDate("projects.created_at", $date);
                    }
                })
                ->editColumn('collabarator', function ($row) {
                    return count($row->collabarators);
                })

                ->editColumn('status', function ($row) {
                    if (access()->hasAccess('project', 'status', $row->id)) {

                        $status = '<a href="javascript:void(0);" class="text-decoration-none text-white btn btn-sm bg-' . (($row->status == 'active') ? 'success' : 'danger') . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-custom-class="custom-tooltip"
                        data-bs-title="Click to ' . (($row->status == 'active') ? 'InActive' : 'Active') . '" onclick="return changeStatus(' . $row->id . ',\'' . (($row->status == 'active') ? 'inactive' : 'active') . '\')">' . ucfirst($row->status) . '</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="text-decoration-none text-white btn btn-sm bg-' . (($row->status == 'active') ? 'success' : 'danger') . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-custom-class="custom-tooltip"
                        data-bs-title="Click to ' . (($row->status == 'active') ? 'InActive' : 'Active') . '" >' . ucfirst($row->status) . '</a>';
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
                ->editColumn('name', function ($row) {
                    $name = '<a href="'.route('project.task',['project_id' => $row->id ]).'">'.$row->name.'</a>';
                    return $name;
                })

                ->addColumn('action', function ($row) {
                    $del_btn = ''; $edit_btn = '';
                    if (access()->hasAccess('project', 'edit', $row->id)) {
                        $edit_btn = '<a href="javascript:void(0);" onclick="return  addProjectModel(
                                            ' . $row->id . ')" class="btn btn-primary btn-light-primary mx-1 w-30px h-30px" > 
                                        <i class="bi bi-pencil-square"></i>
                                    </a>';
                                    
                        $del_btn = '<a href="javascript:void(0);" onclick="return deleteProject(' . $row->id . ')" class="btn btn-danger btn-light-danger mx-1 w-30px h-30px" > 
                                        <i class="bi bi-trash3"></i></a>';
                    }

                    return $edit_btn. $del_btn;
                })
                ->rawColumns(['action', 'status', 'collabarator', 'name']);
            return $datatables->make(true);
        }
        return view('admin.pages.projects.list');
    }

    public function add_edit(Request $request)
    {
        $title = 'Add Projects';
        $id = $request->id ?? '';
        $info = '';
        if ($id) {
            $info = Project::find($id);
            $title = 'Update Projects';
        }
        $users = User::where('id', '!=', auth()->id())->where('is_super_admin', 0)->get();
        return view('admin.pages.projects.add_edit', compact('users', 'title', 'info'));
    }

    public function save(Request $request)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
            'project_name' => 'required|string|unique:projects,name,' . $id . ',id,deleted_at,NULL',
        ]);

        if ($validator->passes()) {

            $collabarators = [auth()->id()];
            if ($request->collabarator && count($request->collabarator) > 0) {
                $collabarators = array_merge($collabarators, $request->collabarator);
            }

            $ins['name']              = $request->project_name;
            $ins['description']       = $request->description;
            $ins['status']            = 'active';

            $error                      = 0;
            $project = Project::updateOrCreate(['id' => $id], $ins);
            $id = $project->id;

            if ($collabarators) {
                ProjectCollabarator::where('project_id', $id)->update(['status' => 'deleted']);
                foreach ($collabarators as $item) {
                    $insParam['task_id'] = $id;
                    $insParam['user_id'] = $item;
                    $insParam['is_owner'] = $item == auth()->id() ? 'yes' : 'no';
                    $insParam['status'] = 'pending';

                    ProjectCollabarator::updateOrCreate(['project_id' => $id, 'user_id' => $item], $insParam);
                }
            }
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
        $info           = Project::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message' => "You changed the Project status!", 'status' => 1]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Project::find($id);
        $info->delete();

        return response()->json(['message' => "Successfully deleted Project!", 'status' => 1]);
    }

    public function taskView(Request $request)
    {
        $project_id = $request->project_id;
        
        if ($request->ajax()) {
            $data = Task::select('tasks.*','projects.name as project')
                        ->join('projects', 'projects.id', '=', 'tasks.project_id')
                        ->where('project_id', $project_id);
            $keywords = $request->get('search')['value'];
            $datatables = Datatables::of($data)
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
        $from = 'project';
        return view('admin.pages.tasks.list', compact('from', 'project_id'));
        
    }
}
