<?php

namespace App\Livewire\App;

use App\Jobs\SendWebPushNotifications;
use App\Models\Conversation;
use App\Models\Setup;
use App\Models\User;
use App\Notifications\UserApproved;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use misterspelik\LaravelPdf\Facades\Pdf;

class UserComponent extends Component
{

    use WithPagination;
    use LivewireAlert;
    public $selectedRows = [];
    public $selectPageRows = false;
    public $itemPerPage=20;
    public $orderBy = 'id';
    public $orderDirection = 'asc';
    public $searchBy = 'name';
    public $search = '';
    public $itemStatus;
    protected $queryString = [
        'search' => ['except' => ''],
        'itemStatus' => ['except' => null],
    ];

    public $user;
    public $name,$email,  $phone, $address, $bio, $status='active', $type, $facebook, $twitter, $instagram, $password, $confirmPassword;
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedItemPerPage()
    {
        $this->resetPage();
    }
    public function updatedSelectPageRows($value)
    {
        if ($value) {
            $this->selectedRows = $this->data->pluck('id')->map(function ($id) {
                return (string) $id;
            });
        } else {
            $this->reset('selectedRows', 'selectPageRows');
        }
    }
    public function changeStatus(User $user)
    {
                $this->authorize('app.users.edit');

        $user->status=='active'?$user->update(['status'=>'inactive']):$user->update(['status'=>'active']);

        $authUser = auth()->user();
        $message = "Your account's status has been changed ";
        $link = route('app.user.detail', $user);
        $model = User::find($user->id);
        $user->notify(new UserApproved($authUser, $model, $message, $link));

        $this->alert('success', __('Data updated successfully'));
    }
    public function resetData()
    {
        $this->reset('name', 'email', 'phone', 'bio', 'address', 'status', 'type', 'facebook', 'twitter', 'instagram', 'password', 'confirmPassword');
    }

    public function saveData()
    {
        $this->authorize('app.users.create');

        $data = $this->validate([
            'name' => ['required', 'min:2', 'max:33'],
            'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            'status' => ['required'],
            'type' => ['required'],
            'bio' => ['nullable', 'min:10'],
            'address' => ['nullable', 'min:10'],
            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable',  'url'],
            'instagram' => ['nullable', 'url'],
            'password' => ['required', 'min:8', 'same:confirmPassword'],
            'email' => ['required', 'min:2', 'max:44', Rule::unique('users', 'email')]
        ]);
        $data['password'] = Hash::make($this->password);

        $data = User::create($data);
        $var = $data->id;
        $this->dispatch('dataAdded', dataId: "item-id-$var");
        $this->goToPage($this->getDataProperty()->lastPage());
        $this->alert('success', __('Data updated successfully'));
        $this->resetData();

    }
    public function loadData(User $user)
    {
        $this->resetData();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->bio = $user->bio;
        $this->address = $user->address;
        $this->status = $user->status;
        $this->type = $user->role_id;
        $this->facebook = $user->facebook;
        $this->instagram = $user->instagram;
        $this->twitter = $user->twitter;
        $this->user = $user;
    }
    public function editData()
    {
        $this->authorize('app.users.edit');

        $data = $this->validate([
            'name' => ['required', 'min:2', 'max:33'],
            'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            'status' => ['required'],
            'type' => ['required'],
            'bio' => ['nullable', 'min:10'],
            'address' => ['nullable', 'min:10'],
            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable',  'url'],
            'instagram' => ['nullable', 'url'],
            'password' => ['nullable', 'min:8', 'same:confirmPassword'],
            'email' => ['required', 'min:2', 'max:44', Rule::unique('users', 'email')->ignore($this->user['id'])]
        ]);
        $data['password'] = $data['password']==null? $this->user->password:Hash::make($data['password']);
        $data['role_id'] = $data['type'];

        $this->user->update($data);
        $var = $this->user->id;
        $this->dispatch('dataAdded', dataId: "item-id-$var");
        $this->alert('success', __('Data updated successfully'));
        $this->resetData();
    }
    public function getDataProperty()
    {
        return User::where($this->searchBy, 'like', '%'.$this->search.'%')
            ->orderBy($this->orderBy, $this->orderDirection)
            ->when($this->itemStatus, function ($query) {
                return $query->where('status', $this->itemStatus);
            })
            ->paginate($this->itemPerPage)->withQueryString();
    }
    public function generate_pdf()
    {
        return response()->streamDownload(function () {
            $items= $this->data;
            $pdf = Pdf::loadView('pdf.users', compact('items'));
            return $pdf->stream('users.pdf');
        }, 'users.pdf');
    }
    public function deleteMultiple()
    {
        $this->authorize('app.users.delete');

        User::whereIn('id', $this->selectedRows)->where('role_id', '!=', 1)->delete();
        $this->selectPageRows = false;
        $this->selectedRows = [];
        $this->alert('success', __('Data deleted successfully'));
    }
    public function deleteSingle(User $user)
    {
        $this->authorize('app.users.delete');

        $user->delete();
        $this->alert('success', __('Data deleted successfully'));
    }
    public function orderByDirection($field)
    {
        if ($this->orderBy == $field){

            $this->orderDirection==='asc'? $this->orderDirection='desc': $this->orderDirection='asc';
        }else{
            $this->orderBy = $field;
            $this->orderDirection==='asc';

        }
    }
    public function pdfGenerate()
    {
        return response()->streamDownload(function () {
            $items= $this->data;
//            $setup = Setup::first();
            $pdf = Pdf::loadView('pdf.users', compact('items'));
            return $pdf->stream('users.pdf');
        }, 'users.pdf');
    }
    public function createConversation($receiverId)
    {
        $checkedConversation = Conversation::where('receiver_id', auth()->user()->id)->where('sender_id', $receiverId)->orWhere('receiver_id', $receiverId)->where('sender_id', auth()->user()->id)->first();
        if ($checkedConversation) {
            $checkedConversation->update(['last_time_message'=> now()]);
        } else {
            Conversation::create(['receiver_id'=>$receiverId,'sender_id'=>auth()->user()->id,'last_time_message'=>now()]);
            $this->dispatch('receiveOpponentId', $receiverId);
        }
        return redirect()->route('app.chat');
    }
    public function render()
    {
        $this->authorize('app.users.index');
        $items = $this->data;

        return view('livewire.app.user-component', compact('items'));
    }
}
