<?php

namespace Dorcas\ModulesPeopleLeaves\Http\Controllers;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class ModulesPeopleLeavesController extends Controller
{
    public function __construct()
    {
        $this->data = [
            'page' => ['title' => config('modules-people.title')],
            'header' => ['title' => config('modules-people-leaves.title')],
            'selectedMenu' => 'modules-people-leaves',
            'submenuConfig' => 'navigation-menu.modules-people.sub-menu',
            'submenuAction' => ''
        ];

    }

    public function index(Request $request, Sdk $sdk){
        try{
            $this->data['page']['title'] .= ' &rsaquo; Leaves';
            $this->data['header']['title'] = 'Leave';
            $this->data['selectedSubMenu'] = 'people-payroll-leaves';
            $this->data['submenuAction'] = '';

            $this->setViewUiResponse($request);

            return view('modules-people-leaves::index',$this->data);
        }catch (\Exception $e){
            $this->setViewUiResponse($request);
            return view('modules-people-leaves::index',$this->data);
        }


    }

    public function typesIndex(Request $request, Sdk $sdk){
        try {
            if(auth()->user()->is_employee === 1){
                return response(view('errors.404'), 404);
            }
            $this->data['page']['title'] .= ' &rsaquo; Leaves';
            $this->data['header']['title'] = '  Types';
            $this->data['selectedSubMenu'] = 'people-payroll-leaves';

            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $this->data['types'] = $this->getPeopleLeaveTypes($sdk);
            $this->data['approvals'] = $this->getPeopleApprovals($sdk);
            switch ($this->data){
                case !empty($this->data['types']):
                    $this->data['submenuAction'] .= '
                    <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu">
                          <a href="#" data-toggle="modal" data-target="#leave-type-add-modal" class="dropdown-item">Add New  Leave Type</a>
                          </div>
                          </div>';

            }
            return view('modules-people-leaves::Types/index', $this->data);


        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return view('modules-people-approval::Types/index', $this->data);

        }

    }

