<?php

namespace App\Livewire\App;

use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation;


    public $photo;
    public $image_url;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $bio;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'bio' => 'nullable|string|max:500',
    ];
    public function updatePhoto()
    {
        $this->authorize('app.profile.edit');

        $this->validate([
            'photo' => 'nullable|image|max:2048', // 2MB Max
            'image_url' => 'nullable|url', // 2MB Max
        ]);

        $user = auth()->user();

        if ($this->image_url!=null) {
            $extension = pathinfo(parse_url($this->image_url, PHP_URL_PATH), PATHINFO_EXTENSION);
          $media =  $user->addMediaFromUrl($this->image_url)->usingFileName($user->name. '.' . $extension)->toMediaCollection('profile');
          $path = storage_path("app/public/".$media->id.'/'. $media->file_name);
            if (file_exists($path)) {
                unlink($path);
            }

            $this->alert('success', __('Profile photo updated successfully.'));

        }elseif($this->photo!=null){
           $media = $user->addMedia($this->photo->getRealPath())->usingFileName($user->name. '.' . $this->photo->extension())->toMediaCollection('profile');
            $path = storage_path("app/public/".$media->id.'/'. $media->file_name);
            if (file_exists($path)) {
                unlink($path);
            }
            $this->alert('success', __('Profile photo updated successfully.'));
        }
        $this->reset('image_url', 'photo');

    }
    public function updatePassword()
    {
        $this->authorize('app.profile.edit');

        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|same:newPasswordConfirmation|min:8',
        ]);

        if (!\Hash::check($this->currentPassword, auth()->user()->password)) {
            $this->alert('error', __('The current password is incorrect.'));
           return;
        }

        auth()->user()->update([
            'password' => \Hash::make($this->newPassword),
        ]);
        $this->alert('success', __('Password updated successfully.'));
    }
    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->bio = $user->bio;
    }
    public function updateProfile()
    {
        $this->authorize('app.profile.edit');

        $this->validate();

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'bio' => $this->bio,
        ]);
        $this->alert('success', __('Profile information updated successfully.'));
    }
    public function render()
    {
        $this->authorize('app.profile.index');

        $item = auth()->user();

        return view('livewire.app.profile-component', compact('item'))->layout('components.layouts.web');
    }
}
