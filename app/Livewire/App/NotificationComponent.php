<?php

namespace App\Livewire\App;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationComponent extends Component
{
    use LivewireAlert;
    use WithPagination;

//    public $notifications;
    public $filter = 'unread'; // default filter is 'all'
    public $type = ''; // default filter is 'all'
    public $activity = 'notification'; // default filter is 'all'


    public function markAsRead($notificationId)
    {
        $this->authorize('app.notifications.edit');

        $notification = DatabaseNotification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->alert('success', __('Data was mark as read successfully'));
        }
    }
    public function markAllAsRead()
    {
        $this->authorize('app.notifications.edit');

        $this->data->get()->markAsRead(); // Mark all unread notifications as read
        $this->alert('success', __('All data was mark as read successfully'));
    }
    public function deleteAll()
    {
        $this->authorize('app.notifications.delete');

        $this->data->delete(); // Mark all unread notifications as read
        $this->alert('success', __('All data was deleted successfully'));
    }
    public function deleteNotification($notificationId)
    {
        $this->authorize('app.notifications.delete');

        $notification = DatabaseNotification::find($notificationId);

        if ($notification) {
            $notification->delete();
            $this->alert('success', __('Data deleted successfully'));
        }
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $notifications = $this->data;
//        dd($this->filter);
    }
    public function getDataProperty()
    {
        if($this->activity == 'notification'){
            return auth()->user()->notifications()->where('type','!=','App\Notifications\ModelUpdateNotification')
                ->when($this->filter==='read', function ($query) {
                    return $query->where('read_at', '!=', null);
                })->when($this->filter==='unread', function ($query) {
                    return $query->where('read_at',  null);
                })->when($this->type, function ($query) {
                    return $query->where('data->type', $this->type);
                });
        }elseif($this->activity == 'myActivity'){
            return auth()->user()->notifications()->whereType('App\Notifications\ModelUpdateNotification')
                ->when($this->filter==='read', function ($query) {
                    return $query->where('read_at', '!=', null);
                })->when($this->filter==='unread', function ($query) {
                    return $query->where('read_at',  null);
                })->when($this->type, function ($query) {
                    return $query->where('data->type', $this->type);
                });
        }elseif($this->activity == 'allActivity'){
            return auth()->user()->role->notifications()->whereType('App\Notifications\ModelUpdateNotification')
                ->when($this->filter==='read', function ($query) {
                    return $query->where('read_at', '!=', null);
                })->when($this->filter==='unread', function ($query) {
                    return $query->where('read_at',  null);
                })->when($this->type, function ($query) {
                    return $query->where('data->type', $this->type);
                });
        }


    }
    public function render()
    {
        $this->authorize('app.notifications.index');

        $notifications = $this->data->paginate(10);

//        dd($notifications);
        return view('livewire.app.notification-component', compact('notifications'))->layout('components.layouts.web');
    }
}