    public function searchLeaveTypes(Request $request, Sdk $sdk){
        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        # get the request parameters
        $path = ['types'];

        $query = $sdk->createLeavesResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching Leave Type.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function createLeaveType(Request $request, Sdk $sdk){
        try{
            $resource = $sdk->createLeavesResource();
            $resource = $resource
                ->addBodyParam('title',$request->title)
                ->addBodyParam('approval_id',$request->approval_id);
            $response = $resource->send('post',['types']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the Leave Type  '.$message);

            }
            return response()->json(['message'=>'Leave Type  Created Successfully'],200);

        }
        catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);


        }
    }

    public function singleLeaveType(Sdk $sdk, string $id){
        try {
            $response = $sdk->createLeavesResource()->send('get',['types',$id]);
            if(!$response->isSuccessful()){
                throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find the Leave Type');
            }
            $approval = $response->getData(true);
            return response()->json([$approval, 200]);
        }
        catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);

        }
    }

    public function updateLeaveType(Request $request, Sdk $sdk,string $id){
        try {
            $resource = $sdk->createLeavesResource();
            $resource = $resource->addBodyParam('title', $request->title);
            if($request->has('approval_id')){
                $resource->addBodyParam('approval_id',$request->approval_id);
            }
            $response = $resource->send('put', ['types',$id]);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the  Leave Type ' . $message);

            }
            return response()->json(['message' => ' Leave Type Updated Successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteLeaveType(Request $request, Sdk $sdk, string $id){
        try{
            $resource = $sdk->createLeavesResource();
            $response = $resource->send('delete', ['types',$id]);
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the Leave Type.');

            }
            $this->data = $response->getData();
            return response()->json($this->data);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }




    public function groupsIndex(Request $request, Sdk $sdk){
        try {
            if(auth()->user()->is_employee === 1){
                return response(view('errors.404'), 404);
            }
            $this->data['page']['title'] .= ' &rsaquo; Leaves';
            $this->data['header']['title'] = ' Groups';
            $this->data['selectedSubMenu'] = 'people-payroll-leaves';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $this->data['groups'] = $this->getPeopleLeaveGroups($sdk);
            switch ($this->data){
                case !empty($this->data['groups']):
                    $this->data['submenuAction'] .= '
                    <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu">
                          <a href="/mpe/leave-groups/create" class="dropdown-item">Add New  Leave Group</a>
                          </div>
                          </div>';

            }
            return view('modules-people-leaves::Groups/index', $this->data);


        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return view('modules-people-approval::Groups/index', $this->data);

        }
    }

    public function leaveGroupsForm(Request $request, Sdk $sdk)
    {
        try {
            if (auth()->user()->is_employee === 1) {
                return response(view('errors.404'), 404);
            }
            $this->data['selectedSubMenu'] = 'people-payroll-leaves';
            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $this->data['page']['title'] .= ' &rsaquo; Leave Groups';
            $this->data['header']['title'] ='Create Leave Group';
            $this->data['leaveTypes'] = $this->getPeopleLeaveTypes($sdk);
            $this->data['teams'] = $this->getTeams($sdk);
            $this->data['departments'] = $this->getDepartments($sdk);
            return view('modules-people-leaves::Groups/create', $this->data);
        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return redirect()->route('leave-groups-main');

        }

    }

    public function createLeaveGroup(Request $request, Sdk $sdk){
        try{
            $types = explode(",",$request->types);
            $resource = $sdk->createLeavesResource();
            $resource = $resource->addBodyParam('group_id',$request->group_id)
                ->addBodyParam('types',$types)
                ->addBodyParam('group_type',$request->group_type)
                ->addBodyParam('duration_days',$request->duration_days)
                ->addBodyParam('duration_term','annual');
            $response = $resource->send('post',['groups']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                $error = (tabler_ui_html_response(['Failed while adding the Leave Group  '.$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('leave-groups-main')->with('UiResponse', $error);

            }
            $message = (tabler_ui_html_response(['Leave Group Created Successfully']))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-groups-main')->with('UiResponse', $message);


        }
        catch (\Exception $e){
            $message = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-groups-main')->with('UiResponse', $message);

        }
    }

    public function searchLeaveGroups(Request $request, Sdk $sdk){
        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        # get the request parameters
        $path = ['groups'];

        $query = $sdk->createLeavesResource();
        $query = $query->addQueryArgument('limit', $limit)
            ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching Leave Group.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function singleLeaveGroup(Request $request, Sdk $sdk, string $id){
        try {
            if (auth()->user()->is_employee === 1) {
                return response(view('errors.404'), 404);
            }

            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $response = $sdk->createLeavesResource()->send('get', ['groups', $id]);
            if (!$response->isSuccessful()) {
                $response = (tabler_ui_html_response([$response->errors[0]['title'] ?? 'Could not find the Leave Group']))->setType(UiResponse::TYPE_ERROR);
                return Redirect::back()->with('UiResponse', $response)->with('UiResponse',$response);
            }
            $leaveGroup = $response->getData(true);
            $this->data['page']['title'] .= ' &rsaquo; Authorizer';
            $this->data['header']['title'] = ' Leave Group Update';
            $this->data['leaveGroup'] = $leaveGroup;
            $this->data['leaveTypes'] = $this->getPeopleLeaveTypes($sdk);
            $this->data['teams'] = $this->getTeams($sdk);
            $this->data['departments'] = $this->getDepartments($sdk);
            return view('modules-people-leaves::Groups/update', $this->data);
        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return Redirect::back();


        }
    }

    public function updateLeaveGroup(Request $request, Sdk $sdk, string $id){
        try{
           $types = explode(",",$request->types);
            $resource = $sdk->createLeavesResource();
            $resource = $resource->addBodyParam('group_id',$request->group_id)
                ->addBodyParam('types',(array) $types)
                ->addBodyParam('group_type',$request->group_type)
                ->addBodyParam('duration_days',$request->duration_days)
                ->addBodyParam('duration_term','annual');
            $response = $resource->send('put',['groups',$id]);

            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                $response = (tabler_ui_html_response(['Failed while adding the Leave Group  '.$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('leave-groups-main')->with('UiResponse', $response);

            }
            $response = (tabler_ui_html_response(['Leave Group Updated Successfully']))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-groups-main')->with('UiResponse', $response);


        }
        catch (\Exception $e){
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-groups-main')->with('UiResponse', $response);


        }
    }

    public function deleteLeaveGroup(Request $request, Sdk $sdk, string $id){
        try{
            $resource = $sdk->createLeavesResource();
            $response = $resource->send('delete', ['groups',$id]);
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the Leave Group.');

            }
            $this->data = $response->getData();
            return response()->json($this->data);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }



    public function requestIndex(Request $request, Sdk $sdk){
        try {
            $this->data['page']['title'] .= ' &rsaquo; Leaves';
            $this->data['header']['title'] = 'People Leaves Requests';
            $this->data['submenuAction'] = '';
            $this->data['args'] = $request->query->all();
            $this->data['requests'] = $this->getEmployeeLeaveRequest($sdk);
            $this->setViewUiResponse($request);
            switch ($this->data){
                case !empty($this->data['requests']):
                    $this->data['submenuAction'] .= '
                    <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu">
                          <a href="/mpe/leave-request/create" class="dropdown-item">Add New  Leave Request</a>
                          </div>
                          </div>';
            }
            return view('modules-people-leaves::Requests/index', $this->data);
        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return view('modules-people-approval::Requests/index', $this->data);

        }
    }

    private function getLeaveTypes(Sdk $sdk, $userId){
        $resource = $sdk->createLeavesResource();

        $response = $resource->send('get',['requests','types',$userId]);
        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->getData();
    }

    public function leaveRequestForm(Request $request, Sdk $sdk)
    {
        try {
            $userId = auth()->user()->id;
            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            if ($this->getLeaveTypes($sdk,$userId) === null){
                $error = (tabler_ui_html_response(['Failed no Leave Configurations found for you ']))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('leave-request-main')->with('UiResponse', $error);
            }
            $this->data['leaveTypes'] = $this->getLeaveTypes($sdk,$userId);
            $this->data['args'] = $request->query->all();
            $this->data['page']['title'] .= ' &rsaquo; Leave Requests';
            $this->data['header']['title'] ='Create Leave Requests';
            return view('modules-people-leaves::Requests/create', $this->data);
        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return redirect()->route('leave-request-main');

        }

    }

    public function createLeaveRequest(Request $request, Sdk $sdk){
        try{
            $resource = $sdk->createLeavesResource();
            $resource = $resource->addBodyParam('count_requesting',$request->count_requesting)
                ->addBodyParam('data_start_date',$request->data_start_date)
                ->addBodyParam('data_contact_address',$request->data_contact_address)
                ->addBodyParam('data_contact_phone',$request->data_contact_phone)
                ->addBodyParam('data_backup_staff',$request->data_backup_staff)
                ->addBodyParam('data_remarks',$request->data_remarks)
                ->addBodyParam('user_id',auth()->user()->id)
                ->addBodyParam('type_id',$request->type_id);
            $response = $resource->send('post',['requests']);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                $error = (tabler_ui_html_response(['Failed while adding the Leave Request  '.$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('leave-request-create')->with('UiResponse', $error);

            }
            $message = (tabler_ui_html_response(['Leave Request Created Successfully']))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-request-main')->with('UiResponse', $message);


        }
        catch (\Exception $e){
            $message = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-request-main')->with('UiResponse', $message);


        }
    }

    public function searchLeaveRequest(Request $request, Sdk $sdk){
        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);

        # get the request parameters
        $path = ['requests'];

        $query = $sdk->createLeavesResource();
        $query = $query
            ->addQueryArgument('limit', $limit)
            ->addQueryArgument('user_id', auth()->user()->id)
            ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching Leave Requests.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function singleLeaveRequest(Request $request, Sdk $sdk, string $id){
        try {


            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            $this->data['args'] = $request->query->all();
            $response = $sdk->createLeavesResource()->send('get', ['requests', $id]);
            if (!$response->isSuccessful()) {
                $response = (tabler_ui_html_response([$response->errors[0]['title'] ?? 'Could not find the Leave Request']))->setType(UiResponse::TYPE_ERROR);
                return Redirect::back()->with('UiResponse', $response)->with('UiResponse',$response);
            }
            $leaveRequest = $response->getData(true);
            $this->data['page']['title'] .= ' &rsaquo; Authorizer';
            $this->data['header']['title'] = ' Leave Group Update';
            $this->data['submenuAction'] = '';
            $this->setViewUiResponse($request);
            $userID = auth()->user()->id;
            if ($this->getLeaveTypes($sdk,$userID) === null){
                $error = (tabler_ui_html_response(['Failed no Leave Configurations found for you ']))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('leave-request-main')->with('UiResponse', $error);
            }
            $this->data['leaveTypes'] = $this->getLeaveTypes($sdk,$userID);
            $this->data['leaveRequest'] = $leaveRequest;
            return view('modules-people-leaves::Requests/update', $this->data);
        }
        catch (\Exception $e){
            $this->setViewUiResponse($request);
            return Redirect::back();


        }
    }

    public function updateLeaveRequest(Request $request, Sdk $sdk, string $id){
        try{
            $resource = $sdk->createLeavesResource();
            $resource = $resource->addBodyParam('count_requesting',$request->count_requesting)
                ->addBodyParam('data_start_date',$request->data_start_date)
                ->addBodyParam('data_contact_address',$request->data_contact_address)
                ->addBodyParam('data_contact_phone',$request->data_contact_phone)
                ->addBodyParam('data_backup_staff',$request->data_backup_staff)
                ->addBodyParam('data_remarks',$request->data_remarks)
                ->addBodyParam('user_id',auth()->user()->id)
                ->addBodyParam('type_id',$request->type_id);
            $response = $resource->send('put',['requests',$id]);
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                $error = (tabler_ui_html_response(['Failed while updating the Leave Request  '.$message]))->setType(UiResponse::TYPE_ERROR);
                return redirect()->route('leave-request-single',['id'=>$id])->with('UiResponse', $error);

            }
            $message = (tabler_ui_html_response(['Leave Request Created Successfully']))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-request-main')->with('UiResponse', $message);


        }
        catch (\Exception $e){
            $message = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_SUCCESS);
            return redirect()->route('leave-request-main')->with('UiResponse', $message);


        }
    }

    public function deleteLeaveRequest(Request $request, Sdk $sdk, string $id){
        try{
            $resource = $sdk->createLeavesResource();
            $response = $resource->send('delete', ['requests',$id]);
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the Leave Request.');

            }
            $this->data = $response->getData();
            return response()->json($this->data);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }



}